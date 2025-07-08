@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">قائمة الأطباء</h1>

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

    {{-- زر إضافة طبيب جديد: يظهر فقط إذا كان المستخدم مصرحاً له بإنشاء أطباء --}}
    @can('create', App\Models\Doctor::class)
        <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('doctors.create') }}" class="btn btn-primary">إضافة طبيب جديد</a>
        </div>
    @endcan

    @if ($doctors->isEmpty())
        <div class="alert alert-info text-center">
            لا يوجد أطباء مسجلون حاليًا.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم الطبيب</th>
                        <th>التخصص</th>
                        <th>رقم الترخيص</th>
                        <th>الهاتف</th>
                        <th>البريد الإلكتروني للمستخدم</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctors as $doctor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $doctor->name }}</td> {{-- اسم الطبيب من جدول doctors --}}
                            <td>{{ $doctor->specialization }}</td>
                            <td>{{ $doctor->license_number }}</td>
                            <td>{{ $doctor->phone ?? 'N/A' }}</td>
                            <td>{{ $doctor->user->email ?? 'N/A' }}</td> {{-- البريد الإلكتروني للمستخدم المرتبط --}}
                            <td>

                                                                {{-- زر عرض المواعيد المتاحة للطبيب (يظهر للمريض فقط) --}}
                                @auth
                                    @if(Auth::user()->hasRole('patient'))
                                        <a href="{{ route('doctors.available_slots', $doctor->id) }}" class="btn btn-info btn-sm me-2">
                                            عرض المواعيد المتاحة
                                        </a>
                                    @endif
                                @endauth
                                {{-- زر العرض: يظهر إذا كان المستخدم مصرحاً له برؤية هذا الطبيب المحدد --}}
                                @can('view', $doctor)
                                    <a href="{{ route('doctors.show', $doctor->id) }}" class="btn btn-info btn-sm">عرض</a>
                                @endcan

                                {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا الطبيب المحدد --}}
                                @can('update', $doctor)
                                    <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                @endcan

                                {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا الطبيب المحدد --}}
                                @can('delete', $doctor)
                                    <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الطبيب؟ سيتم حذف المستخدم المرتبط به أيضًا.')">حذف</button>
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