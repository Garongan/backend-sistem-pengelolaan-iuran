<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HouseResident extends Model
{
    use HasUuids;

    protected $fillable = [
        'house_id',
        'resident_id',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'house_id',
        'resident_id',
        'created_at',
        'updated_at'
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
