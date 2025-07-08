<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // المدير يمكنه رؤية جميع المواعيد
        // الطبيب يمكنه رؤية مواعيده (أو كل المواعيد إذا أردت ذلك)
        // المريض يمكنه رؤية مواعيده فقط (هذا سيتم تقييده في الـ Controller)
        return $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('patient');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // المدير يمكنه رؤية أي موعد
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه رؤية مواعيده الخاصة
        if ($user->hasRole('doctor') && $appointment->doctor_id === $user->doctor->id) {
            return true;
        }

        // المريض يمكنه رؤية مواعيده الخاصة فقط
        if ($user->hasRole('patient') && $appointment->patient_id === $user->patient->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // المدير والطبيب يمكنهم إنشاء مواعيد
        return $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // المدير يمكنه تعديل أي موعد
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه تعديل مواعيده الخاصة
        if ($user->hasRole('doctor') && $appointment->doctor_id === $user->doctor->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // المدير فقط يمكنه حذف المواعيد
        return $user->hasRole('admin');
    }

    // يمكنك إضافة دوال restore و forceDelete إذا كانت لديك
}