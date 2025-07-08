@extends('layouts.app')

@section('content')
<div class="container">
    <h1>إضافة سجل طبي جديد</h1>
    <form action="{{ route('medical-records.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="patient_id" class="form-label">المريض:</label>
            <select class="form-control" id="patient_id" name="patient_id" required>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="doctor_id" class="form-label">الطبيب:</label>
            <select class="form-control" id="doctor_id" name="doctor_id" required>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="diagnosis" class="form-label">التشخيص:</label>
            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required>{{ old('diagnosis') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="treatment" class="form-label">العلاج:</label>
            <textarea class="form-control" id="treatment" name="treatment" rows="3">{{ old('treatment') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="record_date" class="form-label">تاريخ السجل:</label>
            <input type="date" class="form-control" id="record_date" name="record_date" value="{{ old('record_date', now()->format('Y-m-d')) }}" required>
        </div>
        <button type="submit" class="btn btn-success">حفظ السجل</button>
        <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection