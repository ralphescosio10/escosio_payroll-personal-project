<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compensation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('tax_year')->default(now()->year);
            $table->string('pay_period');
            $table->string('tax_form')->default('MAP');
            $table->string('atc_code')->default('A010');
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('gross_compensation', 12, 2)->default(0);
            $table->decimal('non_taxable_compensation', 12, 2)->default(0);
            $table->decimal('government_contributions', 12, 2)->default(0);
            $table->decimal('taxable_compensation', 12, 2)->default(0);
            $table->decimal('tax_withheld', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'pay_period', 'tax_form']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compensation_records');
    }
};
