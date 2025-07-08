@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg"> {{-- أضفت shadow-lg لظل أوضح --}}
                {{-- رأس الكارد الرئيسي - تم تغيير bg-dark إلى bg-primary --}}
                <div class="card-header bg-primary text-white text-center">
                    <h1 class="h3 mb-0">{{ __('Edit Profile') }}</h1>
                </div>
                <div class="card-body">
                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab" data-bs-target="#profile-info" type="button" role="tab" aria-controls="profile-info" aria-selected="true">
                                {{ __('Profile Information') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                {{ __('Update Password') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="delete-account-tab" data-bs-toggle="tab" data-bs-target="#delete-account" type="button" role="tab" aria-controls="delete-account" aria-selected="false">
                                {{ __('Delete Account') }}
                            </button>
                        </li>
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="profileTabsContent">
                        {{-- تبويب معلومات الملف الشخصي --}}
                        <div class="tab-pane fade show active" id="profile-info" role="tabpanel" aria-labelledby="profile-info-tab">
                            @include('profile.partials.update-profile-information-form')
                        </div>

                        {{-- تبويب تحديث كلمة المرور --}}
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            @include('profile.partials.update-password-form')
                        </div>

                        {{-- تبويب حذف الحساب --}}
                        <div class="tab-pane fade" id="delete-account" role="tabpanel" aria-labelledby="delete-account-tab">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection