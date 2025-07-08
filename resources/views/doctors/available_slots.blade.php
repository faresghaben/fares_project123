@extends('layouts.app') {{-- افترض أن لديك هذا التخطيط --}}

@section('content')
    <div class="container">
        <h1>المواعيد المتاحة للطبيب: {{ $doctor->user->name }}</h1>

        @if($availableSlots->isEmpty())
            <p>لا توجد مواعيد متاحة لهذا الطبيب حالياً.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>الطبيب</th>
                        <th>بداية الموعد</th>
                        <th>نهاية الموعد</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($availableSlots as $slot)
                        <tr>
                            <td>{{ $slot->doctor->user->name }}</td>
                            <td>{{ $slot->start_time->format('Y-m-d H:i') }}</td>
                            <td>{{ $slot->end_time->format('Y-m-d H:i') }}</td>
                            <td><span class="badge bg-success">{{ $slot->status }}</span></td> {{-- افترض أن لديك عمود status --}}
                            <td>
                                @if(Auth::check() && Auth::user()->hasRole('patient')) {{-- التحقق من أن المستخدم مسجل دخول وله دور مريض --}}
                                    @if($slot->status == 'available') {{-- عرض الزر فقط إذا كانت الحالة 'available' --}}
                                        <form action="{{ route('available_slots.book', $slot->id) }}" method="POST"> {{-- تحديد مسار الحجز وطريقة الإرسال --}}
                                            @csrf {{-- حماية CSRF في Laravel --}}
                                            <button type="submit" class="btn btn-primary btn-sm">حجز الآن</button>
                                        </form>
                                @else
                                                {{-- إذا لم تكن متاحة، اعرض حالتها (مثل 'booked') --}}
                                        <span class="badge bg-secondary">{{ $slot->status }}</span>
                                @endif
                            @else
                            {{-- إذا لم يكن المريض مسجلاً، يمكن عرض رسالة أو عدم عرض الزر --}}
                                <span class="text-muted">سجل الدخول للحجز</span>
                            @endif
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $availableSlots->links() }} {{-- لإظهار أزرار Pagination --}}
        @endif

        <a href="{{ route('doctors.index') }}" class="btn btn-secondary mt-3">العودة إلى قائمة الأطباء</a>
    </div>
@endsection