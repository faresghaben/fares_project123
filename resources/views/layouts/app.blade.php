<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'My Clinic App') }}</title> {{-- غيرت العنوان الافتراضي ليناسب تطبيق العيادة --}}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .container {
            margin-top: 20px; /* لترك مسافة من الأعلى */
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                {{-- <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'My Clinic App') }}
                </a> --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth {{-- تحقق أولاً ما إذا كان هناك مستخدم مسجل دخول --}}
                        {{-- ثم تحقق من دور المستخدم --}}
                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('doctor'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('patients.index') }}">المرضى</a>
                        </li>
                        @endif
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctors.index') }}">الأطباء</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('appointments.index') }}">المواعيد</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('medical-records.index') }}">السجلات الطبية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('available-slots.index') }}">المواعيد المتاحة</a>
                        </li>

                        @auth {{-- تحقق أولاً ما إذا كان هناك مستخدم مسجل دخول --}}
                        {{-- ثم تحقق من دور المستخدم --}}
                        @if(Auth::user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">المستخدمون</a>
                        </li>
                        @endif
                        @endauth
                        {{-- أضف المزيد من الروابط هنا --}}
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content') {{-- هذا هو المكان الذي سيتم فيه عرض محتوى كل صفحة فرعية --}}
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>