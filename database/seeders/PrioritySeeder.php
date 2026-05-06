<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            ['priority_name' => 'Low', 'sla_hours' => 45],
            ['priority_name' => 'Medium', 'sla_hours' => 24],
            ['priority_name' => 'High', 'sla_hours' => 8],
            ['priority_name' => 'Critical', 'sla_hours' => 2],
        ];

        foreach ($priorities as $priority) {
            Priority::create($priority);
        }
    }
}
