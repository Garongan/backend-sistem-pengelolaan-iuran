<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasUuids;

    protected $fillable = [
        'description',
        'amount',
        'date'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
