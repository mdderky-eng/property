<?php

use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\WishlistController;

Route::get('/governorates', [LocationController::class, 'getGovernorates'])->name('api.governorates.index');
Route::get('/governorates/{id}/districts', [LocationController::class, 'getDistricts'])->name('api.districts.show');


Route::get('/properties', [PropertyController::class, 'index'])->name('api.properties.index');
Route::get('/properties/filter-options', [PropertyController::class, 'filterOptions'])->name('api.properties.filter-options');
Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('api.properties.show');
Route::post('/properties/compare', [PropertyController::class, 'compare'])->name('api.properties.compare');

Route::name('api.')->group(function () {
    require __DIR__ . '/auth.php';
});


Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/appointments', [UserProfileController::class, 'myAppointments'])->name('api.user.appointments');
    Route::get('/user/bookings', [UserProfileController::class, 'myBookings'])->name('api.user.bookings');
    Route::put('/user/profile', [UserProfileController::class, 'updateProfile'])->name('api.user.update-profile');
    Route::put('/user/password', [UserProfileController::class, 'updatePassword'])->name('api.user.update-password');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('api.wishlist.index'); // لعرض المفضلة
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggleFavorite'])->name('api.wishlist.toggle'); // لإضافة أو إزالة عقار من المفضلة
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('api.appointments.store');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('api.appointments.update'); // تعديل
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('api.appointments.destroy'); // إلغاء
    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments'])->name('api.appointments.my-appointments');
    Route::get('/properties/{id}/booked-slots', [AppointmentController::class, 'getBookedSlots'])->name('api.properties.booked-slots');
    Route::post('/bookings', [BookingController::class, 'store'])->name('api.bookings.store'); // إرسال طلب حجز جديد مع إشعار الدفع
    // روابط الإشعارات للمستخدم الحالي (تتعرف تلقائياً هل هو زبون أم أدمن)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
});


Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('/users', [AdminUserController::class, 'index'])->name('api.users.index');      // جلب كل المستخدمين
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('api.users.show');  // جلب مستخدم محدد مع كل تاريخه العقاري
    Route::post('/properties', [PropertyController::class, 'store'])->name('api.properties.store');
    Route::put('/properties/{id}', [PropertyController::class, 'update'])->name('api.properties.update');
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('api.properties.destroy');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('api.appointments.index');
    Route::patch('/properties/{id}/status', [PropertyController::class, 'updateStatus'])->name('api.properties.status.update');
    Route::delete('/properties/images/{id}', [PropertyController::class, 'deleteImage'])->name('api.properties.images.delete');
    Route::patch('/properties/images/{id}/set-Main', [PropertyController::class, 'setMainImage'])->name('api.properties.images.set-main');
    Route::get('/properties/statistics', [PropertyController::class, 'statistics'])->name('api.properties.statistics');
    // قبول أو رفض طلب الحجز والتحقق المالي
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']);

    // جلب إحصائيات لوحة التحكم بالكامل
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats']);

    // Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('api.appointments.update-status');
});

