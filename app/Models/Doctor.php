<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'specialization',
        'license_number',
        'phone',
        'address',
    ];

    // ----------------------------------------------------
    // تعريف العلاقات (Relationships)
    // ----------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function availableSlots()
    {
        return $this->hasMany(AvailableSlot::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}