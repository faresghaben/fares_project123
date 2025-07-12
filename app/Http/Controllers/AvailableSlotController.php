<?php

namespace App\Http\Controllers;

use App\Models\AvailableSlot;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AvailableSlotController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the available slots based on user role.
     * This method assumes AvailableSlot represents a specific date/time slot with an 'is_booked' status.
     * If AvailableSlot represents recurring patterns, this logic needs significant change.
     */
// app/Http/Controllers/AvailableSlotController.php

public function index()
{
    $user = Auth::user();

    $query = AvailableSlot::with('doctor.user')
                            ->where('start_time', '>', now()); 

    if ($user->hasRole('admin')) {

        $query->where('status', 'available'); 
    } elseif ($user->hasRole('doctor')) {
        $doctor = $user->doctor;
        if (!$doctor) {
            return redirect()->back()->with('error', 'حساب الطبيب الخاص بك غير مكتمل.');
        }
        $query->where('doctor_id', $doctor->id);
        // الأطباء يرون كل فتحاتهم (متاحة أو محجوزة) في صفحة إدارتهم،
        // ولكن إذا كانت هذه الصفحة مخصصة للمواعيد المتاحة فقط،
        // فيمكنك إضافة $query->where('status', 'available'); هنا أيضاً إذا أردت.
        // حالياً، الكود سيعرض كل فتحات الطبيب بغض النظر عن حالتها في هذه الجزئية، وهذا قد يكون منطقياً للطبيب.
    } elseif ($user->hasRole('patient')) {
        // Patient sees only unbooked future slots for all doctors
        $query->where('status', 'available'); 
    } else {

        $query->where('status', 'available'); 
    }

    $availableSlots = $query->orderBy('start_time')->paginate(10);

    return view('available_slots.index', compact('availableSlots'));
}
    /**
     * Book an available slot by a Patient.
     * This method assumes AvailableSlot represents a specific date/time instance.
     * It relies on the 'is_booked' field on the AvailableSlot model.
     */
    public function book(Request $request, AvailableSlot $available_slot)
    {
        if (!Auth::user()->hasRole('patient')) {
            abort(403, 'You are not authorized to book appointments.');
        }

        if ($available_slot->is_booked || $available_slot->start_time <= now()) {
            return redirect()->back()->with('error', 'هذا الموعد غير متاح للحجز أو قد تم حجزه بالفعل.');
        }

        try {
            \App\Models\Appointment::create([ 
                'patient_id' => Auth::user()->patient->id, 
                'doctor_id' => $available_slot->doctor_id,
                'start_time' => $available_slot->start_time,
                'end_time' => $available_slot->end_time,
                'status' => 'scheduled', 
            ]);

            $available_slot->update(['is_booked' => true]);

            return redirect()->route('appointments.index')->with('success', 'تم حجز الموعد بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حجز الموعد: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource (for Doctors/Admins to define availability).
     * This method seems to manage recurring availability patterns (day_of_week, time-only).
     * This implies a different structure for AvailableSlot than the 'book' method expects.
     * This needs clarification with the user.
     */
    public function create()
    {
        $this->authorize('create', AvailableSlot::class);

        $user = Auth::user();
        if ($user->hasRole('doctor')) {
            $doctors = Doctor::where('user_id', $user->id)->get();
        } else {
            $doctors = Doctor::all();
        }

        return view('available_slots.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage (Store new available slot).
     * This method seems to manage recurring availability patterns (day_of_week, time-only).
     * This implies a different structure for AvailableSlot than the 'book' method expects.
     * This needs clarification with the user.
     */
    public function store(Request $request)
    {
        $this->authorize('create', AvailableSlot::class);

        $user = Auth::user();

        $rules = [
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean', 
        ];

        if ($user->hasRole('doctor')) {
            $doctor = $user->doctor;
            if (!$doctor || $request->doctor_id !== $doctor->id) {
                return redirect()->back()->withInput()->with('error', 'ليس لديك صلاحية لإنشاء موعد متاح لهذا الطبيب.');
            }
        }

        $request->validate($rules);

        AvailableSlot::create([
            'doctor_id' => $request->doctor_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('available-slots.index')->with('success', 'تم إضافة الموعد المتاح بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AvailableSlot $availableSlot)
    {
        $this->authorize('view', $availableSlot);
        $availableSlot->load('doctor.user');
        return view('available_slots.show', compact('availableSlot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AvailableSlot $availableSlot)
    {
        $this->authorize('update', $availableSlot);

        $user = Auth::user();
        if ($user->hasRole('doctor')) {
            $doctors = Doctor::where('user_id', $user->id)->get();
        } else {
            $doctors = Doctor::all();
        }

        return view('available_slots.edit', compact('availableSlot', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     * This method also seems to manage recurring availability patterns.
     */
    public function update(Request $request, AvailableSlot $availableSlot)
    {
        $this->authorize('update', $availableSlot);

        $user = Auth::user();

        $rules = [
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ];

        if ($user->hasRole('doctor')) {
            $doctor = $user->doctor;
            if (!$doctor || $request->doctor_id !== $doctor->id) {
                return redirect()->back()->withInput()->with('error', 'ليس لديك صلاحية لتعديل موعد متاح لهذا الطبيب.');
            }
        }

        $request->validate($rules);

        $availableSlot->update([
            'doctor_id' => $request->doctor_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->route('available-slots.index')->with('success', 'تم تحديث الموعد المتاح بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvailableSlot $availableSlot)
    {
        $this->authorize('delete', $availableSlot);

        try {
            $availableSlot->delete();
            return redirect()->route('available-slots.index')->with('success', 'تم حذف الموعد المتاح بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الموعد المتاح: ' . $e->getMessage());
        }
    }
}
