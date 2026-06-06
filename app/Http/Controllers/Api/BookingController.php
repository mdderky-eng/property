<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        //  التحقق من البيانات والملفات
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'transaction_number' => 'required|string|unique:bookings,transaction_number', // لمنع إعادة استخدام نفس الإشعار
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // صورة الإشعار
            'client_note' => 'nullable|string|max:500',
        ]);

        $property = Property::findOrFail($request->property_id);

        //  التأكد أن العقار متاح
        if ($property->status !== 'available') {
            return response()->json(['message' => 'عذراً، هذا العقار غير متاح للحجز حالياً.'], 422);
        }

        //  رفع صورة الإشعار إلى السيرفر
        $imagePath = null;
        if ($request->hasFile('receipt_image')) {
            // تخزين الصورة في مجلد الـ receipts داخل الـ storage
            $imagePath = $request->file('receipt_image')->store('receipts', 'public');
        }

        //  إنشاء طلب الحجز
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id,
            'transaction_number' => $request->transaction_number,
            'receipt_image' => $imagePath,
            'client_note' => $request->client_note,
        ]);
        // اشعار لل admin عند وصول طلب حجز جديد
        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::send(
                $admin->id,
                'طلب حجز جديد',
                "قام الزبون {$booking->user->name} بإرسال طلب حجز للعقار رقم {$booking->property_id}."
            );
        }

        return response()->json([
            'message' => 'تم إرسال طلب الحجز وإشعار الدفع بنجاح. يتم الآن التدقيق من قبل إدارة المكتب.',
            'booking' => $booking
        ], 201);




    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $booking = Booking::with('property')->findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'هذا الطلب تم البت فيه مسبقاً.'], 422);
        }

        if ($request->status === 'approved') {
            // التأكد من أن العقار لم يشتره أو يحجزه شخص آخر
            if ($booking->property->status !== 'available') {
                return response()->json(['message' => 'عذراً، هذا العقار لم يعد متاحاً للقبول.'], 422);
            }

            // قبول الطلب الحالي وإرسال إشعاره
            $booking->status = 'approved';
            $booking->save();

            \App\Models\Notification::send(
                $booking->user_id,
                'تم قبول طلب الحجز',
                "تهانينا، تم قبول طلب حجزك للعقار رقم {$booking->property_id} بنجاح."
            );

            // تحديث حالة العقار تلقائياً
            $booking->property->update(['status' => 'reserved']);

            // ---  جلب بقية الطلبات المعلقة لنفس العقار  ---
            $otherPendingBookings = Booking::where('property_id', $booking->property_id)
                ->where('status', 'pending')
                ->where('id', '!=', $id)
                ->get();

            // المرور على كل طلب مرفوض تلقائياً لإرسال إشعاره الخاص
            foreach ($otherPendingBookings as $rejectedBooking) {
                //  تحديث الحالة في قاعدة البيانات وحفظها
                $rejectedBooking->status = 'rejected';
                $rejectedBooking->save();

                //  إرسال الإشعار لصاحب هذا الطلب المرفوض
                \App\Models\Notification::send(
                    $rejectedBooking->user_id,
                    'تحديث بشأن طلب الحجز',
                    "عذراً، تم رفض طلب حجزك الآخر للعقار رقم {$rejectedBooking->property_id} تلقائياً بسبب قبول طلب حجز آخر لنفس العقار."
                );
            }

        } else {
            // كود الرفض اليدوي من الأدمن
            $booking->status = 'rejected';
            $booking->save();

            \App\Models\Notification::send(
                $booking->user_id,
                'تحديث بشأن طلب الحجز',
                "عذراً، تم رفض طلب حجزك للعقار رقم {$booking->property_id}."
            );
        }
        return response()->json([
            'message' => 'تم تحديث حالة طلب الحجز والتحقق المالي بنجاح إلى ' . __($request->status),
            'booking' => $booking
        ]);



    }
}
