<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AvailableSlot;
use Illuminate\Auth\Access\Response;

class AvailableSlotPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // المسؤولون يمكنهم رؤية جميع الفتحات المتاحة.
        // الأطباء يمكنهم رؤية جميع الفتحات المتاحة (بما في ذلك الخاصة بهم).
        // المرضى يمكنهم رؤية جميع الفتحات المتاحة.
        return $user->role === 'admin' || $user->role === 'doctor' || $user->role === 'patient'
            ? Response::allow()
            : Response::deny('ليس لديك صلاحية لعرض قائمة المواعيد المتاحة.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AvailableSlot  $availableSlot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AvailableSlot $availableSlot)
    {
        // المسؤولون يمكنهم عرض أي فتحة متاحة.
        // الأطباء يمكنهم عرض الفتحات الخاصة بهم فقط.
        // المرضى يمكنهم عرض أي فتحة متاحة.
        if ($user->role === 'admin' || $user->role === 'patient') {
            return Response::allow();
        }

        // الطبيب يمكنه عرض الفتحة إذا كانت خاصة به
        if ($user->role === 'doctor') {
            return $user->id === $availableSlot->doctor->user_id
                ? Response::allow()
                : Response::deny('ليس لديك صلاحية لعرض هذا الموعد المتاح.');
        }

        return Response::deny('ليس لديك صلاحية لعرض هذا الموعد المتاح.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // المسؤولون يمكنهم إنشاء فتحات متاحة.
        // الأطباء يمكنهم إنشاء فتحات متاحة (لأنفسهم).
        return $user->role === 'admin' || $user->role === 'doctor'
            ? Response::allow()
            : Response::deny('ليس لديك صلاحية لإنشاء موعد متاح.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AvailableSlot  $availableSlot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AvailableSlot $availableSlot)
    {
        // المسؤولون يمكنهم تعديل أي فتحة متاحة.
        // الأطباء يمكنهم تعديل الفتحات الخاصة بهم فقط.
        if ($user->role === 'admin') {
            return Response::allow();
        }

        // الطبيب يمكنه التعديل إذا كانت الفتحة خاصة به
        if ($user->role === 'doctor') {
            return $user->id === $availableSlot->doctor->user_id
                ? Response::allow()
                : Response::deny('ليس لديك صلاحية لتعديل هذا الموعد المتاح.');
        }

        return Response::deny('ليس لديك صلاحية لتعديل هذا الموعد المتاح.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AvailableSlot  $availableSlot
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AvailableSlot $availableSlot)
    {
        // المسؤولون يمكنهم حذف أي فتحة متاحة.
        // الأطباء يمكنهم حذف الفتحات الخاصة بهم فقط.
        if ($user->role === 'admin') {
            return Response::allow();
        }

        // الطبيب يمكنه الحذف إذا كانت الفتحة خاصة به
        if ($user->role === 'doctor') {
            return $user->id === $availableSlot->doctor->user_id
                ? Response::allow()
                : Response::deny('ليس لديك صلاحية لحذف هذا الموعد المتاح.');
        }

        return Response::deny('ليس لديك صلاحية لحذف هذا الموعد المتاح.');
    }
}