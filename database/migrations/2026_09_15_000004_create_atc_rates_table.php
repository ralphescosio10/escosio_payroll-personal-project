<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atc_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('atc_rates')->insert([
            ['code' => 'A010', 'description' => 'Compensation Income', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A020', 'description' => 'Employment / Professional', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A030', 'description' => '13th Month Pay', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A040', 'description' => 'Other Benefits', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A050', 'description' => 'Fringe Benefits', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A060', 'description' => 'Holiday Pay', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A070', 'description' => 'Overtime Pay', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A080', 'description' => 'Commission', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A090', 'description' => 'Allowance', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A100', 'description' => 'Retainer / Fee', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A110', 'description' => 'Taxable Bonus', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A120', 'description' => 'Special Pay', 'tax_rate' => 0.0000, 'active' => true],
            ['code' => 'A130', 'description' => 'Salary Adjustment', 'tax_rate' => 0.0000, 'active' => true],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('atc_rates');
    }
};
