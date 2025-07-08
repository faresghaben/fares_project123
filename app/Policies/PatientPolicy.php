<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    /**
     * تحدد ما إذا كان المستخدم يمكنه رؤية قائمة بجميع المرضى.
     */
    public function viewAny(User $user): bool
    {
        // المدير يمكنه رؤية جميع المرضى
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه الوصول إلى صفحة قائمة المرضى
        if ($user->hasRole('doctor')) {
            return true;
        }

        // أي دور آخر (بخلاف المدير والطبيب) لا يمكنه رؤية قائمة المرضى
        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه عرض ملف مريض معين.
     */
    public function view(User $user, Patient $patient): bool
    {
        // المدير يمكنه رؤية أي ملف مريض
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه رؤية مرضاه فقط
        // يجب أن تكون هناك علاقة بين الطبيب والمريض (مثلاً عبر المواعيد أو السجلات الطبية)
        if ($user->hasRole('doctor')) {
            // تحقق ما إذا كان هذا المريض مرتبطاً بهذا الطبيب عبر أي من سجلاته الطبية
            // (سجلات الطبيب يجب أن تحتوي على doctor_id)
            if ($patient->medicalRecords()->where('doctor_id', $user->doctor->id)->exists()) {
                return true;
            }
            // أو عبر المواعيد التي لديه مع هذا الطبيب
            if ($patient->appointments()->where('doctor_id', $user->doctor->id)->exists()) {
                return true;
            }
        }

        // المريض يمكنه رؤية ملفه الشخصي فقط
        // (تأكد أن هذا الشرط إذا تم استدعاؤه، أن $patient->user_id موجود)
        if ($user->hasRole('patient') && $user->id === $patient->user_id) {
            return true;
        }

        return false; // لا يسمح بالعرض لأي حالة أخرى
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه تحديث بيانات مريض معين.
     */
    public function update(User $user, Patient $patient): bool
    {
        // المدير يمكنه تحديث أي ملف مريض
        if ($user->hasRole('admin')) {
            return true;
        }

        // المريض يمكنه تحديث ملفه الشخصي فقط
        if ($user->hasRole('patient') && $user->id === $patient->user_id) {
            return true;
        }

        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه إنشاء ملف مريض جديد.
     */
    public function create(User $user): bool
    {
        // فقط المدير يمكنه إنشاء مرضى جدد
        return $user->hasRole('admin');
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه حذف ملف مريض معين.
     */
    public function delete(User $user, Patient $patient): bool
    {
        // فقط المدير يمكنه حذف المرضى
        return $user->hasRole('admin');
    }

    // ... يمكنك إضافة دوال أخرى حسب الحاجة (مثل restore, forceDelete)
}