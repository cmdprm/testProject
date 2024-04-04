<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Article::factory(6)->create();
        Article::factory(2)->unpublished()->create();
    }
}
