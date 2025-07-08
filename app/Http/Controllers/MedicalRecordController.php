<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth; // <-- تأكد من وجود هذا السطر

class MedicalRecordController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. استخدام Policy للتحقق من صلاحية عرض قائمة السجلات الطبية (viewAny)
        $this->authorize('viewAny', MedicalRecord::class);

        // ابدأ في بناء الاستعلام مع تحميل العلاقات المشتركة
        $medicalRecords = MedicalRecord::with(['patient', 'doctor']);

        // تطبيق منطق التصفية بناءً على دور المستخدم المسجل الدخول
        if (Auth::check()) { // تأكد أن هناك مستخدم مسجل دخول
            $user = Auth::user();

            if ($user->hasRole('patient')) {
                // إذا كان المستخدم مريضًا، اعرض السجلات الطبية الخاصة به فقط
                if ($user->patient) { // تأكد أن المستخدم لديه سجل مريض مرتبط
                    $medicalRecords->where('patient_id', $user->patient->id);
                } else {
                    // إذا كان دوره 'patient' لكن لا يوجد سجل patient مرتبط، فلا تعرض شيئًا
                    $medicalRecords->where('id', null); // لضمان عدم إرجاع أي سجلات
                }
            } elseif ($user->hasRole('doctor')) {
                // إذا كان المستخدم طبيباً، اعرض السجلات الطبية لمرضاه فقط
                if ($user->doctor) { // تأكد أن المستخدم لديه سجل doctor مرتبط
                    $medicalRecords->where('doctor_id', $user->doctor->id);
                } else {
                    // إذا كان دوره 'doctor' لكن لا يوجد سجل doctor مرتبط، فلا تعرض شيئًا
                    $medicalRecords->where('id', null); // لضمان عدم إرجاع أي سجلات
                }
            }
            // للمسؤول (admin) أو الأدوار الأخرى، لن نضيف أي شروط تصفية
            // مما يعني أنهم سيرون جميع السجلات بشكل افتراضي
        } else {
            // إذا لم يكن هناك مستخدم مسجل دخول، فلا تعرض أي سجلات طبية
            // هذا السطر قد لا يكون ضرورياً جداً إذا كانت السياسة تفرض الدخول.
            $medicalRecords->where('id', null); // لضمان عدم إرجاع أي سجلات لغير المسجلين دخول
        }

        // جلب السجلات بعد تطبيق الشروط
        $medicalRecords = $medicalRecords->get();

        return view('medical_records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 2. استخدام Policy للتحقق من صلاحية إنشاء سجل طبي جديد (create)
        // هذا سيتحقق من MedicalRecordPolicy@create
        $this->authorize('create', MedicalRecord::class);

        $patients = Patient::all(); // جلب جميع المرضى
        $doctors = Doctor::all();   // جلب جميع الأطباء
        return view('medical_records.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 3. استخدام Policy للتحقق من صلاحية تخزين سجل طبي جديد (create)
        // هذا سيتحقق من MedicalRecordPolicy@create
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
        // 4. استخدام Policy للتحقق من صلاحية عرض سجل طبي معين (view)
        // هذا سيتحقق من MedicalRecordPolicy@view
        $this->authorize('view', $medicalRecord);

        // تحميل العلاقات
        $medicalRecord->load('patient', 'doctor');

        return view('medical_records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        // 5. استخدام Policy للتحقق من صلاحية عرض نموذج تعديل سجل طبي معين (update)
        // هذا سيتحقق من MedicalRecordPolicy@update
        $this->authorize('update', $medicalRecord);

        $patients = Patient::all();
        $doctors = Doctor::all();
        // تحميل العلاقات قبل إرسالها إلى الـ view
        $medicalRecord->load('patient', 'doctor');
        return view('medical_records.edit', compact('medicalRecord', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // 6. استخدام Policy للتحقق من صلاحية تحديث سجل طبي معين (update)
        // هذا سيتحقق من MedicalRecordPolicy@update
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
        // 7. استخدام Policy للتحقق من صلاحية حذف سجل طبي معين (delete)
        // هذا سيتحقق من MedicalRecordPolicy@delete
        $this->authorize('delete', $medicalRecord);

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
                        ->with('success', 'تم حذف السجل الطبي بنجاح.');
    }
}