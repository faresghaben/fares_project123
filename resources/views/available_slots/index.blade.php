@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">المواعيد المتاحة للحجز</h1>

    {{-- رسائل النجاح أو الخطأ --}}
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

    @if ($availableSlots->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            لا توجد مواعيد متاحة للحجز حالياً. يرجى التحقق لاحقاً.
        </div>
    @else
        <div class="row">
            @foreach ($availableSlots as $slot)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">موعد مع: {{ $slot->doctor->user->name }}</h5>
                            <p class="card-text">
                                <strong>التخصص:</strong> {{ $slot->doctor->specialization }}<br>
                                <strong>الوقت:</strong> {{ $slot->start_time->format('Y-m-d H:i') }} - {{ $slot->end_time->format('H:i') }}<br>
                                <strong>الحالة:</strong>
                                @if ($slot->status === 'available') {{-- التغيير الأول: استخدم status --}}
                                    <span class="badge bg-success">متاح للحجز</span>
                                @elseif ($slot->status === 'booked') {{-- التغيير الثاني: استخدم status --}}
                                    <span class="badge bg-warning">محجوز</span>
                                @else {{-- حالة افتراضية لأي حالة أخرى (مثل cancelled, completed) --}}
                                    <span class="badge bg-secondary">{{ $slot->status }}</span>
                                @endif
                            </p>
                            <a href="{{ route('available-slots.show', $slot->id) }}" class="btn btn-info btn-sm">عرض</a>

                            {{-- الشرط لعرض زر الحجز فقط للمريض والمواعيد غير المحجوزة --}}
                            @if ($slot->status === 'available' && Auth::check() && Auth::user()->hasRole('patient')) {{-- التغيير الثالث: استخدم status --}}
                                <form action="{{ route('available_slots.book', $slot->id) }}" method="POST"> {{-- التغيير الرابع: اسم الراوت _ --}}
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">احجز الآن</button>
                                </form>
                            @elseif($slot->status === 'booked') {{-- التغيير الخامس: استخدم status --}}
                                <button type="button" class="btn btn-secondary btn-sm" disabled>محجوز</button>
                                {{-- يمكن عرض زر "عرض" للمدير أو الطبيب أو لغير المسجلين --}}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $availableSlots->links() }}
        </div>
    @endif
</div>
@endsection