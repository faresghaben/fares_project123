@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تعديل بيانات المستخدم: {{ $user->name }}</h1>

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
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">الاسم:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور الجديدة (اتركها فارغة لعدم التغيير):</label>
                <input type="password" id="password" name="password" class="form-control">
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">الدور:</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="patient" {{ old('role', $user->role) == 'patient' ? 'selected' : '' }}>مريض</option>
                    <option value="doctor" {{ old('role', $user->role) == 'doctor' ? 'selected' : '' }}>طبيب</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مسؤول</option>
                </select>
                @error('role') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            {{-- حقول خاصة بالدور --}}
            <div id="role-specific-fields">
                {{-- **التعديل هنا:** حقول الطبيب --}}
                <div class="mb-3" id="doctor-fields" style="display: none;">
                    <label for="specialization" class="form-label">التخصص:</label>
                    <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization', ($user->role == 'doctor' && $user->doctor ? $user->doctor->specialization : '')) }}">
                    @error('specialization')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <label for="license_number" class="form-label mt-3">رقم الترخيص:</label>
                    <input type="text" name="license_number" id="license_number" class="form-control" value="{{ old('license_number', ($user->role == 'doctor' && $user->doctor ? $user->doctor->license_number : '')) }}">
                    @error('license_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- **التعديل الجديد هنا:** حقول المريض --}}
                <div class="mb-3" id="patient-fields" style="display: none;">
                    <label for="date_of_birth" class="form-label">تاريخ الميلاد:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', ($user->role == 'patient' && $user->patient ? $user->patient->date_of_birth : '')) }}">
                    @error('date_of_birth')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    {{-- يمكنك إضافة حقول أخرى هنا لبيانات المريض --}}
                </div>
            </div> {{-- نهاية role-specific-fields --}}


            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-primary me-md-2">تحديث المستخدم</button>
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

        const patientFieldsGroup = document.getElementById('patient-fields');
        const dateOfBirthInput = document.getElementById('date_of_birth');

        function toggleRoleSpecificFields() {
            // إخفاء جميع الحقول أولاً ومسح قيمها وإزالة صفة required
            doctorFieldsGroup.style.display = 'none';
            specializationInput.removeAttribute('required');
            specializationInput.value = '';
            licenseNumberInput.removeAttribute('required');
            licenseNumberInput.value = '';

            patientFieldsGroup.style.display = 'none';
            dateOfBirthInput.removeAttribute('required');
            dateOfBirthInput.value = '';

            // إظهار الحقول بناءً على الدور المختار وتعيين صفة required
            if (roleSelect.value === 'doctor') {
                doctorFieldsGroup.style.display = 'block';
                specializationInput.setAttribute('required', 'required');
                licenseNumberInput.setAttribute('required', 'required');
            } else if (roleSelect.value === 'patient') {
                patientFieldsGroup.style.display = 'block';
                dateOfBirthInput.setAttribute('required', 'required');
            }
            // في وضع التعديل، إذا كان الدور السابق هو نفسه الدور الحالي وكان له قيم، فاحتفظ بها.
            // هذا المنطق تم تغطيته بواسطة old() و (?? '') في قيمة حقل الإدخال مباشرة في Blade.
        }

        roleSelect.addEventListener('change', toggleRoleSpecificFields);
        toggleRoleSpecificFields(); // استدعاء الوظيفة عند تحميل الصفحة لأول مرة لضبط الحالة الأولية
    });
</script>
@endsection