<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('compensation_records', function (Blueprint $table) {
            if (! Schema::hasColumn('compensation_records', 'atc_code')) {
                $table->string('atc_code')->default('A010')->after('pay_period');
            }

            if (! Schema::hasColumn('compensation_records', 'tax_rate')) {
                $table->decimal('tax_rate', 8, 4)->default(0)->after('atc_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compensation_records', function (Blueprint $table) {
            if (Schema::hasColumn('compensation_records', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }

            if (Schema::hasColumn('compensation_records', 'atc_code')) {
                $table->dropColumn('atc_code');
            }
        });
    }
};
