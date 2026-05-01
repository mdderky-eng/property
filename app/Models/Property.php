<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    // السماح بإدخال البيانات لهذه الحقول
    protected $fillable = [
        'user_id', 'location_id', 'title', 'description',
        'price', 'area', 'rooms_count', 'property_type',
        'ownership_type', 'offer_type'
    ];

    // علاقة العقار بالصور (عقار واحد له صور كثيرة)
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    // علاقة العقار بالمنطقة (عقار يتبع منطقة واحدة)
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // علاقة العقار بالمستخدم (عقار يملكه مستخدم واحد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
