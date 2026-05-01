<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $propertyId = $this->route('id');
        
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'area' => 'sometimes|integer|min:1',
            'rooms_count' => 'sometimes|integer|min:0',
            'property_type' => 'sometimes|in:apartment,shop,villa,farm,land',
            'offer_type' => 'sometimes|in:sale,rent',
            'ownership_type' => 'sometimes|in:green_taboo,court_ruling,contract_sequence,state_property,other',
            'location_id' => 'sometimes|exists:locations,id',
            'is_furnished' => 'nullable|boolean',
            'has_elevator' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'العنوان يجب أن يكون نصاً',
            'title.max' => 'العنوان طويل جداً',
            'description.string' => 'الوصف يجب أن يكون نصاً',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون أكبر من صفر',
            'area.integer' => 'المساحة يجب أن تكون رقماً صحيحاً',
            'area.min' => 'المساحة يجب أن تكون أكبر من صفر',
            'rooms_count.integer' => 'عدد الغرف يجب أن يكون رقماً صحيحاً',
            'rooms_count.min' => 'عدد الغرف يجب أن يكون صفراً أو أكثر',
            'property_type.in' => 'نوع العقار غير صحيح',
            'offer_type.in' => 'نوع العرض غير صحيح (بيع أو إيجار)',
            'ownership_type.in' => 'نوع الملكية غير صحيح',
            'location_id.exists' => 'الموقع المحدد غير موجود',
            'images.*.image' => 'الملف يجب أن يكون صورة',
            'images.*.mimes' => 'نوع الصورة غير مدعوم',
            'images.*.max' => 'حجم الصورة كبير جداً (2MB كحد أقصى)',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'price' => 'السعر',
            'area' => 'المساحة',
            'rooms_count' => 'عدد الغرف',
            'property_type' => 'نوع العقار',
            'offer_type' => 'نوع العرض',
            'ownership_type' => 'نوع الملكية',
            'location_id' => 'الموقع',
            'is_furnished' => 'مؤثث',
            'has_elevator' => 'مصعد',
            'is_featured' => 'مميز',
            'is_available' => 'متاح',
            'images' => 'الصور',
        ];
    }
}