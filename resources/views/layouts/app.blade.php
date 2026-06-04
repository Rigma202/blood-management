<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blood Inventory Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width:250px; min-height:100vh;">

        <h3 class="mb-4">Admin Panel</h3>

<ul class="nav flex-column">

    <li class="nav-item mb-2">
        <a href="{{ route('dashboard') }}" class="nav-link text-white">
            Dashboard
        </a>
    </li>

    {{-- ADMIN MENU --}}
    @if(auth()->user()->role === 'admin')

        <li class="nav-item mb-2">
            <a href="{{ route('blood-banks.index') }}" class="nav-link text-white">
                Blood Banks
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('staff.index') }}" class="nav-link text-white">
                Manage Staff
            </a>
        </li>

    @endif


    {{-- STAFF MENU --}}
    @if(auth()->user()->role === 'staff')
        <li class="nav-item mb-2">
            <a href="{{ route('staff.blood-banks') }}" class="nav-link text-white">
                Blood Banks
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('refrigerators.index') }}" class="nav-link text-white">
                Refrigerators
            </a>
        </li>

        <li class="nav-item mb-2">
        <a href="{{ route('refrigerators.logs') }}" class="nav-link text-white">
            Temperature Logs
        </a>
        </li>
        <li class="nav-item mb-2">
        <a href="{{ route('refrigerators.dailyAnalysis') }}" class="nav-link text-white">
            Daily Analysis
        </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('blood-bags.index') }}" class="nav-link text-white">
                Blood Bags
            </a>
        </li>

    @endif


    {{-- MONITORING USER MENU --}}
    @if(auth()->user()->role === 'monitoring_user')

        <li class="nav-item mb-2">
            <a href="{{ route('blood-banks.index') }}" class="nav-link text-white">
                Blood Banks
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('analysis.index') }}" class="nav-link text-white">
                Analysis
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('contact-admin') }}" class="nav-link text-white">
                Contact Admin
            </a>
        </li>

    @endif

</ul>

    </div>

    <!-- Main Content -->
    <div class="w-100">

        <!-- Top Navbar -->
        <nav class="navbar navbar-light bg-light px-4 d-flex justify-content-end">

            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown">
                    {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item"
                           href="{{ route('profile.edit') }}">
                            Profile
                        </a>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                Logout
                            </button>
                        </form>

                    </li>

                </ul>

            </div>

        </nav>

        <!-- Page Content -->
        <div class="p-4">

            @yield('content')

        </div>

    </div>

</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@stack('scripts')
</body>
</html>
