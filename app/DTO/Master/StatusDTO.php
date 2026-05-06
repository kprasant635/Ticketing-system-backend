<?php

namespace App\DTO\Master;

use Illuminate\Http\Request;

class StatusDTO
{
    public function __construct(
        public readonly string $status_name,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            status_name: $request->input('status_name'),
        );
    }
}
