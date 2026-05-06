<?php

namespace App\Transformers\Master;

use App\Models\Priority;
use Illuminate\Support\Collection;

class PriorityTransformer
{
    public static function transform(Priority $priority): array
    {
        return [
            'id'            => $priority->id,
            'priority_name' => $priority->priority_name,
            'sla_hours'     => $priority->sla_hours,
            'created_at'    => $priority->created_at?->toISOString(),
            'updated_at'    => $priority->updated_at?->toISOString(),
        ];
    }

    public static function collection(Collection $priorities): array
    {
        return $priorities->map(fn($priority) => self::transform($priority))->toArray();
    }
}
