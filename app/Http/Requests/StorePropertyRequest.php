<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'area' => 'required|integer|min:1',
            'rooms_count' => 'required|integer|min:0',
            'property_type' => 'required|in:apartment,shop,villa,farm,land',
            'offer_type' => 'required|in:sale,rent',
            'ownership_type' => 'required|in:green_taboo,court_ruling,contract_sequence,state_property,other',
            'location_id' => 'required|exists:locations,id',
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
            'title.required' => 'العنوان مطلوب',
            'title.max' => 'العنوان طويل جداً',
            'description.required' => 'الوصف مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون أكبر من صفر',
            'area.required' => 'المساحة مطلوبة',
            'area.integer' => 'المساحة يجب أن تكون رقماً صحيحاً',
            'area.min' => 'المساحة يجب أن تكون أكبر من صفر',
            'rooms_count.required' => 'عدد الغرف مطلوب',
            'rooms_count.integer' => 'عدد الغرف يجب أن يكون رقماً صحيحاً',
            'rooms_count.min' => 'عدد الغرف يجب أن يكون صفراً أو أكثر',
            'property_type.required' => 'نوع العقار مطلوب',
            'property_type.in' => '(apartment,shop,villa,farm,land)نوع العقار غير صحيح',
            'offer_type.required' => 'نوع العرض مطلوب',
            'offer_type.in' => '(sale,rent)نوع العرض غير صحيح',
            'ownership_type.required' => 'نوع الملكية مطلوب',
            'ownership_type.in' => '(green_taboo,court_ruling,contract_sequence,state_property,other)نوع الملكية غير صحيح',
            'location_id.required' => 'الموقع مطلوب',
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
