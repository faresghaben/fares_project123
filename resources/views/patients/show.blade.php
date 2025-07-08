@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تفاصيل المريض: {{ $patient->name }}</h1>

    <div class="card p-4 mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h5 class="card-title mb-3">بيانات المريض</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>الاسم الكامل:</strong> {{ $patient->name }}</li>
                <li class="list-group-item"><strong>تاريخ الميلاد:</strong> {{ $patient->date_of_birth ?? 'غير محدد' }}</li>
                <li class="list-group-item"><strong>الجنس:</strong> {{ $patient->gender ?? 'غير محدد' }}</li>
                <li class="list-group-item"><strong>فصيلة الدم:</strong> {{ $patient->blood_type ?? 'غير محدد' }}</li>
                <li class="list-group-item"><strong>الحساسيات:</strong> {{ $patient->allergies ?? 'لا توجد' }}</li>
            </ul>

            @if ($patient->user)
                <h5 class="card-title mt-4 mb-3">بيانات حساب المستخدم</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>اسم المستخدم:</strong> {{ $patient->user->name }}</li>
                    <li class="list-group-item"><strong>البريد الإلكتروني:</strong> {{ $patient->user->email }}</li>
                    <li class="list-group-item"><strong>الدور:</strong> {{ $patient->user->role }}</li>
                </ul>
            @else
                <div class="alert alert-warning mt-4" role="alert">
                    لا يوجد حساب مستخدم مرتبط بهذا المريض.
                </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between mt-3">
            {{-- زر التعديل: يظهر إذا كان المستخدم مصرحاً له بتعديل هذا المريض المحدد --}}
            @can('update', $patient)
                <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">تعديل المريض</a>
            @endcan

            {{-- زر الحذف: يظهر إذا كان المستخدم مصرحاً له بحذف هذا المريض المحدد --}}
            @can('delete', $patient)
                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المريض؟ سيتم حذف المستخدم المرتبط به أيضًا.')">حذف المريض</button>
                </form>
            @endcan

            <a href="{{ route('patients.index') }}" class="btn btn-secondary">العودة إلى قائمة المرضى</a>
        </div>
    </div>
</div>
@endsection