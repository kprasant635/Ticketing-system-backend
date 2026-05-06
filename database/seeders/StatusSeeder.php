<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Open'],
            ['status_name' => 'In Progress'],
            ['status_name' => 'On Hold'],
            ['status_name' => 'Resolved'],
            ['status_name' => 'Closed'],
            ['status_name' => 'Reopened'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
