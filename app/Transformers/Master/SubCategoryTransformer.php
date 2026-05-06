<?php

namespace App\Transformers\Master;

use App\Models\SubCategory;
use Illuminate\Support\Collection;

class SubCategoryTransformer
{
    public static function transform(SubCategory $subcategory): array
    {
        return [
            'id' => encrypt_id($subcategory->id),
            'category_id' => encrypt_id($subcategory->category_id),
            'category_name' => $subcategory->category?->name,
            'service_id' => encrypt_id($subcategory->category?->service_id),
            'service_name' => $subcategory->category?->service?->service_name,
            'subcategory_name' => $subcategory->name,
            'description' => $subcategory->description,
            'status' => $subcategory->status,
            'created_at' => $subcategory->created_at?->toISOString(),
            'updated_at' => $subcategory->updated_at?->toISOString(),
        ];
    }

    public static function collection(Collection $subcategories): array
    {
        return $subcategories->map(fn($subcategory) => self::transform($subcategory))->toArray();
    }
}
