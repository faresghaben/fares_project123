<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Doctor; // تأكد من استيراد نموذج Doctor

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // الحصول على المستخدمين الذين دورهم 'doctor'
        $doctorUsers = User::where('role', 'doctor')->get();

        foreach ($doctorUsers as $user) {
            Doctor::create([
                'user_id' => $user->id,
                'name' => $user->name, // نستخدم اسم المستخدم مباشرة
                'specialization' => $faker->randomElement(['Cardiology', 'Pediatrics', 'Dermatology', 'Neurology', 'Orthopedics', 'General Medicine']),
                'license_number' => $faker->unique()->regexify('[A-Z]{2}[0-9]{5}'),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }
    }
}