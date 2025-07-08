<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,         // أولاً: المستخدمون (المرضى والأطباء)
            PatientSeeder::class,      // ثانياً: المرضى
            DoctorSeeder::class,       // ثالثاً: الأطباء
            AvailableSlotSeeder::class,// رابعاً: الفتحات المتاحة للأطباء
            AppointmentSeeder::class,  // خامساً: المواعيد
            MedicalRecordSeeder::class,// سادساً: السجلات الطبية
        ]);
    }
}