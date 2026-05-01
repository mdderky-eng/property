<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => number_format($this->price, 2),
            'area' => $this->area,
            'rooms_count' => $this->rooms_count,
            'property_type' => $this->property_type,
            'property_type_display' => $this->getPropertyTypeDisplay(),
            'offer_type' => $this->offer_type,
            'offer_type_display' => $this->getOfferTypeDisplay(),
            'ownership_type' => $this->ownership_type,
            'ownership_type_display' => $this->getOwnershipTypeDisplay(),
            'is_furnished' => $this->is_furnished,
            'has_elevator' => $this->has_elevator,
            'is_featured' => $this->is_featured,
            'is_available' => $this->is_available,
            'location' => new LocationResource($this->whenLoaded('location')),
            'images' => PropertyImageResource::collection($this->whenLoaded('images')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getPropertyTypeDisplay(): string
    {
        return match($this->property_type) {
            'apartment' => 'شقة',
            'shop' => 'محل',
            'villa' => 'فيلا',
            'farm' => 'مزرعة',
            'land' => 'أرض',
            default => $this->property_type,
        };
    }

    private function getOfferTypeDisplay(): string
    {
        return match($this->offer_type) {
            'sale' => 'بيع',
            'rent' => 'إيجار',
            default => $this->offer_type,
        };
    }

    private function getOwnershipTypeDisplay(): string
    {
        return match($this->ownership_type) {
            'green_taboo' => 'طابو أخضر',
            'court_ruling' => 'حكم محكمة',
            'contract_sequence' => 'تسلسل عقد',
            'state_property' => 'ملك دولة',
            'other' => 'أخرى',
            default => $this->ownership_type,
        };
    }
}
