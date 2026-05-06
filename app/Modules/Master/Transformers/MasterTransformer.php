<?php

namespace App\Modules\Master\Transformers;

class MasterTransformer
{
    public static function service($data)
    {
        return [
            'id' => encrypt_id($data->id),
            'service_name' => $data->service_name,
            'description' => $data->description,
            'status' => $data->status,
            'categoryCount' => $data->categories_count ?? 0,
            'created_at' => $data->created_at?->toISOString(),
            'updated_at' => $data->updated_at?->toISOString(),
        ];
    }

    public static function category($data)
    {
        return [
            'id' => $data->id,
            'category_name' => $data->category_name,
            'service_id' => $data->service_id
        ];
    }

    public static function mapping($data)
    {
        return [
            'id' => $data->id
        ];
    }
}
