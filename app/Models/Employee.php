<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'tin',
        'first_name',
        'last_name',
        'middle_name',
        'employment_status',
        'monthly_salary',
        'address',
        'rdo',
        'tax_year',
        'created_by',
    ];

    public function compensationRecords(): HasMany
    {
        return $this->hasMany(CompensationRecord::class);
    }

    protected function normalizeName(string $value): string
    {
        $value = trim($value);

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public function setFirstNameAttribute(string $value): void
    {
        $this->attributes['first_name'] = $this->normalizeName($value);
    }

    public function setMiddleNameAttribute(?string $value): void
    {
        $this->attributes['middle_name'] = $value !== null ? $this->normalizeName($value) : null;
    }

    public function setLastNameAttribute(string $value): void
    {
        $this->attributes['last_name'] = $this->normalizeName($value);
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }
}
