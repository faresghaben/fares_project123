@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">إدارة المواعيد</h1>

    @if(session('success'))
        <div class="alert alert-success" role="alert"> {{-- Bootstrap alert class --}}
            {{ session('success') }}
        </div>
    @endif

    {{-- زر إضافة موعد جديد: يظهر فقط إذا كان المستخدم مصرحاً له بإنشاء مواعيد --}}
    @can('create', App\Models\Appointment::class)
        <div class="mb-3 text-end">
            <a href="{{ route('appointments.create') }}" class="btn btn-success">إضافة موعد جديد</a> {{-- Bootstrap button --}}
        </div>
    @endcan

    @if($appointments->isEmpty())
        <p class="text-center">لا توجد مواعيد حاليًا.</p>
    @else
        <div class="table-responsive"> {{-- For responsive tables on smaller screens --}}
            <table class="table table-bordered table-hover"> {{-- Bootstrap table classes --}}
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطبيب</th>
                        <th>اسم المريض</th>
                        <th>وقت البداية</th>
                        <th>وقت النهاية</th>
                        <th>الحالة</th>
                        <th>سبب الإلغاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->start_time->format('Y-m-d H:i') }}</td>
                            <td>{{ $appointment->end_time->format('Y-m-d H:i') }}</td>
                            <td>{{ $appointment->status }}</td>
                            <td>{{ $appointment->cancellation_reason ?? 'لا يوجد' }}</td>
                            <td class="d-flex flex-wrap gap-1"> {{-- Use d-flex and gap for spacing buttons --}}
                                {{-- زر العرض: يظهر إذا كان المستخدم مصرحاً له برؤية هذا الموعد المحدد --}}
                                @can('view', $appointment)
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">عرض</a>
                                @endcan

                                {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا الموعد المحدد --}}
                                @can('update', $appointment)
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                @endcan

                                {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا الموعد المحدد --}}
                                @can('delete', $appointment)
                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">حذف</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="d-flex justify-content-center mt-4"> {{-- Center pagination --}}
                {{ $appointments->onEachSide(0)->links('pagination::bootstrap-4') }}
            </div>
        @endif
    @endif
</div>
@endsection