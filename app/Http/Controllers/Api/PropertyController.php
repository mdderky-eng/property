<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        // الاستعلام الأساسي مع جلب الصور والمنطقة
        $query = Property::with(['images', 'location']);


        if ($request->has('location_id')) {
            $location = \App\Models\Location::find($request->location_id);

            if ($location) {
                // نجلب مصفوفة تضم ID المنطقة المختارة وكل ما يتبعها
                $allIds = $location->getAllChildrenIds();
                $query->whereIn('location_id', $allIds);
            }
        }

        // 2. فلترة حسب نوع العقار (apartment, villa, shop, farm, land)
        if ($request->has('type')) {
            $query->where('property_type', $request->type);
        }

        // 3. فلترة حسب نوع العرض (sale, rent)
        if ($request->has('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        // 4. فلترة حسب السعر (من - إلى)
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 5. فلترة حسب المساحة (من - إلى)
        if ($request->has('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }
        if ($request->has('max_area')) {
            $query->where('area', '<=', $request->max_area);
        }

        // 6. فلترة حسب عدد الغرف
        if ($request->has('rooms_count')) {
            $query->where('rooms_count', $request->rooms_count);
        }
        if ($request->has('min_rooms')) {
            $query->where('rooms_count', '>=', $request->min_rooms);
        }
        if ($request->has('max_rooms')) {
            $query->where('rooms_count', '<=', $request->max_rooms);
        }

        // 7. فلترة حسب مفروش / غير مفروش
        if ($request->has('is_furnished')) {
            $query->where('is_furnished', $request->boolean('is_furnished'));
        }

        // 8. فلترة حسب وجود مصعد
        if ($request->has('has_elevator')) {
            $query->where('has_elevator', $request->boolean('has_elevator'));
        }

        // 9. فلترة حسب نوع الملكية (green_taboo, court_ruling, etc.)
        if ($request->has('ownership_type')) {
            $query->where('ownership_type', $request->ownership_type);
        }

        // 10. فلترة حسب العقارات المميزة فقط
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // 11. فلترة حسب حالة العقار
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // 12. فلترة حسب البحث في العنوان أو الوصف
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        //  ترتيب النتائج
        $sortRaw = $request->input('sort_by', 'created_at-desc');


        $sortParts = explode('-', $sortRaw);
        $sortBy = $sortParts[0]; // العمود (price)
        $sortOrder = $sortParts[1] ?? 'desc'; // الاتجاه (desc) إذا لم يوجد نجعل الافتراضي desc

        $allowedSorts = ['price', 'area', 'rooms_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {

            $finalOrder = $request->input('sort_order', $sortOrder);
            $query->orderBy($sortBy, $finalOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }
        // جلب العقارات مع الترقيم (pagination)
        $perPage = $request->get('per_page', 15);
        $properties = $query->paginate($perPage);

        return PropertyResource::collection($properties)->response();
    }

    public function show($id)
    {
        // جلب العقار مع الصور والمنطقة والمستخدم (صاحب العقار)
        $property = Property::with(['images', 'location', 'user'])->find($id);

        // إذا لم يجد العقار، نرسل رسالة خطأ واضحة
        if (!$property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }

        return (new PropertyResource($property))->response();
    }

    /**
     * Get filter options for the API
     */
    public function filterOptions()
    {
        return response()->json([
            'property_types' => [
                ['value' => 'apartment', 'label' => 'شقة'],
                ['value' => 'shop', 'label' => 'محل'],
                ['value' => 'villa', 'label' => 'فيلا'],
                ['value' => 'farm', 'label' => 'مزرعة'],
                ['value' => 'land', 'label' => 'أرض'],
            ],
            'offer_types' => [
                ['value' => 'sale', 'label' => 'بيع'],
                ['value' => 'rent', 'label' => 'إيجار'],
            ],
            'ownership_types' => [
                ['value' => 'green_taboo', 'label' => 'طابو أخضر'],
                ['value' => 'court_ruling', 'label' => 'حكم محكمة'],
                ['value' => 'contract_sequence', 'label' => 'تسلسل عقد'],
                ['value' => 'state_property', 'label' => 'ملك دولة'],
                ['value' => 'other', 'label' => 'أخرى'],
            ],
            'sort_options' => [
                ['value' => 'created_at-desc', 'label' => 'الأحدث'],
                ['value' => 'created_at-asc', 'label' => 'الأقدم'],
                ['value' => 'price-desc', 'label' => 'السعر: من الأعلى'],
                ['value' => 'price-asc', 'label' => 'السعر: من الأدنى'],
                ['value' => 'area-desc', 'label' => 'المساحة: من الأكبر'],
                ['value' => 'area-asc', 'label' => 'المساحة: من الأصغر'],
            ],
        ]);
    }


    public function store(StorePropertyRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->input('status', 'available');
        $property = Property::create($validated);

        // Handle image upload if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');

                $property->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0, // First image is primary
                ]);
            }
        }

        // Load relationships and return
        $property->load(['images', 'location', 'user']);

        return (new PropertyResource($property))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validated();

        // معالجة قيم الحالة إذا أرسل المستخدم قيمة جديدة
        if ($request->has('status')) {
            $validated['status'] = $request->status;
        }

        $property->update($validated);

        // معالجة الصور: هنا سنضيف الصور الجديدة "إضافة" وليس "استبدال"
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                $property->images()->create([
                    'image_path' => $path,
                    'is_primary' => false, // الصور الجديدة ليست أساسية افتراضياً
                ]);
            }
        }

        $property->load(['images', 'location', 'user']);
        return (new PropertyResource($property))->response();

    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,reserved,rented,sold'
        ]);

        $property = Property::findOrFail($id);

        $property->status = $request->status;
        $property->save();

        // إذا تحول العقار إلى مباع أو مؤجر، ارفض كل الحجوزات المعلقة عليه تلقائياً
        if (in_array($request->status, ['sold', 'rented'])) {
            Booking::where('property_id', $id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
        }

        return response()->json([
            'message' => 'تم تحديث حالة العقار بنجاح.',
            'property' => $property
        ]);
    }



    public function destroy($id)
    {


        $property = Property::findOrFail($id);
        $property->delete(); // الـ Observer سيتكفل بمسح الصور من المجلد تلقائياً
        return response()->json(['message' => 'تم حذف العقار بنجاح']);
    }

    public function deleteImage($id)
    {
        // جلب الصورة أو إرجاع 404 إذا لم توجد
        $image = \App\Models\PropertyImage::findOrFail($id);

        // التحقق من أن المستخدم الحالي هو صاحب العقار
        if ($image->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذه الصورة'], 403);
        }

        // حذف السجل من قاعدة البيانات
        // ملاحظة: Observer سيقوم بحذف الملف تلقائياً
        $image->delete();

        return response()->json([
            'message' => 'تم حذف الصورة بنجاح من العقار والسيرفر'
        ], 200);
    }
    public function setMainImage($id)
    {
        //جلب الصورة المطلوبة
        $image = PropertyImage::findOrFail($id);

        //  التحقق من الملكية (صاحب العقار فقط)
        if ($image->property->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        //  (المنطق البرمجي) اجعل كل صور هذا العقار غير أساسية أولاً
        PropertyImage::where('property_id', $image->property_id)
            ->update(['is_main' => false]);

        //اجعل هذه الصورة المحددة هي الأساسية
        $image->update(['is_main' => true]);

        return response()->json([
            'message' => 'تم تعيين الصورة كصورة أساسية بنجاح'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total_properties' => Property::count(),
            'by_property_type' => Property::select('property_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('property_type')
                ->get(),
            'by_offer_type' => Property::select('offer_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('offer_type')
                ->get(),
            'by_location' => Property::with('location')
                ->select('location_id')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('location_id')
                ->get(),
            'featured_count' => Property::where('is_featured', true)->count(),
            'available_count' => Property::where('status', 'available')->count(),
            'rent_properties' => Property::where('offer_type', 'rent')->count(),
            'sale_properties' => Property::where('offer_type', 'sale')->count(),
            'avg_price' => Property::avg('price'),
            'avg_area' => Property::avg('area'),
        ];

        return response()->json($stats);
    }

    public function compare(Request $request)
    {
        // التحقق من أن المدخلات تحتوي على رقمي عقارين موجودين بالفعل في قاعدة البيانات
        $request->validate([
            'property_id_1' => 'required|exists:properties,id',
            'property_id_2' => 'required|exists:properties,id',
        ], [
            'property_id_1.exists' => 'العقار الأول غير موجود في النظام.',
            'property_id_2.exists' => 'العقار الثاني غير موجود في النظام.',
        ]);

        // جلب بيانات العقارين مع الصور والموقع (العلاقات المرتبطة بهما)
        $property1 = Property::with(['images', 'location'])->find($request->property_id_1);
        $property2 = Property::with(['images', 'location'])->find($request->property_id_2);

        return response()->json([
            'status' => 'success',
            'data' => [

                'property_1' => new PropertyResource($property1),
                'property_2' => new PropertyResource($property2),
            ]
        ], 200);
    }



}
