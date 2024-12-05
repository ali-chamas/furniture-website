<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Couches']);
        Category::create(['name' => 'Tables']);
        Category::create(['name' => 'Outdoor']);
        Category::create(['name' => 'Kitchen']);
        Category::create(['name' => 'Closets']);
    }
}
