<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor; 
use Illuminate\Auth\Access\Response;

class DoctorPolicy
{
    /**
     * تحدد ما إذا كان المستخدم يمكنه رؤية قائمة بجميع الأطباء.
     */
    public function viewAny(User $user): bool
    {
        // المدير يمكنه رؤية جميع الأطباء
        // الطبيب يمكنه رؤية قائمة الأطباء (مثلاً للتنسيق)
        // المريض يمكنه رؤية قائمة الأطباء (مثلاً للتنسيق)
        return $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('patient');
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه عرض ملف طبيب معين.
     */
    public function view(User $user, Doctor $doctor): bool
    {
        // المدير يمكنه رؤية أي ملف طبيب
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه رؤية ملفه الشخصي فقط
        if ($user->hasRole('doctor') && $user->id === $doctor->user_id) {
            return true;
        }

        // المريض يمكنه رؤية ملف الطبيب لأغراض الحجز أو عرض التفاصيل الأساسية
        // (لا توجد قيود على المريض هنا في Policy لأننا سنحدد البيانات المرئية في الـ Controller/View)
        if ($user->hasRole('patient')) {
            return true; // يسمح للمريض بالعرض العام للبروفايل
        }

        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه تحديث بيانات طبيب معين.
     */
    public function update(User $user, Doctor $doctor): bool
    {
        // المدير يمكنه تحديث أي ملف طبيب
        if ($user->hasRole('admin')) {
            return true;
        }

        // الطبيب يمكنه تحديث ملفه الشخصي فقط
        if ($user->hasRole('doctor') && $user->id === $doctor->user_id) {
            return true;
        }

        return false;
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه إنشاء ملف طبيب جديد.
     */
    public function create(User $user): bool
    {
        // فقط المدير يمكنه إنشاء أطباء جدد
        return $user->hasRole('admin');
    }

    /**
     * تحدد ما إذا كان المستخدم يمكنه حذف ملف طبيب معين.
     */
    public function delete(User $user, Doctor $doctor): bool
    {
        // فقط المدير يمكنه حذف الأطباء
        return $user->hasRole('admin');
    }
}