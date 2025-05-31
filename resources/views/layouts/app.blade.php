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
                    <div class="text-white text-sm">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="text-blue-200">({{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }})</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-4 px-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} SKP Online SMA. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>