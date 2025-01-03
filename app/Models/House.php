<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    /** @use HasFactory<\Database\Factories\HouseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'resident_id',
        'house_code',
        'is_occupied'
    ];

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function subcriptions(){
        return $this->hasMany(Subcription::class);
    }
}
