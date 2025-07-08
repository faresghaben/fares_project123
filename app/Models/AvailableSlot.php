<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        // 'is_available',
        'status',

    ];

    protected $casts = [
        'start_time' => 'datetime', // يمكن أن يكون 'time' أو 'datetime' حسب طريقة تخزينك
        'end_time' => 'datetime',   // يمكن أن يكون 'time' أو 'datetime' حسب طريقة تخزينك
        // 'is_available' => 'boolean',
    ];

    // ----------------------------------------------------
    // تعريف العلاقات (Relationships)
    // ----------------------------------------------------

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}