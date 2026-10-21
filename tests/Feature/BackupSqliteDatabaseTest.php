<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use Tests\TestCase;

class BackupSqliteDatabaseTest extends TestCase
{
    public function test_it_creates_a_sqlite_snapshot_and_prunes_snapshots_older_than_thirty_days(): void
    {
        $testId = bin2hex(random_bytes(8));
        $databasePath = storage_path('framework/testing/database-'.$testId.'.sqlite');
        $backupDirectory = storage_path('framework/testing/backups-'.$testId);
        $expiredBackupPath = $backupDirectory.DIRECTORY_SEPARATOR.'payroll-20000101000000.sqlite';
        $unrelatedFilePath = $backupDirectory.DIRECTORY_SEPARATOR.'keep.txt';

        File::ensureDirectoryExists(dirname($databasePath));
        File::ensureDirectoryExists($backupDirectory);
        File::put($databasePath, '');
        File::put($expiredBackupPath, 'expired');
        File::put($unrelatedFilePath, 'keep');
        touch($expiredBackupPath, now()->subDays(31)->getTimestamp());

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $databasePath,
            'database.backup_path' => $backupDirectory,
        ]);
        DB::purge('sqlite');

        try {
            DB::connection('sqlite')->statement('CREATE TABLE backup_probe (id INTEGER PRIMARY KEY)');
            DB::connection('sqlite')->table('backup_probe')->insert(['id' => 1]);

            $this->assertSame(0, Artisan::call('app:backup-database'));
            $snapshots = collect(File::files($backupDirectory))
                ->filter(fn ($file): bool => str_ends_with($file->getFilename(), '.sqlite'))
                ->values();

            $this->assertCount(1, $snapshots);
            $this->assertFileDoesNotExist($expiredBackupPath);
            $this->assertFileExists($unrelatedFilePath);

            $snapshot = new PDO('sqlite:'.$snapshots->first()->getPathname());
            $this->assertSame('ok', $snapshot->query('PRAGMA integrity_check')->fetchColumn());
            $this->assertSame(1, $snapshot->query('SELECT COUNT(*) FROM backup_probe')->fetchColumn());
        } finally {
            DB::purge('sqlite');
            File::deleteDirectory($backupDirectory);
            File::delete($databasePath);
        }
    }

    public function test_it_rejects_an_in_memory_database(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        $this->assertSame(1, Artisan::call('app:backup-database'));
    }
}
