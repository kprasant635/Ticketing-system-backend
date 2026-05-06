<?php

namespace App\Transformers\Master;

use App\Models\Status;
use Illuminate\Support\Collection;

class StatusTransformer
{
    public static function transform(Status $status): array
    {
        return [
            'id'          => $status->id,
            'status_name' => $status->status_name,
            'created_at'  => $status->created_at?->toISOString(),
            'updated_at'  => $status->updated_at?->toISOString(),
        ];
    }

    public static function collection(Collection $statuses): array
    {
        return $statuses->map(fn($status) => self::transform($status))->toArray();
    }
}
