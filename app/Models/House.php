<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasUuids;

    protected $fillable = [
        'house_code',
        'is_occupied',
    ];

    protected function casts(): array
    {
        return [
            'is_occupied' => 'boolean',
        ];
    }

    public function houseResidents()
    {
        return $this->hasMany(HouseResident::class);
    }

    public function currentResident()
    {
        return $this->houseResidents()->whereNull('end_date');
    }
}
