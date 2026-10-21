<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class BackupSqliteDatabase extends Command
{
    protected $signature = 'app:backup-database';

    protected $description = 'Create a SQLite database backup and remove backups older than 30 days';

    public function handle(): int
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'sqlite') {
            $this->error('Database backups are currently supported only for SQLite.');

            return self::FAILURE;
        }

        $databasePath = $connection->getDatabaseName();
        if ($databasePath === ':memory:' || ! is_file($databasePath)) {
            $this->error('The configured SQLite database file does not exist or is in-memory.');

            return self::FAILURE;
        }

        $backupDirectory = config('database.backup_path', storage_path('app/private/backups'));
        File::ensureDirectoryExists($backupDirectory);

        $backupPath = $backupDirectory.DIRECTORY_SEPARATOR.'payroll-'.now()->format('YmdHis').'.sqlite';
        $quotedBackupPath = $connection->getPdo()->quote($backupPath);

        try {
            $connection->statement('VACUUM INTO '.$quotedBackupPath);
        } catch (Throwable $exception) {
            report($exception);
            $this->error('SQLite backup failed. See the application log for details.');

            return self::FAILURE;
        }

        $retentionCutoff = now()->subDays(30)->getTimestamp();
        foreach (File::files($backupDirectory) as $file) {
            if (! preg_match('/^payroll-\d{14}\.sqlite$/', $file->getFilename())) {
                continue;
            }

            if ($file->getMTime() < $retentionCutoff && ! File::delete($file->getPathname())) {
                throw new RuntimeException('Could not remove an expired database backup: '.$file->getPathname());
            }
        }

        $this->info('Database backup created: '.$backupPath);

        return self::SUCCESS;
    }
}
