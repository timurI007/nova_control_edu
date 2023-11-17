<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
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
            'position_id' => null,
            'department_id' => null,
            'status' => fake()->randomElement(['Working', 'Sickleave', 'Absent', 'Vacation', 'Dismissed']),
            'notes' => fake()->sentence,
            'working_hours' => [
                'friday' => [],
                'monday' => [
                    '08:00-12:00',
                    '13:00-19:00',
                ],
                'sunday' => [],
                'tuesday' => [],
                'saturday' => [],
                'thursday' => [],
                'wednesday' => [],
                'exceptions' => []
            ],
        ];
    }
}
