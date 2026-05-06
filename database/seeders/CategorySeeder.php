<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['service_id' => '8', 'name' => 'Bug', 'description' => 'Report bugs and issues', 'status' => 1],
            ['service_id' => '8', 'name' => 'Feature Request', 'description' => 'Request new features', 'status' => 1],
            ['service_id' => '8', 'name' => 'Enhancement', 'description' => 'Improvement to existing features', 'status' => 1],
            ['service_id' => '8', 'name' => 'Documentation', 'description' => 'Documentation related tickets', 'status' => 1],
            ['service_id' => '8', 'name' => 'Support', 'description' => 'General support tickets', 'status' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
