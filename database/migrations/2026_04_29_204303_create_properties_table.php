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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب المكتب (الأدمن)
            $table->foreignId('location_id')->constrained(); // الربط مع جدول المناطق
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 15, 2); // سعر مناسب لليرة السورية
            $table->integer('area'); // المساحة
            $table->integer('rooms_count');
            $table->boolean('is_furnished')->default(0); // مفروش أم لا
            $table->boolean('has_elevator')->default(0);
            $table->enum('property_type', ['apartment', 'shop', 'villa', 'farm', 'land']);
            $table->enum('ownership_type', ['green_taboo', 'court_ruling', 'contract_sequence', 'state_property', 'other']);
            $table->enum('offer_type', ['sale', 'rent']);
            $table->boolean('is_featured')->default(0); // عقار مميز
            $table->enum('status', ['available', 'reserved', 'rented', 'sold'])->default('available');
            // سابقاً كان العمود is_available، تم استبداله بـ status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
