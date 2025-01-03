<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'resident_id',
        'payment_type',
        'amount',
        'period',
        'is_paid_off'
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
