@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تعديل السجل الطبي</h1>
    <form action="{{ route('medical-records.update', $medicalRecord->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- مهم لتحديد طريقة HTTP كـ PUT/PATCH --}}
        <div class="mb-3">
            <label for="patient_id" class="form-label">المريض:</label>
            <select class="form-control" id="patient_id" name="patient_id" required>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $medicalRecord->patient_id == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="doctor_id" class="form-label">الطبيب:</label>
            <select class="form-control" id="doctor_id" name="doctor_id" required>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $medicalRecord->doctor_id == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="diagnosis" class="form-label">التشخيص:</label>
            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required>{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="treatment" class="form-label">العلاج:</label>
            <textarea class="form-control" id="treatment" name="treatment" rows="3">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="record_date" class="form-label">تاريخ السجل:</label>
            <input type="date" class="form-control" id="record_date" name="record_date" value="{{ old('record_date', $medicalRecord->record_date->format('Y-m-d')) }}" required>
        </div>
        <button type="submit" class="btn btn-success">تحديث السجل</button>
        <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection