<?php

namespace App\Modules\Master\DTO;

class ServiceDTO
{
    public function __construct(
        public string $service_name,
        public ?int $status,
        public ?string $description,
    ) {}

    public static function fromRequest($request)
    {
        return new self(
            service_name: $request->service_name,
            status: $request->status,
            description: $request->description,
        );
    }
}
