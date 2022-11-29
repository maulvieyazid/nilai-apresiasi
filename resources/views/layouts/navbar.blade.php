<div class="header-top">
    <div class="container">
        <div class="logo">
            {{-- <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a> --}}
            <h5>{{ config('app.name', 'Aplikasi Nilai Apresiasi') }}</h5>
        </div>
        <div class="header-top-right">

            {{-- <div class="dropdown">
                <a href="#" class="user-dropdown d-flex dropend" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar avatar-md2">
                        <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                    </div>
                    <div class="text">
                        <h6 class="user-dropdown-name">John Ducky</h6>
                        <p class="user-dropdown-status text-sm text-muted">Member</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownMenuButton1">
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="auth-login.html">Logout</a></li>
                </ul>
            </div> --}}

            <!-- Burger button responsive -->
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </div>
    </div>
</div>
{{-- <nav class="main-navbar">
    <div class="container">
        <ul>
            <li class="menu-item @if ($navbar == 'mhsonlinepembatasan') active @endif">
                <a href="{{ route('mhsonlinepembatasan.index') }}" class='menu-link'>
                    <i class="bi bi-door-closed-fill"></i>
                    <span>Online Karena Pembatasan Kelas</span>
                </a>
            </li>

            @php
                // <li class="menu-item @if ($navbar == 'mhsonlineperijinan') active @endif">
                //     <a href="{{ route('mhsonlineperijinan.index') }}" class='menu-link'>
                //         <i class="bi bi-people-fill"></i>
                //         <span>Online Karena Tidak Diijinkan Ortu</span>
                //     </a>
                // </li>
            @endphp

            <li class="menu-item @if ($navbar == 'mhsonlinepermahasiswa') active @endif">
                <a href="{{ route('mhsonlinepermahasiswa.index') }}" class='menu-link'>
                    <i class="bi bi-person-fill"></i>
                    <span>Online Per Mahasiswa</span>
                </a>
            </li>

            <li class="menu-item @if ($navbar == 'mhsonlinepermatakuliah') active @endif">
                <a href="{{ route('mhsonlinepermatakuliah.index') }}" class='menu-link'>
                    <i class="bi bi-book"></i>
                    <span>Online Per Matakuliah</span>
                </a>
            </li>

            <li class="menu-item @if ($navbar == 'historistatuspermk') active @endif">
                <a href="{{ route('historistatuspermk.index') }}" class='menu-link'>
                    <i class="bi bi-clock-history"></i>
                    <span>Histori Status Per Matakuliah</span>
                </a>
            </li>
        </ul>
    </div>
</nav> --}}
