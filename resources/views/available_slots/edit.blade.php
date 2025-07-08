@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>تعديل الموعد المتاح</h1>

        <form action="{{ route('available-slots.update', $availableSlot->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- لإرسال طلب PUT لتحديث المورد --}}

            <div class="mb-3">
                <label for="doctor_id" class="form-label">الطبيب:</label>
                <select class="form-control" id="doctor_id" name="doctor_id" required>
                    <option value="">اختر طبيبًا</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ $doctor->id == $availableSlot->doctor_id ? 'selected' : '' }}>
                            {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="day_of_week" class="form-label">اليوم:</label>
                <select class="form-control" id="day_of_week" name="day_of_week" required>
                    @php
                        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $dayNames = [
                            'monday' => 'الاثنين',
                            'tuesday' => 'الثلاثاء',
                            'wednesday' => 'الأربعاء',
                            'thursday' => 'الخميس',
                            'friday' => 'الجمعة',
                            'saturday' => 'السبت',
                            'sunday' => 'الأحد',
                        ];
                    @endphp
                    @foreach($daysOfWeek as $day)
                        <option value="{{ $day }}" {{ $day == $availableSlot->day_of_week ? 'selected' : '' }}>
                            {{ $dayNames[$day] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="start_time" class="form-label">وقت البدء:</label>
                <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($availableSlot->start_time)->format('H:i')) }}" required>
            </div>

            <div class="mb-3">
                <label for="end_time" class="form-label">وقت الانتهاء:</label>
                <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($availableSlot->end_time)->format('H:i')) }}" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ $availableSlot->is_available ? 'checked' : '' }}>
                <label class="form-check-label" for="is_available">متاح</label>
            </div>

            <button type="submit" class="btn btn-primary">تحديث الموعد</button>
            <a href="{{ route('available-slots.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
@endsection