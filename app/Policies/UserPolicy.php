<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     * (تحدد ما إذا كان المستخدم يمكنه رؤية قائمة بجميع المستخدمين).
     */
    public function viewAny(User $user): bool
    {
        // فقط المدير يمكنه رؤية قائمة بجميع المستخدمين
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // المدير يمكنه رؤية أي مستخدم
        if ($user->hasRole('admin')) {
            return true;
        }
        // المستخدم يمكنه رؤية ملفه الشخصي فقط (إذا كان ذلك مطلوبًا)
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // فقط المدير يمكنه إنشاء مستخدمين جدد
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // المدير يمكنه تحديث أي مستخدم
        if ($user->hasRole('admin')) {
            return true;
        }
        // المستخدم يمكنه تحديث ملفه الشخصي فقط (إذا كان ذلك مطلوبًا)
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // فقط المدير يمكنه حذف المستخدمين
        // وتأكد أن المستخدم لا يستطيع حذف نفسه
        return $user->hasRole('admin') && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}