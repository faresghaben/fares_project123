<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Doctor;
use App\Models\AvailableSlot;

class AvailableSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $doctors = Doctor::all();

        if ($doctors->isEmpty()) {
            $this->command->info('No doctors found. Please run DoctorSeeder first.');
            return;
        }

        foreach ($doctors as $doctor) {
            for ($i = 0; $i < 5; $i++) {
                $startTime = $faker->dateTimeBetween('+1 day', '+2 weeks');
                $startTime->setTime($startTime->format('H'), ($startTime->format('i') < 30 ? 0 : 30), 0);
                $endTime = (clone $startTime)->modify('+30 minutes');

                AvailableSlot::create([
                    'doctor_id' => $doctor->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'available', // هذا هو التعديل الذي ذكرته
                    // 'day_of_week' => null, // يمكنك حذف هذا السطر من هنا إذا حذفته من المايجريشن
                    // 'is_booked' => false, // احذف هذا إذا كنت تستخدم status
                    // 'is_available' => true, // احذف هذا إذا كنت تستخدم status
                ]);
            }
        }
    }
}