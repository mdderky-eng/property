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
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade'); // إذا حُذف العقار تُحذف صوره
            $table->string('image_path'); // مسار الملف في مجلد storage
            $table->boolean('is_main')->default(false); // لتحديد الصورة التي تظهر في غلاف الإعلان
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
