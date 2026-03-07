<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\Service;
use App\Models\User;
use App\Models\UserSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarbershopSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Men\'s cut', 'description' => 'Classic men\'s haircut', 'duration_minutes' => 30, 'price' => 25.00],
            ['name' => 'Beard trim', 'description' => 'Beard shaping and trim', 'duration_minutes' => 15, 'price' => 12.00],
            ['name' => 'Hair and beard', 'description' => 'Full service', 'duration_minutes' => 45, 'price' => 35.00],
            ['name' => 'Kids cut', 'description' => 'Ages 12 and under', 'duration_minutes' => 25, 'price' => 18.00],
        ];

        $createdServices = [];
        foreach ($services as $attrs) {
            $createdServices[] = Service::create($attrs);
        }

        $staff = User::factory()->count(3)->create();

        foreach ($staff as $index => $user) {
            $user->services()->attach(
                collect($createdServices)->random(rand(2, 4))->pluck('id')
            );

            foreach ([DayOfWeek::Monday, DayOfWeek::Tuesday, DayOfWeek::Wednesday, DayOfWeek::Thursday, DayOfWeek::Friday] as $day) {
                UserSchedule::create([
                    'user_id' => $user->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'lunch_start' => '12:00',
                    'lunch_end' => '13:00',
                ]);
            }
        }
    }
}
