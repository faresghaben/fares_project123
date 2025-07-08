<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User; 
use App\Models\Patient; 
use App\Models\AvailableSlot; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\Rule; // لاستخدام Rule في التحقق من فرادة البريد الإلكتروني
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <--- أضف هذا السطر

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
                            ->where('start_time', '>', now()); // فقط الفتحات المستقبلية

        // منطق الفلترة بناءً على الدور (يمكن تبسيطه هنا)
        if ($user && $user->hasRole('admin')) {
            // المسؤول يرى كل الفتحات للطبيب المحدد
            // لا نطبق is_booked هنا لأن صفحة الإدارة قد تحتاج لرؤية كل شيء
        } elseif ($user && $user->hasRole('doctor')) {
            // الطبيب يرى فتحاته الخاصة فقط (يجب أن يكون هو نفس الطبيب المعني بالصفحة)
            if ($user->id !== $doctor->user_id) {
                return redirect()->back()->with('error', 'ليس لديك صلاحية لعرض مواعيد طبيب آخر.');
            }
            // هنا لا نطبق is_booked=false لأن الطبيب يدير كل فتحاته
        } else {
            // الزوار والمرضى يرون فقط الفتحات المتاحة للحجز لهذا الطبيب
            $query->where('status', 'available'); // استخدم 'status' بدلاً من 'is_booked' كما اتفقنا
            // أو $query->where('is_booked', false); إذا لم تكن قد غيرت اسم العمود
        }

        $availableSlots = $query->orderBy('start_time')->paginate(10);

        return view('doctors.available_slots', compact('doctor', 'availableSlots'));
    }
    
    public function index()
    {
        // 1. استخدام Policy للتحقق من صلاحية عرض قائمة الأطباء (viewAny)
        // هذا سيتحقق من DoctorPolicy@viewAny
        $this->authorize('viewAny', Doctor::class); // تمرير اسم الكلاس لأنها ليست كائناً محدداً

        // استعراض جميع الأطباء مع تحميل بيانات المستخدم المرتبط بهم
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
        // 2. استخدام Policy للتحقق من صلاحية إنشاء طبيب جديد (create)
        // هذا سيتحقق من DoctorPolicy@create
        $this->authorize('create', Doctor::class);

        // عرض نموذج إضافة طبيب جديد
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
        // 3. استخدام Policy للتحقق من صلاحية حفظ طبيب جديد (create)
        // هذا سيتحقق من DoctorPolicy@create
        $this->authorize('create', Doctor::class);

        // 1. التحقق من صحة بيانات المستخدم (User) قبل إنشاء الطبيب
        $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_password_confirmation' => ['required', 'string', 'min:8'],
            // 2. التحقق من صحة بيانات الطبيب (Doctor)
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:doctors,license_number'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // إنشاء مستخدم جديد أولاً
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'doctor', // تعيين الدور كطبيب
            ]);

            // إنشاء الطبيب وربطه بالمستخدم الذي تم إنشاؤه
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
        // 4. استخدام Policy للتحقق من صلاحية عرض ملف طبيب معين (view)
        // هذا سيتحقق من DoctorPolicy@view ويمرر كائن الطبيب المحدد
        $this->authorize('view', $doctor);

        // عرض تفاصيل طبيب محدد
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
        // 5. استخدام Policy للتحقق من صلاحية تعديل بيانات طبيب معين (update)
        // هذا سيتحقق من DoctorPolicy@update
        $this->authorize('update', $doctor);

        // عرض نموذج تعديل بيانات طبيب محدد
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
        // 6. استخدام Policy للتحقق من صلاحية تحديث بيانات طبيب معين (update)
        // هذا سيتحقق من DoctorPolicy@update
        $this->authorize('update', $doctor);

        // 1. التحقق من صحة بيانات المستخدم (User)
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
            // 2. التحقق من صحة بيانات الطبيب (Doctor)
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
            // تحديث بيانات المستخدم المرتبط أولاً
            $user = $doctor->user;
            $user->name = $request->user_name;
            $user->email = $request->user_email;
            if ($request->filled('user_password')) {
                $user->password = Hash::make($request->user_password);
            }
            $user->save();

            // تحديث بيانات الطبيب
            $doctor->name = $request->name; // تحديث حقل الاسم في جدول الأطباء
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
        // 7. استخدام Policy للتحقق من صلاحية حذف طبيب معين (delete)
        // هذا سيتحقق من DoctorPolicy@delete
        $this->authorize('delete', $doctor);

        try {
            // عند حذف الطبيب، يفضل حذف المستخدم المرتبط به أيضًا
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
        // لا نحتاج لـ Policy هنا بالضرورة لأننا سنقيد الاستعلام بالأسفل
        // ولكن يمكن استخدام Policy أيضاً إذا أردت التحقق من أن المستخدم طبيب أولاً.
        if (!Auth::user()->hasRole('doctor')) {
            abort(403, 'Unauthorized. Only doctors can view their patients.');
        }

        // جلب المرضى المرتبطين بهذا الطبيب (عبر المواعيد أو السجلات الطبية)
        $myPatients = Patient::whereHas('appointments', function ($query) {
            $query->where('doctor_id', Auth::user()->doctor->id);
        })->orWhereHas('medicalRecords', function ($query) {
            $query->where('doctor_id', Auth::user()->doctor->id);
        })->get();

        return view('doctor.my_patients', compact('myPatients'));
    }
}