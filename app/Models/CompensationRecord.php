<?php

namespace App\Models;

use App\TaxFormCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'tax_year',
        'pay_period',
        'tax_form',
        'amended_return',
        'no_of_sheets_attached',
        'tax_relief_special_law',
        'quarter',
        'atc_code',
        'tax_rate',
        'gross_compensation',
        'non_taxable_compensation',
        'government_contributions',
        'taxable_compensation',
        'tax_withheld',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:4',
        'gross_compensation' => 'decimal:2',
        'non_taxable_compensation' => 'decimal:2',
        'government_contributions' => 'decimal:2',
        'taxable_compensation' => 'decimal:2',
        'tax_withheld' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTaxFormLabelAttribute(): string
    {
        return TaxFormCatalog::label($this->tax_form);
    }
}
