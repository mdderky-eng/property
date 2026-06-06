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
        // 1. المحافظات الرئيسية (المستوى الأول)
        $damascus = Location::create(['name' => 'دمشق', 'parent_id' => null]);
        $rif_damascus = Location::create(['name' => 'ريف دمشق', 'parent_id' => null]);

        // 2. مناطق مدينة دمشق (المستوى الثاني - بناءً على الصورة الأولى)
        $damascusAreas = [
            'دمشق القديمة',
            'ساروجة',
            'القنوات',
            'جوبر',
            'الميدان',
            'الشاغور',
            'القدم',
            'كفر سوسة',
            'المزة',
            'دمر',
            'برزة',
            'القابون',
            'ركن الدين',
            'الصالحية',
            'المهاجرين',
            'اليرموك'
        ];

        foreach ($damascusAreas as $areaName) {
            $parentArea = Location::create([
                'name' => $areaName,
                'parent_id' => $damascus->id
            ]);

            // إضافة أحياء فرعية لمناطق معينة (المستوى الثالث - مثل دشيش)
            if ($areaName === 'دمشق القديمة') {
                $subAreas = [
                    'باب توما',
                    'الحميدية',
                    'الحريقة',
                    'العمارة الجوانية'
                ];
                foreach ($subAreas as $sub) {
                    Location::create(['name' => $sub, 'parent_id' => $parentArea->id]);
                }
            }
        }

        // 3. مناطق ريف دمشق (المستوى الثاني - بناءً على الصورة الثانية)
        $rifAreas = [
            'مركز ريف دمشق',
            'دوما',
            'القطيفة',
            'التل',
            'يبرود',
            'النبك',
            'الزبداني',
            'قطنا',
            'داريا'
        ];

        foreach ($rifAreas as $areaName) {
            $parentRif = Location::create([
                'name' => $areaName,
                'parent_id' => $rif_damascus->id
            ]);

            // إضافة بلدات تتبع لـ "مركز ريف دمشق" (المستوى الثالث)
            if ($areaName === 'مركز ريف دمشق') {
                $subRif = [
                    'حرستا',
                    'زملكا',
                    'عربين',
                    'كفربطنا',
                    'جرمانا',
                    'صحنايا'
                ];
                foreach ($subRif as $sub) {
                    Location::create(['name' => $sub, 'parent_id' => $parentRif->id]);
                }
            }
        }
    }
}
