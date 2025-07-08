<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Auth\Access\Response;

class MedicalRecordPolicy
{
    /**
     * تحدد ما إذا كان المستخدم يمكنه رؤية قائمة بجميع السجلات الطبية.
     */
    public function viewAny(User $user): bool
    {
        // المدير يمكنه رؤية جميع السجلات الطبية
        // الطبيب يمكنه رؤية قائمة السجلات الطبية (حتى يتمكن من رؤية سجلات مرضاه)
        // المريض يمكنه رؤية قائمة السجلات الطبية (حتى يتمكن من رؤية سجلاته الخاصة)
        return $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('patient');
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه عرض سجل طبي معين.
     */
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        // المدير يمكنه رؤية أي سجل طبي
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه رؤية السجلات الطبية لمرضاه فقط
        // هذا يعني أن الطبيب الذي أنشأ السجل يجب أن يكون هو نفسه الذي يحاول عرضه،
        // أو الطبيب الذي المريض مرتبط به (إذا كانت هناك علاقة Doctor-Patient مباشرة).
        if ($user->hasRole('doctor') && $medicalRecord->doctor_id === $user->doctor->id) {
            return true;
        }

        // المريض يمكنه رؤية سجلاته الطبية الخاصة فقط
        if ($user->hasRole('patient') && $medicalRecord->patient_id === $user->patient->id) {
            return true;
        }

        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه تحديث سجل طبي معين.
     */
    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        // المدير يمكنه تحديث أي سجل طبي
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه تحديث السجلات الطبية لمرضاه فقط
        if ($user->hasRole('doctor') && $medicalRecord->doctor_id === $user->doctor->id) {
            return true;
        }

        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه إنشاء سجل طبي جديد.
     */
    public function create(User $user): bool
    {
        // المدير والطبيب يمكنهم إنشاء سجلات طبية
        return $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه حذف سجل طبي معين.
     */
    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        // فقط المدير يمكنه حذف السجلات الطبية
        return $user->hasRole('admin');
    }
}