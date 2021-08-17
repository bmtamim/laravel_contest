<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name'   => 'Uncategorized',
            'slug'   => 'uncategorized',
            'status' => true,
        ]);

        Category::factory(10)->create();
    }
}
