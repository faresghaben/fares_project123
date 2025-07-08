@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">إضافة مريض جديد</h1>

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
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf

            {{-- 1. حقول المستخدم (لإنشاء User أولاً) --}}
            <h5 class="mb-3">بيانات تسجيل الدخول (للمستخدم الجديد)</h5>
            <div class="mb-3">
                <label for="user_name" class="form-label">اسم المستخدم:</label>
                <input type="text" id="user_name" name="user_name" class="form-control" value="{{ old('user_name') }}" required>
                @error('user_name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_email" class="form-label">البريد الإلكتروني للمستخدم:</label>
                <input type="email" id="user_email" name="user_email" class="form-control" value="{{ old('user_email') }}" required>
                @error('user_email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_password" class="form-label">كلمة المرور:</label>
                <input type="password" id="user_password" name="user_password" class="form-control" required>
                @error('user_password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="user_password_confirmation" class="form-label">تأكيد كلمة المرور:</label>
                <input type="password" id="user_password_confirmation" name="user_password_confirmation" class="form-control" required>
            </div>

            <hr>

            {{-- 2. حقول بيانات المريض (Patient) --}}
            <h5 class="mb-3 mt-4">بيانات المريض</h5>
            <div class="mb-3">
                <label for="name" class="form-label">اسم المريض الكامل:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="date_of_birth" class="form-label">تاريخ الميلاد:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                @error('date_of_birth') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">الجنس:</label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="">اختر الجنس</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>آخر</option>
                </select>
                @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="blood_type" class="form-label">فصيلة الدم (اختياري):</label>
                <select id="blood_type" name="blood_type" class="form-select">
                    <option value="">اختر فصيلة الدم</option>
                    <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
                @error('blood_type') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="allergies" class="form-label">الحساسيات (اختياري):</label>
                <textarea id="allergies" name="allergies" class="form-control" rows="3">{{ old('allergies') }}</textarea>
                @error('allergies') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-success me-md-2">إضافة مريض</button>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection