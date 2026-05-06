<?php

namespace App\DTO\Master;

use Illuminate\Http\Request;

class SubCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $categoryId,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('subcategory_name'),
            categoryId: $request->input('category_id'),
            description: $request->input('description'),
            status: $request->input('status'),
        );
    }
}
