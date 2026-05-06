<?php

namespace App\DTO\Master;

use Illuminate\Http\Request;

class CategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $service_id = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $decryptedServiceId = decrypt_id($request->input('serviceId'));

        if (!$decryptedServiceId) {
            throw new \Exception('Invalid Service ID');
        }

        return new self(
            name: $request->input('name'),
            service_id: $decryptedServiceId,
            description: $request->input('description'),
            status: $request->input('status'),
        );
    }
}
