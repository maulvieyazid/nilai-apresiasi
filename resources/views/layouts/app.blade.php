<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Aplikasi Nilai Apresiasi') }}</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    @stack('styles')

    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">

    <style>
        .layout-horizontal .main-navbar ul>.menu-item.active .menu-link {
            color: #FFFFFF
        }

    </style>
</head>

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <header class="mb-5">
                @include('layouts.navbar')
            </header>

            <div class="content-wrapper container">
                @include('layouts.alert')

                @yield('content')
            </div>

            <footer>
                @include('layouts.footer')
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    {{-- <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    {{-- <script src="assets/js/pages/dashboard.js"></script> --}}

    @stack('scripts')

    <script>
        var configsLoadingOverlay = {
            "overlayBackgroundColor": "#666666",
            "overlayOpacity": 0.6,
            "spinnerIcon": "ball-spin-fade-rotating",
            "spinnerColor": "#FFFFFF",
            "spinnerSize": "3x",
            "overlayIDName": "overlay",
            "spinnerIDName": "spinner",
            "offsetX": 0,
            "offsetY": "-10%",
            "containerID": null,
            "lockScroll": true,
            "overlayZIndex": 9998,
            "spinnerZIndex": 9999
        };
    </script>

    <script src="{{ asset('assets/js/pages/horizontal-layout.js') }}"></script>
</body>

</html>
