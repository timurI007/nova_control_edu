<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'fathers_name' => fake()->name('male'),
            'fathers_phone' => fake()->unique()->tollFreePhoneNumber(),
            'mothers_name' => fake()->name('female'),
            'mothers_phone' => fake()->unique()->tollFreePhoneNumber(),
            'notes' => fake()->sentence,
        ];
    }
}
