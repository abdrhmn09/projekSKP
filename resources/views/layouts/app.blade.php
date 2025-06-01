<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SKP Online SMA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-white text-xl font-bold">SKP Online SMA</h1>
                    </div>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            @auth
                                @if(auth()->user()->role == 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                    <a href="{{ route('admin.pegawai') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Pegawai</a>
                                    <a href="{{ route('admin.periode') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Periode</a>
                                    <a href="{{ route('admin.jabatan') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Jabatan</a>
                                    <a href="{{ route('admin.laporan') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Laporan</a>
                                @elseif(auth()->user()->role == 'kepala_sekolah')
                                    <a href="{{ route('kepala.dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                    <a href="{{ route('kepala.persetujuan') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Persetujuan SKP</a>
                                    <a href="{{ route('kepala.monitoring') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Monitoring</a>
                                    <a href="{{ route('kepala.laporan') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Laporan</a>
                                @else
                                    <a href="{{ route('pegawai.dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                    <a href="{{ route('pegawai.sasaran') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Sasaran Kerja</a>
                                    <a href="{{ route('pegawai.realisasi') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Realisasi</a>
                                    <a href="{{ route('pegawai.penilaian') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Penilaian</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

            @auth
            <div class="flex items-center space-x-4">
                @include('components.notification-bell')
                <div class="relative">
                    <button id="profileDropdownToggle" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="hidden md:inline ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down ml-1 hidden md:inline text-xs text-gray-500"></i>
                    </button>
                    <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-sign-out-alt mr-2 w-5"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <div class="flex flex-1 pt-16">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-gray-50 border-r border-gray-200 text-gray-700 w-64 min-h-0 p-4 space-y-1 fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 pt-20 lg:pt-4">
            <nav class="mt-4">
                @auth
                    @php
                        $activeClass = 'bg-gray-200 text-blue-600 font-semibold';
                        $inactiveClass = 'hover:bg-gray-200 hover:text-blue-600';
                    @endphp
                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.dashboard') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-tachometer-alt w-5 mr-3 opacity-75"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.pegawai') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.pegawai') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-users w-5 mr-3 opacity-75"></i>Pegawai
                        </a>
                        <a href="{{ route('admin.periode') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.periode') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-calendar-alt w-5 mr-3 opacity-75"></i>Periode
                        </a>
                        <a href="{{ route('admin.jabatan') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.jabatan') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-sitemap w-5 mr-3 opacity-75"></i>Jabatan
                        </a>
                        <a href="{{ route('admin.laporan') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('admin.laporan') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-chart-line w-5 mr-3 opacity-75"></i>Laporan
                        </a>
                    @elseif(auth()->user()->role == 'kepala_sekolah')
                        <a href="{{ route('kepala.dashboard') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('kepala.dashboard') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-tachometer-alt w-5 mr-3 opacity-75"></i>Dashboard
                        </a>
                        <a href="{{ route('kepala.persetujuan') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('kepala.persetujuan') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-check-circle w-5 mr-3 opacity-75"></i>Persetujuan SKP
                        </a>
                        <a href="{{ route('kepala.monitoring') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('kepala.monitoring') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-eye w-5 mr-3 opacity-75"></i>Monitoring
                        </a>
                        <a href="{{ route('kepala.laporan') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('kepala.laporan') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-chart-line w-5 mr-3 opacity-75"></i>Laporan
                        </a>
                     @else
                        <a href="{{ route('pegawai.dashboard') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('pegawai.dashboard') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-tachometer-alt w-5 mr-3 opacity-75"></i>Dashboard
                        </a>
                        <a href="{{ route('pegawai.sasaran') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('pegawai.sasaran') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-bullseye w-5 mr-3 opacity-75"></i>Sasaran Kerja
                        </a>
                        <a href="{{ route('pegawai.realisasi') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('pegawai.realisasi') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-tasks w-5 mr-3 opacity-75"></i>Realisasi
                        </a>
                        <a href="{{ route('pegawai.penilaian') }}" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('pegawai.penilaian') ? $activeClass : $inactiveClass }}">
                            <i class="fas fa-star w-5 mr-3 opacity-75"></i>Penilaian
                        </a>
                    @endif
                @endauth
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 p-6 bg-white">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>


    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto py-4 px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} SKP Online SMA. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>