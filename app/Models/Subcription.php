<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcription extends Model
{
    /** @use HasFactory<\Database\Factories\SubcriptionFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'house_id',
        'type',
        'amount',
        'period',
        'is_paid_off'
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
