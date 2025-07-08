<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Patient; // استيراد موديل Patient
use App\Models\Doctor; // استيراد موديل Doctor
use App\Models\MedicalRecord; // استيراد موديل MedicalRecord
use App\Models\Appointment; // استيراد موديل Appointment
use App\Models\AvailableSlot; // استيراد موديل AvailableSlot
use App\Models\User; // استيراد موديل User
use App\Policies\PatientPolicy; // استيراد Policy
use App\Policies\DoctorPolicy; // استيراد Policy
use App\Policies\MedicalRecordPolicy; // استيراد Policy
use App\Policies\AppointmentPolicy; // استيراد Policy 
use App\Policies\AvailableSlotPolicy; // استيراد Policy
use App\Policies\UserPolicy; // استيراد Policy


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Patient::class => PatientPolicy::class,
        Doctor::class => DoctorPolicy::class,
        MedicalRecord::class => MedicalRecordPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        AvailableSlot::class => AvailableSlotPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}