<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Patient;
use App\Models\User; // لأن المريض مرتبط بمستخدم

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // جلب المستخدمين الذين دورهم 'patient' من جدول users
        $patientUsers = User::where('role', 'patient')->get();

        // إنشاء مرضى من المستخدمين الذين دورهم 'patient'
        foreach ($patientUsers as $user) {
            Patient::create([
                'user_id' => $user->id,
                'name' => $user->name, // نستخدم اسم المستخدم مباشرة
                'date_of_birth' => $faker->date('Y-m-d', '2000-01-01'),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'blood_type' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'allergies' => $faker->optional(0.7)->word() . ', ' . $faker->optional(0.3)->word(),
            ]);
        }
    }
}