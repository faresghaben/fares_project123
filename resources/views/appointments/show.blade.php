@extends('layouts.app')

@section('content')
<div class="container">
    {{-- تم تغيير العنوان ليعكس تفاصيل الموعد --}}
    <h1 class="mb-4 text-center">تفاصيل الموعد</h1>

    <div class="card p-4 mx-auto" style="max-width: 700px;">
        <div class="card-body">
            {{-- تم تغيير المتغير من medicalRecord إلى appointment --}}
            <h5 class="card-title mb-3">الموعد رقم: {{ $appointment->id }}</h5>
            <ul class="list-group list-group-flush">
                {{-- استخدام علاقة المريض في الموعد --}}
                <li class="list-group-item"><strong>المريض:</strong> {{ $appointment->patient->user->name ?? 'غير معروف' }}</li>                {{-- استخدام علاقة الطبيب في الموعد (مع الوصول لاسم المستخدم المرتبط بالطبيب) --}}
                <li class="list-group-item"><strong>الطبيب:</strong> {{ $appointment->doctor->user->name ?? 'غير معروف' }}</li>
                
                {{-- تفاصيل خاصة بالموعد --}}
                <li class="list-group-item"><strong>وقت البدء:</strong> {{ $appointment->start_time->format('Y-m-d H:i:s') }}</li>
                <li class="list-group-item"><strong>وقت الانتهاء:</strong> {{ $appointment->end_time->format('Y-m-d H:i:s') }}</li>
                <li class="list-group-item"><strong>الحالة:</strong> {{ $appointment->status }}</li>
                
                {{-- إذا كان هناك سبب للإلغاء (خاص بالمواعيد) --}}
                @if($appointment->cancellation_reason)
                    <li class="list-group-item"><strong>سبب الإلغاء:</strong> {{ $appointment->cancellation_reason }}</li>
                @endif

                {{-- التفاصيل التالية (التشخيص والعلاج وتاريخ السجل) عادة ما تكون جزءًا من السجل الطبي، وليس الموعد.
                    إذا كنت تحتاجها هنا، فيجب أن يكون هناك علاقة بين الموعد والسجل الطبي.
                    للتصحيح الفوري، تم إزالتها. --}}
                {{-- <li class="list-group-item"><strong>التشخيص:</strong> {{ $medicalRecord->diagnosis ?? 'لا يوجد' }}</li> --}}
                {{-- <li class="list-group-item"><strong>العلاج:</strong> {{ $medicalRecord->treatment ?? 'لا يوجد' }}</li> --}}
                {{-- <li class="list-group-item"><strong>تاريخ السجل:</strong> {{ $medicalRecord->record_date->format('Y-m-d') }}</li> --}}
                
                <li class="list-group-item"><strong>تاريخ الإنشاء:</strong> {{ $appointment->created_at->format('Y-m-d H:i:s') }}</li>
                <li class="list-group-item"><strong>آخر تحديث:</strong> {{ $appointment->updated_at->format('Y-m-d H:i:s') }}</li>
            </ul>
        </div>
        <div class="card-footer d-flex justify-content-between mt-3">
            {{-- زر التعديل: تم تغيير المسار والمتغير --}}
            @can('update', $appointment) {{-- تأكد أن هذا يشير إلى AppointmentPolicy@update --}}
                <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning">تعديل</a>
            @endcan

            {{-- زر الحذف: تم تغيير المسار والمتغير --}}
            @can('delete', $appointment) {{-- تأكد أن هذا يشير إلى AppointmentPolicy@delete --}}
                <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">حذف</button>
                </form>
            @endcan

            {{-- زر العودة: تم تغيير المسار --}}
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">العودة إلى المواعيد</a>
        </div>
    </div>
</div>
@endsection