<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom scrollbar for WebKit browsers */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .active-sidebar-link {
            background-color: #4a5568; /* A slightly lighter shade than the sidebar bg */
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <header class="bg-gray-900 text-white shadow-md fixed w-full z-40 flex items-center h-16">
        <!-- Hamburger button, flush left with its own padding -->
        <button id="sidebarToggle" class="text-gray-400 hover:text-white focus:outline-none focus:text-white px-4 h-full flex items-center">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <a href="{{ Auth::check() ? (auth()->user()->role == 'admin' ? route('admin.dashboard') : (auth()->user()->role == 'kepala_sekolah' ? route('kepala.dashboard') : route('pegawai.dashboard'))) : url('/') }}" class="flex items-center">
            <span class="font-semibold text-xl tracking-tight">SKP Online</span>
        </a>
    
        <!-- Remainder of header content, using a container for centering/padding -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex-1 flex items-center justify-between h-full">
            <!-- Left part of container: Logo/Title -->
            <div class="flex items-center">
                
            </div>
    
            <!-- Right part of container: DateTime, Role, Notifications, Profile -->
            <div class="flex items-center space-x-3">
                <div id="liveDateTime" class="text-sm text-gray-300 hidden sm:block">
                    <span id="liveDate"></span> <span id="liveTime"></span>
                </div>
                
                @auth
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        @if(auth()->user()->role == 'admin') bg-purple-600 text-white
                        @elseif(auth()->user()->role == 'kepala_sekolah') bg-blue-600 text-white
                        @else bg-red-500 text-white
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
    
                    @include('components.notification-bell')
                    
                    <div class="relative">
                        <button id="profileDropdownToggle" class="flex items-center text-sm focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-300 font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:inline ml-2 text-gray-300 hover:text-white">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1 hidden md:inline text-xs text-gray-400"></i>
                        </button>
                        <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                    <i class="fas fa-sign-out-alt mr-2 opacity-75"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-gray-300 transform -translate-x-full transition-transform duration-300 ease-in-out custom-scrollbar overflow-y-auto pt-16">
            <div class="px-4 py-6">
                <!-- Logo for Sidebar (optional, if different from header) -->
                

                @auth
                    @php
                        $activeClass = 'bg-gray-700 text-white'; // More distinct active class for dark sidebar
                        $inactiveClass = 'hover:bg-gray-700 hover:text-white';
                        $currentRouteName = Route::currentRouteName();

                        $role = auth()->user()->role;
                        $sidebarTitle = '';
                        $navLinks = [];

                        if ($role == 'admin') {
                            $sidebarTitle = 'ADMINISTRATOR';
                            $navLinks = [
                                ['route' => 'admin.dashboard', 'icon' => 'fa-tachometer-alt', 'text' => 'Dashboard'],
                                ['route' => 'admin.pegawai', 'icon' => 'fa-users', 'text' => 'Kelola Pegawai'],
                                ['route' => 'admin.periode', 'icon' => 'fa-calendar-alt', 'text' => 'Kelola Periode'],
                                ['route' => 'admin.jabatan', 'icon' => 'fa-sitemap', 'text' => 'Kelola Jabatan'],
                                ['route' => 'admin.laporan', 'icon' => 'fa-chart-line', 'text' => 'Laporan SKP'],
                            ];
                        } elseif ($role == 'kepala_sekolah') {
                            $sidebarTitle = 'KEPALA SEKOLAH';
                            $navLinks = [
                                ['route' => 'kepala.dashboard', 'icon' => 'fa-tachometer-alt', 'text' => 'Dashboard'],
                                ['route' => 'kepala.persetujuan', 'icon' => 'fa-check-circle', 'text' => 'Persetujuan SKP'],
                                ['route' => 'kepala.penilaian-skp.index', 'icon' => 'fa-clipboard-check', 'text' => 'Penilaian SKP'],
                                ['route' => 'kepala.monitoring', 'icon' => 'fa-eye', 'text' => 'Monitoring Pegawai'],
                                ['route' => 'kepala.laporan', 'icon' => 'fa-chart-bar', 'text' => 'Laporan Kinerja'],
                            ];
                        } elseif ($role == 'guru' || $role == 'staff') { // Assuming 'pegawai' covers 'guru' and 'staff'
                            $sidebarTitle = strtoupper(str_replace('_', ' ', $role));
                             $navLinks = [
                                ['route' => 'pegawai.dashboard', 'icon' => 'fa-tachometer-alt', 'text' => 'Dashboard'],
                                ['route' => 'pegawai.sasaran', 'icon' => 'fa-bullseye', 'text' => 'Sasaran Kerja'],
                                ['route' => 'pegawai.realisasi', 'icon' => 'fa-tasks', 'text' => 'Realisasi SKP'],
                                ['route' => 'pegawai.penilaian', 'icon' => 'fa-star', 'text' => 'Hasil Penilaian'],
                            ];
                        }
                    @endphp

                    @if($sidebarTitle)
                    <h3 class="px-4 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">{{ $sidebarTitle }}</h3>
                    @endif
                    
                    <nav class="mt-2">
                        @foreach($navLinks as $link)
                            @if(Route::has($link['route']))
                            <a href="{{ route($link['route']) }}"
                               class="flex items-center px-4 py-2.5 rounded-lg transition-colors duration-200 mb-1 {{ Str::startsWith($currentRouteName, Str::beforeLast($link['route'], '.')) ? $activeClass : $inactiveClass }}">
                                <i class="fas {{ $link['icon'] }} w-5 mr-3 opacity-75"></i>
                                {{ $link['text'] }}
                            </a>
                            @endif
                        @endforeach
                    </nav>
                @endauth
            </div>
        </aside>
        <!-- Sidebar Overlay for mobile -->
        <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"></div>

        <!-- Main content -->
        <div id="mainContentWrapper" class="flex-1 flex flex-col overflow-hidden transition-[margin-left] duration-300 ease-in-out pt-16">
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow-sm" role="alert">
                            <div class="flex">
                                <div class="py-1"><i class="fas fa-check-circle mr-2"></i></div>
                                <div>{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow-sm" role="alert">
                             <div class="flex">
                                <div class="py-1"><i class="fas fa-exclamation-circle mr-2"></i></div>
                                <div>{{ session('error') }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow-sm" role="alert">
                            <div class="font-bold mb-1">Oops! Ada beberapa kesalahan:</div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('modals')
    @stack('scripts')
    <script>
        // Live Date Time Clock
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }; // Use hour12: true for AM/PM
            
            const liveDateEl = document.getElementById('liveDate');
            const liveTimeEl = document.getElementById('liveTime');

            if(liveDateEl) liveDateEl.textContent = now.toLocaleDateString('id-ID', dateOptions); // Indonesian locale
            if(liveTimeEl) liveTimeEl.textContent = now.toLocaleTimeString('id-ID', timeOptions);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime(); // Initial call
    </script>
</body>
</html>