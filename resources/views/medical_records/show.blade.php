@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تفاصيل السجل الطبي</h1>

    <div class="card p-4 mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h5 class="card-title mb-3">السجل رقم: {{ $medicalRecord->id }}</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>المريض:</strong> {{ $medicalRecord->patient->name ?? 'غير معروف' }}</li>
                <li class="list-group-item"><strong>الطبيب:</strong> {{ $medicalRecord->doctor->name ?? 'غير معروف' }}</li>
                <li class="list-group-item"><strong>التشخيص:</strong> {{ $medicalRecord->diagnosis }}</li>
                <li class="list-group-item"><strong>العلاج:</strong> {{ $medicalRecord->treatment }}</li>
                <li class="list-group-item"><strong>تاريخ السجل:</strong> {{ $medicalRecord->record_date->format('Y-m-d') }}</li>
                <li class="list-group-item"><strong>تاريخ الإنشاء:</strong> {{ $medicalRecord->created_at->format('Y-m-d H:i:s') }}</li>
                <li class="list-group-item"><strong>آخر تحديث:</strong> {{ $medicalRecord->updated_at->format('Y-m-d H:i:s') }}</li>
            </ul>
        </div>
        <div class="card-footer d-flex justify-content-between mt-3">
            {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا السجل المحدد --}}
            @can('update', $medicalRecord)
                <a href="{{ route('medical-records.edit', $medicalRecord->id) }}" class="btn btn-warning">تعديل</a>
            @endcan

            {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا السجل المحدد --}}
            @can('delete', $medicalRecord)
                <form action="{{ route('medical-records.destroy', $medicalRecord->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا السجل الطبي؟')">حذف</button>
                </form>
            @endcan

            <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">العودة إلى السجلات الطبية</a>
        </div>
    </div>
</div>
@endsection