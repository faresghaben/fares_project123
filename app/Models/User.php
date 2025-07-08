<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail; 

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <--- تأكد أن هذه Traits موجودة هنا

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the appointments associated with the user as a patient.
     * علاقة المستخدم (المريض) مع المواعيد.
     * المستخدم يمكن أن يكون لديه عدة مواعيد (One-to-Many: User has many Appointments)
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Get the doctor record associated with the user.
     * علاقة المستخدم كـ طبيب (إذا كان لديك جدول Doctors يرتبط بـ User).
     * المستخدم يمكن أن يكون لديه صف واحد في جدول Doctors (One-to-One: User has one Doctor)
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    /**
     * Get the patient record associated with the user.
     * علاقة المستخدم كـ مريض (إذا كان لديك جدول Patients يرتبط بـ User).
     * المستخدم يمكن أن يكون لديه صف واحد في جدول Patients (One-to-One: User has one Patient)
     */
    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id');
    }

    /**
     * Check if the user has a specific role.
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}