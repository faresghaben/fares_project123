<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $patients = Patient::all(); // جلب جميع المرضى
        $doctors = Doctor::all();   // جلب جميع الأطباء

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->info('No patients or doctors found. Please run PatientSeeder and DoctorSeeder first.');
            return;
        }

        foreach ($patients as $patient) {
            // لكل مريض، ننشئ بعض المواعيد
            for ($i = 0; $i < 2; $i++) { // مثلاً، موعدين لكل مريض
                $doctor = $doctors->random(); // اختيار طبيب عشوائي
                $startTime = $faker->dateTimeBetween('-1 month', '+1 month'); // وقت بدء عشوائي
                $endTime = (clone $startTime)->modify('+30 minutes'); // نهاية بعد 30 دقيقة

                Appointment::create([
                    'patient_id' => null,
                    'doctor_id' => $doctor->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'available',
                    'cancellation_reason' => $faker->optional()->sentence,
                ]);
            }
        }
    }
}