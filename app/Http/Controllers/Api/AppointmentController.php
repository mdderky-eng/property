<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {

        $property = Property::findOrFail($request->property_id);
        if ($property->status !== 'available') {
            return response()->json([
                'message' => 'عذراً، هذا العقار غير متاح للمعاينات حالياً (حالة العقار: ' . $property->status . '). يرجى اختيار عقار آخر أو التواصل مع المكتب لمزيد من التفاصيل.'
            ], 422);
        }


        // البيانات هنا مفلترة وجاهزة
        $validated = $request->validated();

        // استخدام الـ Method الموجدة في الموديل لمنع التكرار
        if (Appointment::isSlotOccupied($validated['property_id'], $validated['appointment_date'], $validated['appointment_time'])) {
            return response()->json([
                'message' => 'عذراً، هذا الموعد محجوز مسبقاً، يرجى اختيار ساعة أخرى.'
            ], 409);
        }

        // حفظ الموعد
        $appointment = Appointment::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'status' => 'confirmed' // أو 'pending' حسب سياسة المكتب
        ]));

        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::send(
                $admin->id,
                'طلب حجز جديد',
                "قام الزبون {$appointment->user->name} بإرسال طلب حجز للعقار رقم {$appointment->property_id}."
            );
        }

        return response()->json([
            'message' => 'تم تسجيل طلب الموعد بنجاح.',
            'appointment' => $appointment
        ], 201);

    }

    public function update(UpdateAppointmentRequest $request, $id)
    {

        // $appointment = Appointment::findOrFail($id);

        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json(['message' => 'عذراً، هذا الموعد غير موجود.'], 404);
        }
        //  التحقق من الملكية (صاحب الموعد فقط هو من يعدله)
        if ($appointment->user_id !== auth()->id()) {
            return response()->json(['message' => 'عذراً، لا تملك صلاحية تعديل هذا الموعد.'], 403);
        }

        //  جلب البيانات التي تم التحقق منها فقط
        $validated = $request->validated();

        //  فحص التعارض  لجلب القيم القديمة إذا لم يرسل المستخدم قيماً جديدة للتعديل
        $date = $validated['appointment_date'] ?? $appointment->appointment_date;
        $time = $validated['appointment_time'] ?? $appointment->appointment_time;
        $propertyId = $appointment->property_id;

        if (Appointment::isSlotOccupied($propertyId, $date, $time, $id)) {
            return response()->json([
                'message' => 'عذراً، الوقت الجديد الذي اخترته محجوز مسبقاً.'
            ], 409);
        }

        // تحديث البيانات
        $appointment->update($validated);

        return response()->json([
            'message' => 'تم تحديث الموعد بنجاح.',
            'appointment' => $appointment->refresh() // تحديث البيانات لإعادتها لغزل
        ]);
    }
    public function destroy($id)
    {
        //  البحث عن الموعد
        $appointment = Appointment::find($id);

        //  إذا لم يوجد الموعد (رسالة بسيطة كما طلبت سابقاً)
        if (!$appointment) {
            return response()->json(['message' => 'عذراً، هذا الموعد غير موجود.'], 404);
        }

        //  فحص الملكية
        if ($appointment->user_id !== auth()->id()) {
            return response()->json(['message' => 'عذراً، لا تملك صلاحية إلغاء هذا الموعد.'], 403);
        }

        //  الحذف الفعلي
        $appointment->delete();

        return response()->json(['message' => 'تم إلغاء الموعد بنجاح.']);
    }


    public function getBookedSlots(Request $request, $propertyId)
    {
        $date = $request->query('date', date('Y-m-d'));

        $bookedSlots = Appointment::where('property_id', $propertyId)
            ->where('appointment_date', $date)
            ->pluck('appointment_time'); // سيجلب فقط الأوقات المحجوزة

        return response()->json([
            'booked_slots' => $bookedSlots
        ]);
    }

    // للأدمن: عرض كل المواعيد القادمة
    public function index()
    {
        // نجلب المواعيد مع بيانات المستخدم والعقار المرتبط به
        $appointments = Appointment::with(['user', 'property'])->latest()->get();
        return response()->json($appointments);
    }



    public function myAppointments()
    {
        $appointments = Appointment::query()
            ->where('user_id', auth()->id())
            ->with('property')
            ->latest()
            ->get();

        return response()->json($appointments);
    }
}
