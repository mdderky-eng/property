<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // العميل
            $table->foreignId('property_id')->constrained()->onDelete('cascade'); // العقار المطلوب معاينته
            $table->string('client_phone');
            $table->date('appointment_date'); // التاريخ (مثلاً 2026-05-15)
            $table->time('appointment_time'); // الوقت (مثلاً 14:00:00)

            // الحالة التي يتحكم بها الأدمن (قيد الانتظار، مقبول، مرفوض)
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
