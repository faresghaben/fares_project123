@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تعديل موعد</h1>

    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="doctor_id" class="form-label">الطبيب:</label>
                <select id="doctor_id" name="doctor_id" class="form-select" required>
                    <option value="">اختر طبيبًا</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doctor->id ? 'selected' : '' }}>{{ $doctor->user->name }} ({{ $doctor->specialization }})</option>
                    @endforeach
                </select>
                @error('doctor_id') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="patient_id" class="form-label">المريض:</label>
                <select id="patient_id" name="patient_id" class="form-select" required>
                    <option value="">اختر مريضًا</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                    @endforeach
                </select>
                @error('patient_id') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="start_time" class="form-label">وقت بداية الموعد:</label>
                <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="{{ old('start_time', $appointment->start_time->format('Y-m-d\TH:i')) }}" required>
                @error('start_time') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="end_time" class="form-label">وقت نهاية الموعد:</label>
                <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="{{ old('end_time', $appointment->end_time->format('Y-m-d\TH:i')) }}" required>
                @error('end_time') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">الحالة:</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="scheduled" {{ old('status', $appointment->status) == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                    <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="canceled" {{ old('status', $appointment->status) == 'canceled' ? 'selected' : '' }}>ملغي</option>
                </select>
                @error('status') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="cancellation_reason" class="form-label">سبب الإلغاء (اختياري، إذا كانت الحالة "ملغي"):</label>
                <textarea id="cancellation_reason" name="cancellation_reason" rows="4" class="form-control">{{ old('cancellation_reason', $appointment->cancellation_reason) }}</textarea>
                @error('cancellation_reason') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary me-md-2">تحديث الموعد</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">العودة إلى قائمة المواعيد</a>
            </div>
        </form>
    </div>
</div>
@endsection