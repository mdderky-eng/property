<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::create([
            'user_id' => 1, // تأكد أن لديك مستخدم برقم 1
            'title' => 'شقة فاخرة في المزة',
            'description' => 'شقة فاخرة في قلب المدينة مع إطلالة رائعة',
            'location_id' => 3, // ID المزة مثلاً
            'price' => 500000000,
            'rooms_count' => 3,
            'property_type' => 'apartment',
            'ownership_type' => 'green_taboo',
            'offer_type' => 'sale',
            'is_furnished'=> true,
            'has_elevator'=> true,
            'ownership_type' => 'green_taboo',
            'is_featured' => false,
            'is_available' => true,
            'area' => 150


        ]);
    }
}
