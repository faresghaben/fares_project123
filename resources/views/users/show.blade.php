@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تفاصيل المستخدم: {{ $user->name }}</h1>

    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <div class="mb-3">
            <strong>الاسم:</strong> {{ $user->name }}
        </div>
        <div class="mb-3">
            <strong>البريد الإلكتروني:</strong> {{ $user->email }}
        </div>
        <div class="mb-3">
            <strong>الدور:</strong> {{ $user->role }}
        </div>
        <div class="mb-3">
            <strong>تاريخ الإنشاء:</strong> {{ $user->created_at->format('Y-m-d H:i') }}
        </div>
        <div class="mb-3">
            <strong>آخر تحديث:</strong> {{ $user->updated_at->format('Y-m-d H:i') }}
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-md-2">تعديل</a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">العودة إلى قائمة المستخدمين</a>
        </div>
    </div>
</div>
@endsection