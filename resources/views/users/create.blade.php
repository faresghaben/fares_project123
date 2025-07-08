@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">إضافة مستخدم جديد</h1>

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
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">الاسم:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور:</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">الدور:</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">اختر دور المستخدم</option>
                    <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>مريض</option>
                    <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>طبيب</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مسؤول</option>
                </select>
                @error('role') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- حقول خاصة بالدور --}}
            <div id="role-specific-fields">
                {{-- **التعديل هنا:** حقول الطبيب --}}
                <div class="mb-3" id="doctor-fields" style="display: none;">
                    <label for="specialization" class="form-label">التخصص:</label>
                    <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization') }}">
                    @error('specialization')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <label for="license_number" class="form-label mt-3">رقم الترخيص:</label>
                    <input type="text" name="license_number" id="license_number" class="form-control" value="{{ old('license_number') }}">
                    @error('license_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- **التعديل الجديد هنا:** حقول المريض --}}
                <div class="mb-3" id="patient-fields" style="display: none;">
                    <label for="date_of_birth" class="form-label">تاريخ الميلاد:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    @error('date_of_birth')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    {{-- يمكنك إضافة حقول أخرى هنا لبيانات المريض مثل الجنس، فصيلة الدم، إلخ. --}}
                    {{--
                    <label for="gender" class="form-label mt-3">الجنس:</label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="">اختر الجنس</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    --}}
                </div>
            </div> {{-- نهاية role-specific-fields --}}


            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-success me-md-2">إضافة المستخدم</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const doctorFieldsGroup = document.getElementById('doctor-fields');
        const specializationInput = document.getElementById('specialization');
        const licenseNumberInput = document.getElementById('license_number');

        const patientFieldsGroup = document.getElementById('patient-fields'); // الجديد
        const dateOfBirthInput = document.getElementById('date_of_birth');   // الجديد
        // const genderInput = document.getElementById('gender'); // مثال لحقل آخر

        function toggleRoleSpecificFields() {
            // إخفاء جميع الحقول أولاً
            doctorFieldsGroup.style.display = 'none';
            specializationInput.removeAttribute('required');
            specializationInput.value = '';
            licenseNumberInput.removeAttribute('required');
            licenseNumberInput.value = '';

            patientFieldsGroup.style.display = 'none';
            dateOfBirthInput.removeAttribute('required');
            dateOfBirthInput.value = '';
            // genderInput.removeAttribute('required'); // مثال لحقل آخر
            // genderInput.value = ''; // مثال لحقل آخر


            // إظهار الحقول بناءً على الدور المختار
            if (roleSelect.value === 'doctor') {
                doctorFieldsGroup.style.display = 'block';
                specializationInput.setAttribute('required', 'required');
                licenseNumberInput.setAttribute('required', 'required');
            } else if (roleSelect.value === 'patient') {
                patientFieldsGroup.style.display = 'block';
                dateOfBirthInput.setAttribute('required', 'required');
                // genderInput.setAttribute('required', 'required'); // مثال لحقل آخر
            }
        }

        roleSelect.addEventListener('change', toggleRoleSpecificFields);
        toggleRoleSpecificFields(); // استدعاء الوظيفة عند تحميل الصفحة لأول مرة لضبط الحالة الأولية
    });
</script>
@endsection