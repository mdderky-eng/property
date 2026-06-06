<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'client_phone',
        'appointment_date',
        'appointment_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public static function isSlotOccupied($propertyId, $date, $time, $excludeId = null)
    {
        return self::where('property_id', $propertyId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists();
    }

}
