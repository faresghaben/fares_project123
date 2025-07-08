@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">تفاصيل الموعد المتاح</h1>

        <div class="card p-4 mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="card-title mb-3">الطبيب: {{ $availableSlot->doctor->name }}</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>اليوم:</strong> {{ $availableSlot->day_of_week }}</li>
                    <li class="list-group-item"><strong>وقت البدء:</strong> {{ \Carbon\Carbon::parse($availableSlot->start_time)->format('h:i A') }}</li>
                    <li class="list-group-item"><strong>وقت الانتهاء:</strong> {{ \Carbon\Carbon::parse($availableSlot->end_time)->format('h:i A') }}</li>
                    <li class="list-group-item"><strong>متاح:</strong> {{ $availableSlot->is_available ? 'نعم' : 'لا' }}</li>
                    <li class="list-group-item"><strong>تاريخ الإنشاء:</strong> {{ $availableSlot->created_at->format('Y-m-d H:i:s') }}</li>
                    <li class="list-group-item"><strong>آخر تحديث:</strong> {{ $availableSlot->updated_at->format('Y-m-d H:i:s') }}</li>
                </ul>
            </div>
            <div class="card-footer d-flex justify-content-between mt-3">
                {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا السجل المحدد --}}
                @can('update', $availableSlot)
                    <a href="{{ route('available-slots.edit', $availableSlot->id) }}" class="btn btn-warning">تعديل</a>
                @endcan

                {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا السجل المحدد --}}
                @can('delete', $availableSlot)
                    <form action="{{ route('available-slots.destroy', $availableSlot->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد المتاح؟')">حذف</button>
                    </form>
                @endcan

                <a href="{{ route('available-slots.index') }}" class="btn btn-secondary">العودة إلى القائمة</a>
            </div>
        </div>
    </div>
@endsection