<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSchedule>
 */
class UserScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $day = fake()->randomElement(DayOfWeek::cases());

        return [
            'user_id' => User::factory(),
            'day_of_week' => $day,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'lunch_start' => '12:00',
            'lunch_end' => '13:00',
        ];
    }
}
