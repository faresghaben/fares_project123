@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>إضافة موعد متاح جديد</h1>
        <form action="{{ route('available-slots.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="doctor_id" class="form-label">الطبيب:</label>
                <select class="form-control" id="doctor_id" name="doctor_id" required>
                    <option value="">اختر طبيبًا</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="day_of_week" class="form-label">اليوم:</label>
                <select class="form-control" id="day_of_week" name="day_of_week" required>
                    <option value="monday">الاثنين</option>
                    <option value="tuesday">الثلاثاء</option>
                    <option value="wednesday">الأربعاء</option>
                    <option value="thursday">الخميس</option>
                    <option value="friday">الجمعة</option>
                    <option value="saturday">السبت</option>
                    <option value="sunday">الأحد</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">وقت البدء:</label>
                <input type="time" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">وقت الانتهاء:</label>
                <input type="time" class="form-control" id="end_time" name="end_time" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" checked>
                <label class="form-check-label" for="is_available">متاح</label>
            </div>
            <button type="submit" class="btn btn-primary">إضافة موعد</button>
        </form>
    </div>
@endsection