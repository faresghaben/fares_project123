@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تعديل بيانات الطبيب</h1>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <form action="{{ route('doctors.update', $doctor->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- ضروري لـ Laravel ليتعرف على طلب التحديث --}}

            {{-- 1. حقول بيانات المستخدم المرتبط (User) --}}
            <h5 class="mb-3">بيانات تسجيل الدخول (المستخدم المرتبط)</h5>
            <div class="mb-3">
                <label for="user_name" class="form-label">اسم المستخدم:</label>
                <input type="text" id="user_name" name="user_name" class="form-control"
                    value="{{ old('user_name', $user->name) }}" required>
                @error('user_name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_email" class="form-label">البريد الإلكتروني للمستخدم:</label>
                <input type="email" id="user_email" name="user_email" class="form-control"
                    value="{{ old('user_email', $user->email) }}" required>
                @error('user_email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_password" class="form-label">كلمة المرور (اتركها فارغة لعدم التغيير):</label>
                <input type="password" id="user_password" name="user_password" class="form-control">
                @error('user_password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_password_confirmation" class="form-label">تأكيد كلمة المرور:</label>
                <input type="password" id="user_password_confirmation" name="user_password_confirmation" class="form-control">
            </div>

            <hr class="my-4">

            {{-- 2. حقول بيانات الطبيب (Doctor) --}}
            <h5 class="mb-3">بيانات الطبيب</h5>
            <div class="mb-3">
                <label for="name" class="form-label">اسم الطبيب:</label>
                <input type="text" id="name" name="name" class="form-control"
                    value="{{ old('name', $doctor->name) }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="specialization" class="form-label">التخصص:</label>
                <input type="text" id="specialization" name="specialization" class="form-control"
                    value="{{ old('specialization', $doctor->specialization) }}" required>
                @error('specialization') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="license_number" class="form-label">رقم الترخيص:</label>
                <input type="text" id="license_number" name="license_number" class="form-control"
                    value="{{ old('license_number', $doctor->license_number) }}" required>
                @error('license_number') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">رقم الهاتف:</label>
                <input type="text" id="phone" name="phone" class="form-control"
                    value="{{ old('phone', $doctor->phone) }}">
                @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">العنوان:</label>
                <input type="text" id="address" name="address" class="form-control"
                    value="{{ old('address', $doctor->address) }}">
                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-primary me-md-2">تحديث بيانات الطبيب</button>
                <a href="{{ route('doctors.index') }}" class="btn btn-secondary">العودة إلى القائمة</a>
            </div>
        </form>
    </div>
</div>
@endsection