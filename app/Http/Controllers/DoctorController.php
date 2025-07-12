<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User; 
use App\Models\Patient; 
use App\Models\AvailableSlot; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
        use AuthorizesRequests; 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function showDoctorAvailableSlots(Doctor $doctor)
    {
        $user = Auth::user();
        $query = AvailableSlot::where('doctor_id', $doctor->id)
                            ->where('start_time', '>', now()); 

        if ($user && $user->hasRole('admin')) {
        } elseif ($user && $user->hasRole('doctor')) {
            if ($user->id !== $doctor->user_id) {
                return redirect()->back()->with('error', 'ليس لديك صلاحية لعرض مواعيد طبيب آخر.');
            }
        } else {
            $query->where('status', 'available'); 
        }

        $availableSlots = $query->orderBy('start_time')->paginate(10);

        return view('doctors.available_slots', compact('doctor', 'availableSlots'));
    }
    
    public function index()
    {
        $this->authorize('viewAny', Doctor::class); 

        $doctors = Doctor::with('user')->get();
        return view('doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Doctor::class);

        return view('doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Doctor::class);

        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_password_confirmation' => ['required', 'string', 'min:8'],
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:doctors,license_number'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'doctor', 
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return redirect()->route('doctors.index')->with('success', 'تم إضافة الطبيب بنجاح!');
        } catch (\Exception $e) {
            if (isset($user)) {
                $user->delete();
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الطبيب: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        $this->authorize('view', $doctor);

        $doctor->load('user');
        $user = $doctor->user;
        return view('doctors.show', compact('doctor', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $doctor->load('user');
        $user = $doctor->user;
        return view('doctors.edit', compact('doctor', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($doctor->user_id),
            ],
            'user_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'user_password_confirmation' => ['nullable', 'string', 'min:8'],
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('doctors', 'license_number')->ignore($doctor->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $user = $doctor->user;
            $user->name = $request->user_name;
            $user->email = $request->user_email;
            if ($request->filled('user_password')) {
                $user->password = Hash::make($request->user_password);
            }
            $user->save();

            $doctor->name = $request->name; 
            $doctor->specialization = $request->specialization;
            $doctor->license_number = $request->license_number;
            $doctor->phone = $request->phone;
            $doctor->address = $request->address;
            $doctor->save();

            return redirect()->route('doctors.index')->with('success', 'تم تحديث بيانات الطبيب بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث بيانات الطبيب: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        $this->authorize('delete', $doctor);

        try {
            $user = $doctor->user;
            $doctor->delete();
            if ($user) {
                $user->delete();
            }

            return redirect()->route('doctors.index')->with('success', 'تم حذف الطبيب بنجاح!');
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == '23000') {
                return redirect()->back()->with('error', 'لا يمكن حذف هذا الطبيب لارتباطه ببيانات أخرى (مثل المواعيد أو الفتحات المتاحة).');
            }
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء حذف الطبيب: ' . $e->getMessage());
        }
    }

    /**
     * Custom method to view patients associated with the logged-in doctor.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewMyPatients()
    {
        if (!Auth::user()->hasRole('doctor')) {
            abort(403, 'Unauthorized. Only doctors can view their patients.');
        }

        $myPatients = Patient::whereHas('appointments', function ($query) {
            $query->where('doctor_id', Auth::user()->doctor->id);
        })->orWhereHas('medicalRecords', function ($query) {
            $query->where('doctor_id', Auth::user()->doctor->id);
        })->get();

        return view('doctor.my_patients', compact('myPatients'));
    }
}
