<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasUuids;

    protected $fillable = [
        'fullname',
        'indentity_card_url',
        'is_permanent_resident',
        'phone_number',
        'is_married'
    ];

    protected function casts(): array
    {
        return [
            'is_permanent_resident' => 'boolean',
            'is_married' => 'boolean',
        ];
    }

    public function houseResidents()
    {
        return $this->hasMany(HouseResident::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
