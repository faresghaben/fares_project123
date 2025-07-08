@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تفاصيل الطبيب: {{ $doctor->name }}</h1>

    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <div class="mb-3">
            <strong>اسم الطبيب:</strong> {{ $doctor->name }}
        </div>
        <div class="mb-3">
            <strong>التخصص:</strong> {{ $doctor->specialization }}
        </div>
        <div class="mb-3">
            <strong>رقم الترخيص:</strong> {{ $doctor->license_number }}
        </div>
        <div class="mb-3">
            <strong>رقم الهاتف:</strong> {{ $doctor->phone ?? 'غير متوفر' }}
        </div>
        <div class="mb-3">
            <strong>العنوان:</strong> {{ $doctor->address ?? 'غير متوفر' }}
        </div>
        <hr>
        <h5 class="mb-3">بيانات المستخدم المرتبط:</h5>
        <div class="mb-3">
            <strong>اسم المستخدم:</strong> {{ $doctor->user->name ?? 'غير متوفر' }}
        </div>
        <div class="mb-3">
            <strong>البريد الإلكتروني:</strong> {{ $doctor->user->email ?? 'غير متوفر' }}
        </div>
        <div class="mb-3">
            <strong>الدور:</strong> {{ $doctor->user->role ?? 'غير متوفر' }}
        </div>
        <div class="mb-3">
            <strong>تاريخ الإنشاء:</strong> {{ $doctor->created_at->format('Y-m-d H:i') }}
        </div>
        <div class="mb-3">
            <strong>آخر تحديث:</strong> {{ $doctor->updated_at->format('Y-m-d H:i') }}
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا الطبيب المحدد --}}
            @can('update', $doctor)
                <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-warning me-2">تعديل الطبيب</a>
            @endcan

            {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا الطبيب المحدد --}}
            @can('delete', $doctor)
                <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الطبيب؟ سيتم حذف المستخدم المرتبط به أيضًا.')">حذف الطبيب</button>
                </form>
            @endcan

            <a href="{{ route('doctors.index') }}" class="btn btn-secondary">العودة إلى قائمة الأطباء</a>
        </div>
    </div>
</div>
@endsection