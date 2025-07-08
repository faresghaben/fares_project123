@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg"> {{-- أضفت shadow-lg لظل أوضح --}}
                <div class="card-header bg-primary text-white text-center"> {{-- لون أزرق لرأس الكارد --}}
                    <h2 class="h3 mb-0">{{ __('User Profile') }}</h2>
                </div>
                <div class="card-body">
                    {{-- تم إزالة col-md-4 الخاص بالصورة --}}
                    <div class="row">
                        {{-- تم تغيير col-md-8 إلى col-md-12 ليأخذ العرض الكامل --}}
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Profile Information') }}</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>{{ __('Name') }}:</strong> {{ $user->name }}
                                </li>
                                <li class="list-group-item">
                                    <strong>{{ __('Email') }}:</strong> {{ $user->email }}
                                </li>
                                {{-- عرض معلومات الطبيب فقط إذا كان المستخدم لديه دور طبيب --}}
                                @if(Auth::user()->hasRole('doctor')) {{-- تأكد من أن لديك هذه الدالة hasRole --}}
                                <li class="list-group-item bg-light">
                                    <strong>{{ __('Doctor Details') }}</strong>
                                </li>
                                <li class="list-group-item">
                                    <strong>{{ __('Specialization') }}:</strong> {{ $user->doctor->specialization ?? 'N/A' }} {{-- افترض وجود علاقة doctor بين User و Doctor --}}
                                </li>
                                <li class="list-group-item">
                                    <strong>{{ __('License Number') }}:</strong> {{ $user->doctor->license_number ?? 'N/A' }}
                                </li>
                                @endif
                                {{-- يمكنك إضافة المزيد من المعلومات هنا --}}
                            </ul>
                            <hr class="my-4">
                            <div class="d-grid gap-2"> {{-- لجعل الزر يأخذ العرض الكامل --}}
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">{{ __('Edit Profile') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection