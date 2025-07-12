<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $medicalRecords = MedicalRecord::with(['patient', 'doctor']);

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('patient')) {
                if ($user->patient) {
                    $medicalRecords->where('patient_id', $user->patient->id);
                } else {
                    $medicalRecords->where('id', null);
                }
            } elseif ($user->hasRole('doctor')) {
                if ($user->doctor) {
                    $medicalRecords->where('doctor_id', $user->doctor->id);
                } else {
                    $medicalRecords->where('id', null);
                }
            }
        } else {
            $medicalRecords->where('id', null); 
        }

        $medicalRecords = $medicalRecords->get();

        return view('medical_records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MedicalRecord::class);

        $patients = Patient::all();
        $doctors = Doctor::all();  
        return view('medical_records.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MedicalRecord::class);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'record_date' => 'required|date',
        ]);

        MedicalRecord::create($request->all());

        return redirect()->route('medical-records.index')
            ->with('success', 'تم إضافة السجل الطبي بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);

        $medicalRecord->load('patient', 'doctor');

        return view('medical_records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        $patients = Patient::all();
        $doctors = Doctor::all();
        $medicalRecord->load('patient', 'doctor');
        return view('medical_records.edit', compact('medicalRecord', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'record_date' => 'required|date',
        ]);

        $medicalRecord->update($request->all());

        return redirect()->route('medical-records.index')
                        ->with('success', 'تم تحديث السجل الطبي بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
                        ->with('success', 'تم حذف السجل الطبي بنجاح.');
    }
}
