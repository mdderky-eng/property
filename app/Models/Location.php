<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function parent()
    {
        // العلاقة التي تجلب المحافظة التابع لها الحي
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        // العلاقة التي تجلب الأحياء التابعة للمحافظة
        return $this->hasMany(Location::class, 'parent_id');
    }
}
