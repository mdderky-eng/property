<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $damascus = Location::create(['name' => 'دمشق', 'parent_id' => null]);
        $rif_damascus = Location::create(['name' => 'ريف دمشق', 'parent_id' => null]);
        $aleppo = Location::create(['name' => 'حلب', 'parent_id' => null]);

        // 2. إضافة الأحياء وربطها بالمحافظات (عبر الـ ID الخاص بكل محافظة)
        Location::create(['name' => 'المزة', 'parent_id' => $damascus->id]);
        Location::create(['name' => 'أبو رمانة', 'parent_id' => $damascus->id]);

        Location::create(['name' => 'جرمانا', 'parent_id' => $rif_damascus->id]);
        Location::create(['name' => 'صحنايا', 'parent_id' => $rif_damascus->id]);

        Location::create(['name' => 'الفرقان', 'parent_id' => $aleppo->id]);
    }
}
