<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'image_path', 'is_main'];

    // علاقة الصورة بالعقار (صورة تنتمي لعقار واحد)
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

}
