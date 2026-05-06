<?php

namespace App\DTO\Master;

use Illuminate\Http\Request;

class PriorityDTO
{
    public function __construct(
        public readonly string $priority_name,
        public readonly ?int $sla_hours = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            priority_name: $request->input('priority_name'),
            sla_hours: $request->input('sla_hours'),
        );
    }
}
