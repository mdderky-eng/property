<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    //   جلب إشعارات المستخدم الحالي (أحدث 20 إشعاراً) مع عداد للإشعارات غير المقروءة

    public function index(): JsonResponse
    {
        $notifications = auth()->user()->notifications()->latest()->take(20)->get();

        return response()->json([
            'status' => 'success',
            'unread_count' => auth()->user()->notifications()->where('is_read', false)->count(),
            'notifications' => $notifications
        ], 200);
    }


    //  تحويل إشعار محدد إلى مقروء

    public function markAsRead($id): JsonResponse
    {
        $notification = auth()->user()->notifications()->find($id);

        if (!$notification) {
            return response()->json(['message' => 'الإشعار غير موجود'], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'تم تعيين الإشعار كمقروء.'], 200);
    }
}
