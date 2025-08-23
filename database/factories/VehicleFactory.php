<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence,
            'marca' => $this->faker->company,
            'modelo' => $this->faker->word,
            'ano' => $this->faker->year,
            'preco' => $this->faker->numberBetween(50000, 500000),
            'cor' => $this->faker->colorName,
            'combustivel' => $this->faker->randomElement(['Gasolina', 'Etanol', 'Diesel']),
            'url_imagem' => $this->faker->imageUrl(),
        ];
    }
}
