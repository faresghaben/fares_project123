<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User; // يجب استيراد موديل User للتعامل مع المستخدمين
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // لتشفير كلمة المرور في Laravel 8
use Illuminate\Validation\Rule; // لاستخدام Rule في التحقق من فرادة البريد الإلكتروني
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <--- أضف هذا السطر
use Illuminate\Support\Facades\Auth; // <-- تأكد من استيراد Auth

class PatientController extends Controller
{
    use AuthorizesRequests; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 1. استخدام Policy للتحقق من صلاحية عرض قائمة المرضى (viewAny)
        // هذا سيتحقق من PatientPolicy@viewAny
        $this->authorize('viewAny', Patient::class); // تمرير اسم الكلاس لأنها ليست كائناً محدداً

        // استعراض جميع المرضى مع تحميل بيانات المستخدم المرتبط بهم
        $patients = Patient::with('user')->get();
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 2. استخدام Policy للتحقق من صلاحية إنشاء مريض جديد (create)
        // هذا سيتحقق من PatientPolicy@create
        $this->authorize('create', Patient::class);

        // عرض نموذج إضافة مريض جديد
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 3. استخدام Policy للتحقق من صلاحية حفظ مريض جديد (create)
        // هذا سيتحقق من PatientPolicy@create
        $this->authorize('create', Patient::class);

        // 1. التحقق من صحة بيانات المستخدم (User) قبل إنشاء المريض
        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' يتحقق من تطابق user_password و user_password_confirmation
            'user_password_confirmation' => ['required', 'string', 'min:8'],
            // 2. التحقق من صحة بيانات المريض (Patient)
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            // إنشاء مستخدم جديد أولاً
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'patient', // تعيين الدور كمريض (هذا يجب أن يتم في مكان واحد فقط)
            ]);

            // إنشاء المريض وربطه بالمستخدم الذي تم إنشاؤه
            Patient::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_type' => $request->blood_type,
                'allergies' => $request->allergies,
            ]);

            return redirect()->route('patients.index')->with('success', 'تم إضافة المريض بنجاح!');
        } catch (\Exception $e) {
            // في حال حدوث خطأ، يجب حذف المستخدم الذي تم إنشاؤه لمنع البيانات اليتيمة
            if (isset($user)) {
                $user->delete();
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة المريض: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        // 4. استخدام Policy للتحقق من صلاحية عرض ملف مريض معين (view)
        // هذا سيتحقق من PatientPolicy@view ويمرر كائن المريض المحدد
        $this->authorize('view', $patient);

        // عرض تفاصيل مريض محدد
        // تأكد من تحميل علاقة المستخدم (user) قبل إرسالها إلى الـ view
        $patient->load('user');
        $user = $patient->user; // تمرير المستخدم المرتبط إلى الـ view
        return view('patients.show', compact('patient', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        // 5. استخدام Policy للتحقق من صلاحية تعديل بيانات مريض معين (update)
        // هذا سيتحقق من PatientPolicy@update
        $this->authorize('update', $patient);

        // عرض نموذج تعديل بيانات مريض محدد
        // تأكد من تحميل علاقة المستخدم (user) قبل إرسالها إلى الـ view
        $patient->load('user');
        $user = $patient->user; // تمرير المستخدم المرتبط إلى الـ view
        return view('patients.edit', compact('patient', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        // 6. استخدام Policy للتحقق من صلاحية تحديث بيانات مريض معين (update)
        // هذا سيتحقق من PatientPolicy@update
        $this->authorize('update', $patient);

        // 1. التحقق من صحة بيانات المستخدم (User)
        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($patient->user_id), // تجاهل البريد الإلكتروني الحالي للمستخدم
            ],
            'user_password' => ['nullable', 'string', 'min:8', 'confirmed'], // كلمة المرور اختيارية عند التعديل
            'user_password_confirmation' => ['nullable', 'string', 'min:8'],
            // 2. التحقق من صحة بيانات المريض (Patient)
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            // تحديث بيانات المستخدم المرتبط أولاً
            $user = $patient->user;
            $user->name = $request->user_name;
            $user->email = $request->user_email;
            if ($request->filled('user_password')) {
                $user->password = Hash::make($request->user_password);
            }
            $user->save();

            // تحديث بيانات المريض
            $patient->name = $request->name; // تحديث حقل الاسم في جدول المرضى
            $patient->date_of_birth = $request->date_of_birth;
            $patient->gender = $request->gender;
            $patient->blood_type = $request->blood_type;
            $patient->allergies = $request->allergies;
            $patient->save();

            return redirect()->route('patients.index')->with('success', 'تم تحديث بيانات المريض بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث بيانات المريض: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        // 7. استخدام Policy للتحقق من صلاحية حذف مريض معين (delete)
        // هذا سيتحقق من PatientPolicy@delete
        $this->authorize('delete', $patient);

        try {
            // عند حذف المريض، يفضل حذف المستخدم المرتبط به أيضًا
            // (أو يمكنك تغيير دور المستخدم إذا كان يستخدم لأغراض أخرى)
            $user = $patient->user; // جلب المستخدم المرتبط
            $patient->delete(); // حذف سجل المريض
            if ($user) { // تأكد من وجود المستخدم قبل حذفه
                $user->delete(); // حذف المستخدم المرتبط
            }

            return redirect()->route('patients.index')->with('success', 'تم حذف المريض بنجاح!');
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            // معالجة خطأ قيود المفتاح الأجنبي (مثلاً إذا كان المريض لديه مواعيد مرتبطة)
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == '23000') {
                return redirect()->back()->with('error', 'لا يمكن حذف هذا المريض لارتباطه ببيانات أخرى (مثل المواعيد).');
            }
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء حذف المريض: ' . $e->getMessage());
        }
    }
}