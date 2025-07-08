<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\MedicalRecord;

class MedicalRecordSeeder extends Seeder
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
            // لكل مريض، ننشئ بعض السجلات الطبية
            for ($i = 0; $i < 2; $i++) { // مثلاً، سجلين لكل مريض
                $doctor = $doctors->random(); // اختيار طبيب عشوائي

                MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'diagnosis' => $faker->sentence(5),
                    'treatment' => $faker->paragraph(1),
                    'record_date' => $faker->date(),
                ]);
            }
        }
    }
}