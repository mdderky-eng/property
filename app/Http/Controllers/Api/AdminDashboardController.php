<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getStats()
    {
        // ---  إحصائيات العقارات بناءً على الحالات الجديدة ---
        $totalProperties = Property::count();
        $availableCount = Property::where('status', 'available')->count();
        $reservedCount = Property::where('status', 'reserved')->count();
        $soldCount = Property::where('status', 'sold')->count();
        $rentedCount = Property::where('status', 'rented')->count();

        // ---  إحصائيات طلبات الحجز المالي (شام كاش / سيريتل كاش) ---
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $rejectedBookings = Booking::where('status', 'rejected')->count();

        // ---  إحصائيات مواعيد المعاينة ---
        $totalAppointments = Appointment::count();
        // جلب المواعيد المخصصة لتاريخ اليوم حصراً
        $todayAppointments = Appointment::whereDate('appointment_date', Carbon::today())->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $expiredAppointments = Appointment::whereDate('appointment_date', '<', Carbon::today())->count();

        // ---  دمج البيانات وإعادتها بصيغة JSON---
        return response()->json([
            'status' => 'success',
            'data' => [
                'properties' => [
                    'total' => $totalProperties,
                    'available' => $availableCount,
                    'reserved' => $reservedCount, // محجوز بعربون
                    'sold' => $soldCount,      // تم بيعه
                    'rented' => $rentedCount,    // تم إيجاره
                ],
                'bookings' => [
                    'total' => $totalBookings,
                    'pending' => $pendingBookings,  // طلبات حجز جديدة تحتاج تدقيق مالي
                    'approved' => $approvedBookings, // طلبات حجز مؤكدة ومقبولة
                    'rejected' => $rejectedBookings, // طلبات حجز مرفوضة أو بها مشكلة
                ],
                'appointments' => [
                    'total' => $totalAppointments,
                    'today' => $todayAppointments,   // مواعيد المعاينة المخططة لليوم
                    'pending' => $pendingAppointments, // مواعيد بانتظار تأكيد الأدمن
                    'expired' => $expiredAppointments, // مواعيد منتهية الصلاحية
                ]
            ]
        ], 200);
    }
}
