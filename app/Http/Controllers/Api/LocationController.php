<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getGovernorates()
    {
        $governorates = Location::whereNull('parent_id')->get();
        return response()->json($governorates);
    }

    // دالة لجلب الأحياء التابعة لمحافظة معينة
    public function getDistricts($parentId)
    {
        $districts = Location::where('parent_id', $parentId)->get();
        return response()->json($districts);
    }
}
