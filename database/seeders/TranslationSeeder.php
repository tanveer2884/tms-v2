<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batchSize = 1000;
        $totalRecords = 100000;

        // Create 100 tags
        Tag::factory()->count(100)->create();

        // Create translations in batches
        for ($i = 0; $i < $totalRecords; $i += $batchSize) {
            $translations = Translation::factory()->count($batchSize)->create();

            // Attach random tags to each translation
            foreach ($translations as $translation) {
                // Randomly select 1 to 5 tags to attach
                $randomTags = Tag::inRandomOrder()->take(rand(1, 5))->pluck('id');
                $translation->tags()->attach($randomTags);
            }
        }
    }
}
