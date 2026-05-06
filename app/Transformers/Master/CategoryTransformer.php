<?php

namespace App\Transformers\Master;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryTransformer
{
    public static function transform(Category $category): array
    {
        return [
            'id' => encrypt_id($category->id),
            'service_id' => encrypt_id($category->service_id),
            'service_name' => $category->service->service_name,
            'name' => $category->name,
            'description' => $category->description,
            'status' => $category->status,
            'created_at' => $category->created_at?->toISOString(),
            'updated_at' => $category->updated_at?->toISOString(),
        ];
    }

    public static function collection(Collection $categories): array
    {
        return $categories->map(fn($category) => self::transform($category))->toArray();
    }
}
