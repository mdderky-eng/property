<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        // نبدأ بالاستعلام الأساسي مع جلب الصور والمنطقة
        $query = Property::with(['images', 'location']);

        // 1. فلترة حسب المحافظة أو الحي (location_id)
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
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

        // 7. فلترة حسب مؤثث / غير مؤثث
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

        // 11. فلترة حسب التوفر (متاح / محجوز)
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // 12. فلترة حسب البحث في العنوان أو الوصف
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 13. ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['price', 'area', 'rooms_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
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

    /**
     * Store a newly created property
     */
    public function store(StorePropertyRequest $request)
    {
        // Get validated data
        $validated = $request->validated();

        // Add user_id (for now using a default user, in production use auth)
        $validated['user_id'] = 1;

        // Create property
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

    /**
     * Update an existing property
     */
    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }

        // Get validated data
        $validated = $request->validated();

        // Update property
        $property->update($validated);

        // Handle new image upload if provided
        if ($request->hasFile('images')) {
            // Delete old images
            $property->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');

                $property->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        // Load relationships and return
        $property->load(['images', 'location', 'user']);

        return (new PropertyResource($property))->response();
    }

    /**
     * Remove a property
     */
    public function destroy($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }

        // Delete property images from storage
        foreach ($property->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
        }

        // Delete property
        $property->delete();

        return response()->json([
            'message' => 'تم حذف العقار بنجاح'
        ], 200);
    }

    /**
     * Get property statistics
     */
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
            'available_count' => Property::where('is_available', true)->count(),
            'rent_properties' => Property::where('offer_type', 'rent')->count(),
            'sale_properties' => Property::where('offer_type', 'sale')->count(),
            'avg_price' => Property::avg('price'),
            'avg_area' => Property::avg('area'),
        ];

        return response()->json($stats);
    }
}
