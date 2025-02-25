<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'es', 'fr', 'de']);
        $uniqueKey = $locale . '-' . $this->faker->unique()->randomNumber(8);

        return [
            'locale' => $locale,
            'key' => $uniqueKey,
            'content' => $this->faker->sentence,
        ];
    }
}
