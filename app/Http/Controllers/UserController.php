<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['patient', 'doctor', 'admin'])],
            'specialization' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role === 'doctor')],
            'license_number' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role === 'doctor')],
            // **التعديل الجديد هنا:** إضافة date_of_birth بشكل شرطي
            'date_of_birth' => ['nullable', 'date', Rule::requiredIf($request->role === 'patient')],
            // يمكنك إضافة حقول أخرى خاصة بالمريض مثل 'gender' هنا
            // 'gender' => ['nullable', 'string', Rule::in(['male', 'female']), Rule::requiredIf($request->role === 'patient')],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);

                if ($user->role === 'patient') {
                    $patient = new Patient();
                    $patient->user_id = $user->id;
                    $patient->name = $user->name;
                    // **التعديل الجديد هنا:** تعبئة حقل date_of_birth
                    $patient->date_of_birth = $request->date_of_birth;
                    // $patient->gender = $request->gender ?? null; // مثال لحقل آخر
                    $patient->save();
                } elseif ($user->role === 'doctor') {
                    $doctor = new Doctor();
                    $doctor->user_id = $user->id;
                    $doctor->name = $user->name;
                    $doctor->specialization = $request->specialization;
                    $doctor->license_number = $request->license_number;
                    $doctor->save();
                }
            });

            return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['patient', 'doctor', 'admin'])],
            'specialization' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role === 'doctor')],
            'license_number' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role === 'doctor')],
            // **التعديل الجديد هنا:** إضافة date_of_birth بشكل شرطي للتحديث
            'date_of_birth' => ['nullable', 'date', Rule::requiredIf($request->role === 'patient')],
            // 'gender' => ['nullable', 'string', Rule::in(['male', 'female']), Rule::requiredIf($request->role === 'patient')],
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $oldRole = $user->role;

                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                $user->role = $request->role;
                $user->save();

                if ($oldRole !== $user->role) {
                    if ($oldRole === 'patient' && $user->patient) {
                        $user->patient->delete();
                    } elseif ($oldRole === 'doctor' && $user->doctor) {
                        $user->doctor->delete();
                    }

                    if ($user->role === 'patient') {
                        $patient = new Patient();
                        $patient->user_id = $user->id;
                        $patient->name = $user->name;
                        $patient->date_of_birth = $request->date_of_birth; // تعبئة date_of_birth
                        // $patient->gender = $request->gender ?? null;
                        $patient->save();
                    } elseif ($user->role === 'doctor') {
                        $doctor = new Doctor();
                        $doctor->user_id = $user->id;
                        $doctor->name = $user->name;
                        $doctor->specialization = $request->specialization;
                        $doctor->license_number = $request->license_number;
                        $doctor->save();
                    }
                } else {
                    if ($user->role === 'patient' && $user->patient) {
                        $user->patient->name = $user->name;
                        $user->patient->date_of_birth = $request->date_of_birth; // تحديث date_of_birth
                        // $user->patient->gender = $request->gender ?? null;
                        $user->patient->save();
                    } elseif ($user->role === 'doctor' && $user->doctor) {
                        $user->doctor->name = $user->name;
                        $user->doctor->specialization = $request->specialization;
                        $user->doctor->license_number = $request->license_number;
                        $user->doctor->save();
                    }
                }
            });

            return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح!');
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == '23000') {
                return redirect()->back()->with('error', 'لا يمكن حذف هذا المستخدم لارتباطه ببيانات أخرى (مثل المواعيد أو السجلات).');
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بيانات المستخدم: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                if ($user->role === 'patient' && $user->patient) {
                    $user->patient->delete();
                } elseif ($user->role === 'doctor' && $user->doctor) {
                    $user->doctor->delete();
                }

                $user->delete();
            });

            return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح!');
        } catch (\Exception | \Illuminate\Database\QueryException $e) {
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == '23000') {
                return redirect()->back()->with('error', 'لا يمكن حذف هذا المستخدم لارتباطه ببيانات أخرى (مثل المواعيد أو السجلات).');
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage());
        }
    }
}