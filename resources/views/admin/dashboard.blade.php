@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-gray-600">Selamat datang di sistem SKP Online SMA</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Pegawai -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-indigo-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pegawai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalPegawai ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Periode Aktif -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Periode Aktif</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @if($periodeAktif)
                                    {{ $periodeAktif->nama_periode }}
                                    <span class="block text-xs text-gray-500">
                                        {{ $periodeAktif->tanggal_mulai->isoFormat('LL') }} - {{ $periodeAktif->tanggal_selesai->isoFormat('LL') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- SKP Menunggu -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">SKP Menunggu</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $skpMenunggu ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- SKP Selesai -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">SKP Selesai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $skpSelesai ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.pegawai.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-user-plus mr-2"></i>
                    Tambah Pegawai
                </a>
                <a href="{{ route('admin.periode.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Buat Periode
                </a>
                <a href="{{ route('admin.jabatan.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-briefcase mr-2"></i>
                    Tambah Jabatan
                </a>
                <a href="{{ route('admin.laporan') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Aktivitas Terbaru</h3>
            <div class="mt-5">
                <div class="flow-root">
                    @if($activities->isNotEmpty())
                    <ul class="-mb-8">
                        @foreach($activities as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-{{ $activity->color }}-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas {{ $activity->icon }} text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-700">{!! $activity->description !!}</p> 
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time title="{{ $activity->time->format('Y-m-d H:i:s') }}">{{ $activity->time->diffForHumans() }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500">Tidak ada aktivitas terbaru.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
