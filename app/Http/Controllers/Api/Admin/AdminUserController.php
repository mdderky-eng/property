<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    /**
     * جلب قائمة جميع المستخدمين المسجلين في النظام (زبائن وأدمن)
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // جلب المستخدمين وترتيبهم من الأحدث تسجيلاً
        $users = User::latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'users' => $users
        ], 200);
    }

    /**
     * جلب بيانات مستخدم واحد محدد بالتفصيل مع أرشيفه
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // البحث عن المستخدم مع جلب مواعيده وحجوزاته المالية المرتبطة به في نفس الوقت
        $user = User::with(['appointments.property', 'bookings.property'])->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، هذا المستخدم غير موجود في النظام.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 200);
    }
}
