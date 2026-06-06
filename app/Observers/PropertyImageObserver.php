<?php

namespace App\Observers;

use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

class PropertyImageObserver
{
    /**
     * Handle the PropertyImage "created" event.
     */
    public function created(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "updated" event.
     */
    public function updated(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "deleted" event.
     */
    public function deleted(PropertyImage $propertyImage): void
    {
        if ($propertyImage->image_path && Storage::disk('public')->exists($propertyImage->image_path)) {
            // حذف الملف الفيزيائي
            Storage::disk('public')->delete($propertyImage->image_path);
        }
    }

    /**
     * Handle the PropertyImage "restored" event.
     */
    public function restored(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "force deleted" event.
     */
    public function forceDeleted(PropertyImage $propertyImage): void
    {
        //
    }
}
