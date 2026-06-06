<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    /**
     * 1. تحديث البيانات الشخصية للزبون
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        // تحديث البيانات بحفظ مباشر على الكائن لضمان الأمان
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'message' => 'تم تحديث بيانات البروفايل بنجاح.',
            'user' => $user
        ], 200);
    }

    /**
     * 2. تحديث كلمة المرور من داخل البروفايل
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], //  اللتأكد أن كلمة السر القديمة صحيحة
            'password' => ['required', 'confirmed', Password::defaults()], // كلمة السر الجديدة وتأكيدها
        ]);

        $user = auth()->user();

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح.'
        ], 200);
    }


    public function myAppointments()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('property') // لجلب تفاصيل العقار المراد معاينته
            ->latest() // ترتيب من الأحدث إلى الأقدم
            ->get();

        return response()->json([
            'status' => 'success',
            'appointments' => $appointments
        ], 200);
    }

    //   جلب جميع طلبات الحجز المالي الخاصة بالزبون الحالي فقط

    public function myBookings()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('property') // لجلب تفاصيل العقار المحجوز
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'bookings' => $bookings
        ], 200);
    }
}
