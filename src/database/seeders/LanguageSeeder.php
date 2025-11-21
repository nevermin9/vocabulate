<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
            ['code' => 'cz', 'name' => 'Czech'],
            ['code' => 'el', 'name' => 'Greek'],
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'et', 'name' => 'Estonian'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'ko', 'name' => 'Korean'],
            ['code' => 'lt', 'name' => 'Lithuanian'],
            ['code' => 'lv', 'name' => 'Latvian'],
            ['code' => 'nl', 'name' => 'Dutch'],
            ['code' => 'no', 'name' => 'Norwegian'],
            ['code' => 'pl', 'name' => 'Polish'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'ro', 'name' => 'Romanian'],
            ['code' => 'sv', 'name' => 'Swedish'],
            ['code' => 'tr', 'name' => 'Turkish'],
            ['code' => 'uk', 'name' => 'Ukranian'],
            ['code' => 'zh', 'name' => 'Chinese'],
        ]);
    }
}
