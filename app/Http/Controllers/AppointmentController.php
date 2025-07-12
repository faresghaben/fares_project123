<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AvailableSlot; 
use App\Models\Doctor;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

class AppointmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource (عرض قائمة المواعيد).
     */
    public function index()
    {
        // 1. استخدام Policy للتحقق من صلاحية عرض قائمة المواعيد (viewAny)
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['doctor.user', 'patient'])->orderBy('start_time', 'desc');

        // تطبيق منطق إضافي بناءً على الدور لجلب المواعيد الصحيحة
        if (Auth::check()) { // تأكد أن هناك مستخدم مسجل دخول
            $user = Auth::user();

            if ($user->hasRole('doctor')) {
                $appointments->where('doctor_id', $user->doctor->id);
            } elseif ($user->hasRole('patient')) {
                // المريض يرى مواعيده المجدولة فقط (وليس المتاحة للحجز من هذه القائمة)
                if ($user->patient) { // تأكد أن المستخدم لديه سجل مريض مرتبط
                    $appointments->where('patient_id', $user->patient->id);
                    $appointments->where('status', 'scheduled'); // تأكد أن يرى مواعيده المجدولة فقط

                    // إذا اختار المريض طبيباً، قم بتصفية المواعيد لتظهر مواعيد هذا الطبيب فقط
                    if ($user->patient->chosenDoctor) {
                        $appointments->where('doctor_id', $user->patient->chosenDoctor->id);
                    }
                } else {
                    // إذا كان دوره 'patient' لكن لا يوجد سجل patient مرتبط، فلا تعرض شيئًا
                    $appointments->where('id', null);
                }
            }
            // المدير سيحصل على جميع المواعيد بحكم عدم وجود قيود هنا
        } else {
            // إذا لم يكن هناك مستخدم مسجل دخول، فلا تعرض أي مواعيد (أو قم بإعادة توجيههم)
            $appointments->where('id', null);
        }

        $appointments = $appointments->paginate(10);
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the available appointments for booking by patients.
     * دالة لعرض المواعيد المتاحة للحجز للمرضى
     */
    public function showAvailable()
    {
        // هنا نضمن أن المريض فقط هو من يمكنه رؤية هذه الصفحة
        if (!Auth::user()->hasRole('patient')) {
            abort(403, 'Unauthorized access.');
        }

        $availableAppointments = Appointment::with('doctor.user')
                                ->where('status', 'available')
                                ->whereNull('patient_id') // تأكد أنها غير محجوزة
                                ->where('start_time', '>', now()) // فقط المواعيد المستقبلية
                                ->orderBy('start_time');

        // إضافة التصفية للطبيب المختار في المواعيد المتاحة
        if (Auth::user()->patient && Auth::user()->patient->chosenDoctor) {
            $availableAppointments->where('doctor_id', Auth::user()->patient->chosenDoctor->id);
        }

        $availableAppointments = $availableAppointments->paginate(10);

        return view('appointments.available', compact('availableAppointments')); // سنحتاج لإنشاء هذا الـ view
    }

    /**
     * Show available appointments for a specific doctor.
     * لعرض المواعيد المتاحة لطبيب معين (يستخدم من صفحة قائمة الأطباء).
     */
    public function showAvailableForDoctor(Doctor $doctor)
    {
        // تأكد أن المستخدم مريض ومسجل دخول
        if (!Auth::check() || !Auth::user()->hasRole('patient')) {
            abort(403, 'Unauthorized action.');
        }

        // جلب المواعيد المتاحة للطبيب المحدد فقط
        $availableAppointments = Appointment::with('doctor.user')
                                ->where('doctor_id', $doctor->id) // فلترة حسب الطبيب المحدد
                                ->where('status', 'available')
                                ->whereNull('patient_id')
                                ->where('start_time', '>', now())
                                ->orderBy('start_time')
                                ->paginate(10);


        return view('appointments.available_for_doctor', compact('availableAppointments', 'doctor'));
    }

    /**
     * Handle the booking of an available appointment by a patient.
     * دالة لمعالجة حجز موعد من قبل المريض
     */
    public function book(Request $request, AvailableSlot $availableSlot)
    {
        $user = Auth::user();

        // 1. تحقق من أن المستخدم مريض ومسجل دخول
        if (!$user || !$user->hasRole('patient')) {
            abort(403, 'يجب أن تكون مريضًا مسجلاً للدخول لحجز موعد.');
        }

        // 2. تحقق من صلاحية الفتحة للحجز (متاحة وفي المستقبل)
        if ($availableSlot->status !== 'available') { // استخدم status الخاص بـ AvailableSlot
            return redirect()->back()->with('error', 'هذا الموعد غير متاح للحجز أو قد تم حجزه بالفعل.');
        }

        if ($availableSlot->start_time <= now()) { //
            return redirect()->back()->with('error', 'لا يمكن حجز موعد في الماضي أو موعد بدأ بالفعل.');
        }

        try {
            // 3. إنشاء سجل جديد في جدول Appointments (هذا هو الحجز الفعلي)
            $appointment = Appointment::create([
                'patient_id' => $user->patient->id, //
                'doctor_id' => $availableSlot->doctor_id, // اربطها بالطبيب من الفتحة المتاحة
                'start_time' => $availableSlot->start_time, //
                'end_time' => $availableSlot->end_time, //
                'status' => 'scheduled', // حالة الموعد المحجوز الجديد
                // إذا كان لديك عمود available_slot_id في جدول appointments لربطهما:
                // 'available_slot_id' => $availableSlot->id,
            ]);

            // 4. تحديث حالة الفتحة المتاحة في جدول available_slots إلى 'booked'
            $availableSlot->status = 'booked';
            $availableSlot->save();

            // dd($availableSlot->fresh()->status);
            // 5. إعادة التوجيه إلى نفس صفحة المواعيد المتاحة للطبيب مع رسالة نجاح
            return redirect()->route('doctors.available_slots', $availableSlot->doctor_id)
                            ->with('success', 'تم حجز موعدك بنجاح! رقم الموعد: ' . $appointment->id);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حجز الموعد: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for creating a new resource (عرض نموذج إضافة موعد جديد).
     */
    public function create()
    {
        // 2. استخدام Policy للتحقق من صلاحية إنشاء موعد جديد (create)
        $this->authorize('create', Appointment::class);

        // جلب جميع الأطباء مع بيانات المستخدم الخاصة بهم
        $doctors = Doctor::with('user')->get();

        // جلب جميع المرضى
        // إذا كان المستخدم طبيباً ويريد إنشاء موعد لمرضاه، قد تحتاج لجلب مرضاه فقط
        if (Auth::user()->hasRole('doctor')) {
            // هذا الجزء يعتمد على كيفية ربط المريض بالطبيب
            // إذا كان الطبيب يمكنه رؤية مرضاه فقط، فيمكنك تقييد الاستعلام هنا
            // تأكد أن لديك علاقة patients في موديل Doctor
            if (Auth::user()->doctor && Auth::user()->doctor->patients) {
                $patients = Auth::user()->doctor->patients;
            } else {
                $patients = collect(); // إرجاع مجموعة فارغة إذا لم يكن هناك مرضى مرتبطين
            }
        } else {
            $patients = Patient::all(); // للمدير
        }

        return view('appointments.create', compact('doctors', 'patients'));
    }

    /**
     * Store a newly created resource in storage (حفظ موعد جديد).
     */
    public function store(Request $request)
    {
        // 3. استخدام Policy للتحقق من صلاحية حفظ موعد جديد (create)
        $this->authorize('create', Appointment::class);

        // قواعد التحقق (Validation) من البيانات المدخلة
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => ['nullable', 'exists:patients,id', Rule::requiredIf($request->status === 'scheduled')],
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'status' => ['required', 'string', Rule::in(['booked', 'available'])], 
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        // إذا كان المستخدم طبيباً، تأكد من أن الموعد يتم إنشاؤه له أو لمرضاه
        if (Auth::user()->hasRole('doctor')) {
            if ($request->doctor_id !== Auth::user()->doctor->id) {
                abort(403, 'You can only create appointments for yourself.');
            }
            // إذا كان الطبيب ينشئ موعداً 'available'، يجب أن يكون patient_id فارغاً
            if ($request->status === 'available' && $request->filled('patient_id')) {
                return redirect()->back()->withInput()->with('error', 'الموعد المتاح لا يمكن أن يكون له مريض محدد.');
            }
        }

        // تحديث بيانات الموعد
        Appointment::create($request->all());

        return redirect()->route('appointments.index')->with('success', 'تم إضافة الموعد بنجاح.');
    }

    /**
     * Display the specified resource (عرض تفاصيل موعد).
     */
    public function show(Appointment $appointment)
    {
        // 4. استخدام Policy للتحقق من صلاحية عرض موعد معين (view)
        $this->authorize('view', $appointment);

        // تحميل العلاقات قبل إرسالها إلى الـ view
        $appointment->load(['doctor.user', 'patient']);
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource (عرض نموذج تعديل موعد).
     */
    public function edit(Appointment $appointment)
    {
        // 5. استخدام Policy للتحقق من صلاحية تعديل موعد معين (update)
        $this->authorize('update', $appointment);

        $doctors = Doctor::with('user')->get();
        // جلب المرضى بناءً على الدور كما في create
        if (Auth::user()->hasRole('doctor')) {
            if (Auth::user()->doctor && Auth::user()->doctor->patients) {
                $patients = Auth::user()->doctor->patients;
            } else {
                $patients = collect();
            }
        } else {
            $patients = Patient::all();
        }

        $appointment->load(['doctor.user', 'patient']);
        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    /**
     * Update the specified resource in storage (تحديث موعد).
     */
    public function update(Request $request, Appointment $appointment)
    {
        // 6. استخدام Policy للتحقق من صلاحية تحديث موعد معين (update)
        $this->authorize('update', $appointment);

        // قواعد التحقق (Validation) من البيانات المدخلة للتعديل
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => ['nullable', 'exists:patients,id', Rule::requiredIf($request->status === 'scheduled')], // يمكن أن يكون null إذا كانت الحالة 'available'
            'start_time' => 'required|date|after_or_equal:now',
            'end_time' => 'required|date|after:start_time',
            'status' => ['required', 'string', Rule::in(['scheduled', 'completed', 'canceled', 'available'])], // إضافة 'available'
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        // إذا كان يتم تحديث موعد ليصبح 'available' يجب أن يكون patient_id null
        if ($request->status === 'available' && $request->filled('patient_id')) {
            return redirect()->back()->withInput()->with('error', 'الموعد المتاح لا يمكن أن يكون له مريض محدد.');
        }
        // إذا كان الموعد يتم تحديثه ليصبح 'scheduled' فيجب أن يكون له patient_id
        if ($request->status === 'scheduled' && !$request->filled('patient_id')) {
            return redirect()->back()->withInput()->with('error', 'الموعد المجدول يجب أن يكون له مريض محدد.');
        }


        $appointment->update($request->all());

        return redirect()->route('appointments.index')->with('success', 'تم تحديث الموعد بنجاح.');
    }

    /**
     * Remove the specified resource from storage (حذف موعد).
     */
    public function destroy(Appointment $appointment)
    {
        // 7. استخدام Policy للتحقق من صلاحية حذف موعد معين (delete)
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'تم حذف الموعد بنجاح.');
    }
}
