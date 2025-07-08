@extends('layouts.app') {{-- افترض أن لديك layout أساسي --}}

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">السجلات الطبية</h1>

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

    {{-- زر إضافة سجل طبي جديد: يظهر فقط إذا كان المستخدم مصرحاً له بإنشاء سجلات طبية --}}
    @can('create', App\Models\MedicalRecord::class)
        <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('medical-records.create') }}" class="btn btn-primary">إضافة سجل طبي جديد</a>
        </div>
    @endcan

    @if ($medicalRecords->isEmpty())
        <div class="alert alert-info text-center">
            لا توجد سجلات طبية بعد.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>المريض</th>
                        <th>الطبيب</th>
                        <th>التشخيص</th>
                        <th>العلاج</th>
                        <th>تاريخ السجل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicalRecords as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->patient->name ?? 'غير معروف' }}</td> {{-- عرض اسم المريض --}}
                        <td>{{ $record->doctor->name ?? 'غير معروف' }}</td>   {{-- عرض اسم الطبيب --}}
                        <td>{{ Str::limit($record->diagnosis, 50) }}</td>
                        <td>{{ Str::limit($record->treatment, 50) }}</td>
                        <td>{{ $record->record_date->format('Y-m-d') }}</td> {{-- تنسيق التاريخ --}}
                        <td>
                            {{-- زر العرض: يظهر إذا كان المستخدم مصرحاً له برؤية هذا السجل المحدد --}}
                            @can('view', $record)
                                <a href="{{ route('medical-records.show', $record->id) }}" class="btn btn-info btn-sm">عرض</a>
                            @endcan

                            {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا السجل المحدد --}}
                            @can('update', $record)
                                <a href="{{ route('medical-records.edit', $record->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            @endcan

                            {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا السجل المحدد --}}
                            @can('delete', $record)
                                <form action="{{ route('medical-records.destroy', $record->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection