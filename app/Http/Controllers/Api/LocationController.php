<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getGovernorates()
    {
        // جلب المحافظات (التي ليس لها أب) مع تحميل الأبناء والأحفاد
        $governorates = Location::whereNull('parent_id')
            ->with('children.children')
            ->get();
        return response()->json($governorates, 200, [], JSON_UNESCAPED_UNICODE);
    }

    // جلب الأحياء التابعة لمحافظة معينة
    public function getDistricts($parentId)
    {
        // جلب المنطقة مع كافة فروعها المتداخلة
        $location = Location::with('children.children')->find($parentId);

        if (!$location) {
            return response()->json(['message' => 'المنطقة غير موجودة'], 404);
        }

        return response()->json($location, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
