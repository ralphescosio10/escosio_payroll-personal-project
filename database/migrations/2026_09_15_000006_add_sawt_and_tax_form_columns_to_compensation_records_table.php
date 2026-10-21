<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('compensation_records', function (Blueprint $table) {
            if (! Schema::hasColumn('compensation_records', 'tax_form')) {
                $table->string('tax_form')->default('MAP')->after('pay_period');
            }

            if (! Schema::hasColumn('compensation_records', 'amended_return')) {
                $table->string('amended_return')->nullable()->after('tax_form');
            }

            if (! Schema::hasColumn('compensation_records', 'no_of_sheets_attached')) {
                $table->integer('no_of_sheets_attached')->nullable()->after('amended_return');
            }

            if (! Schema::hasColumn('compensation_records', 'tax_relief_special_law')) {
                $table->string('tax_relief_special_law')->nullable()->after('no_of_sheets_attached');
            }

            if (! Schema::hasColumn('compensation_records', 'quarter')) {
                $table->string('quarter')->nullable()->after('tax_relief_special_law');
            }
        });

        if (! DB::table('compensation_records')->first()) {
            return;
        }

        DB::statement("UPDATE compensation_records SET tax_form = 'MAP' WHERE tax_form IS NULL OR tax_form = ''");
        DB::statement("UPDATE compensation_records SET amended_return = 'N' WHERE amended_return IS NULL");
        DB::statement("UPDATE compensation_records SET tax_relief_special_law = 'N' WHERE tax_relief_special_law IS NULL");
        DB::statement("UPDATE compensation_records SET quarter = '1Q' WHERE quarter IS NULL OR quarter = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compensation_records', function (Blueprint $table) {
            $table->dropColumn(['tax_form', 'amended_return', 'no_of_sheets_attached', 'tax_relief_special_law', 'quarter']);
        });
    }
};
