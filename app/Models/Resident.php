<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    /** @use HasFactory<\Database\Factories\ResidentFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'fullname',
        'indentity_card_url',
        'is_permanent_resident',
        'phone_number',
        'is_married'
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
