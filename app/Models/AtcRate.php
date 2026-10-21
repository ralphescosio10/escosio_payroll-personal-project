<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtcRate extends Model
{
    protected $table = 'atc_rates';

    protected $fillable = [
        'code',
        'description',
        'tax_rate',
        'active',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:4',
        'active' => 'boolean',
    ];
}
