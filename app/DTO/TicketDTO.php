<?php

namespace App\DTO;

class TicketDTO
{
    public function __construct(
        public int $service_id,
        public int $category_id,
        public int $sub_category_id,
        public string $subject,
        public string $description,
        public ?string $applicant_name = null,
        public ?string $applicant_phone = null,
        public ?string $applicant_email = null,
        public array $files = []
    ) {}

    public static function fromRequest($request): self
    {
        $files = $request->file('files');
        if ($files && !is_array($files)) {
            $files = [$files];
        }

        return new self(
            service_id: (int) $request->service_id,
            category_id: (int) $request->category_id,
            sub_category_id: (int) $request->sub_category_id,
            subject: $request->subject,
            description: $request->description,
            applicant_name: $request->input('applicant_name'),
            applicant_phone: $request->input('applicant_phone'),
            applicant_email: $request->input('applicant_email'),
            files: $files ?? []
        );
    }
}
