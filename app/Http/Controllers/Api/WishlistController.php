<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{

    //  عرض جميع العقارات الموجودة في مفضلة الزبون الحالي

    public function index(): JsonResponse
    {
        // جلب العقارات التي تنتمي لمفضلة المستخدم الحالي مع ترتيبها من الأحدث إضافة
        $favorites = auth()->user()->favoriteProperties()->latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $favorites->count(),
            'wishlist' => $favorites
        ], 200);
    }

    //  إضافة أو إزالة عقار من المفضلة (Toggle)
    public function toggleFavorite(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $user = auth()->user();


        $result = $user->favoriteProperties()->toggle($request->property_id);

        // فحص النتيجة لمعرفة هل تم الـ attach (إضافة) أو detach (إزالة)
        if (count($result['attached']) > 0) {
            $message = 'تم إضافة العقار إلى المفضلة بنجاح.';
            $isFavorite = true;
        } else {
            $message = 'تم إزالة العقار من المفضلة بنجاح.';
            $isFavorite = false;
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'is_favorite' => $isFavorite
        ], 200);
    }
}
