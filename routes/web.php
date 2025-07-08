<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\AvailableSlotController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('patients', PatientController::class);
Route::resource('doctors', DoctorController::class);
Route::resource('medical-records', MedicalRecordController::class);
Route::resource('available-slots', AvailableSlotController::class);
Route::resource('appointments', AppointmentController::class);
Route::resource('users', UserController::class);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // المريض يعرض المواعيد المتاحة للحجز (الآن أي مستخدم مسجل دخول يمكنه الوصول)
    Route::get('/book-appointment', [AvailableSlotController::class, 'index'])->name('book.appointment');

    // **هذا السطر سيتم حذفه أو تعليقه لأنه يشير إلى AvailableSlotController**
    // Route::post('/available-slots/{available_slot}/book', [AvailableSlotController::class, 'book'])->name('available-slots.book');

    // **المسار الجديد:** لعرض المواعيد المتاحة لطبيب معين
    Route::get('appointments/available/{doctor}', [AppointmentController::class, 'showAvailableForDoctor'])->name('appointments.availableForDoctor');

    // مسار حجز موعد (هذا المسار الذي يجب أن يشير إلى AppointmentController@book)
    // لا داعي لهذا المسار إذا كنت تستخدم المسار أدناه، لأنه يتعارض معه
    // Route::post('appointments/{appointment}/book', [AppointmentController::class, 'book'])->name('appointments.book');

    Route::get('/doctors/{doctor}/available-slots', [DoctorController::class, 'showDoctorAvailableSlots'])->name('doctors.available_slots');

    // هذا هو المسار الوحيد الذي يجب أن يكون مسؤولاً عن حجز الموعد
    Route::post('/available-slots/{availableSlot}/book', [AppointmentController::class, 'book'])->name('available_slots.book');
    // ->middleware('auth', 'role:patient'); // تأكد أن المريض فقط يمكنه الحجز وهو مسجل دخول
});

require __DIR__.'/auth.php';