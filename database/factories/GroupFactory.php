<?php

namespace Database\Factories;

use App\Classes\GlobalVariable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'course_id' => null,
            'teacher_id' => null,
            'name' => strtoupper(fake()->lexify('???')) . '-' . fake()->randomNumber(3),
            'status' => fake()->randomElement(GlobalVariable::$groups_status),
            'comment' => fake()->sentence,
        ];
    }
}
