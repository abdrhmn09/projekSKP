
@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Pegawai</h1>
                    <p class="mt-1 text-sm text-gray-600">Selamat datang, {{ auth()->user()->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">NIP: {{ auth()->user()->nip ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Periode Aktif: {{ $periodeAktif ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Sasaran Kerja -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-target text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Sasaran Kerja</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalSasaran ?? 0 }}</dd>
                            <dd class="text-xs text-gray-500">{{ $sasaranDisetujui ?? 0 }} disetujui</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Realisasi -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Progress Realisasi</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $progressRealisasi ?? 0 }}%</dd>
                            <dd class="text-xs text-gray-500">dari target keseluruhan</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai SKP -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Nilai SKP</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $nilaiSKP ?? '-' }}</dd>
                            <dd class="text-xs text-gray-500">{{ $kategoriNilai ?? 'Belum dinilai' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Penilaian -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clipboard-check text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Status Penilaian</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $statusPenilaian ?? 'Belum dimulai' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Aksi Cepat</h3>
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('pegawai.sasaran.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Sasaran
                </a>
                <a href="{{ route('pegawai.realisasi.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>
                    Input Realisasi
                </a>
                <a href="{{ route('pegawai.penilaian') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Penilaian
                </a>
                <a href="{{ route('pegawai.rencana') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                    <i class="fas fa-tasks mr-2"></i>
                    Rencana Tindak Lanjut
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sasaran Kerja Progress -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Progress Sasaran Kerja</h3>
                <div class="mt-5">
                    @if(isset($sasaranKerja) && $sasaranKerja->count() > 0)
                        @foreach($sasaranKerja as $sasaran)
                        <div class="mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $sasaran->uraian_kegiatan }}</span>
                                <span class="text-gray-500">{{ $sasaran->progress ?? 0 }}%</span>
                            </div>
                            <div class="mt-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $sasaran->progress ?? 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada sasaran kerja yang dibuat</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Timeline Kegiatan</h3>
                <div class="mt-5">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-target text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Sasaran kerja dibuat</p>
                                            <p class="text-xs text-gray-400">Deadline: {{ $deadlineSasaran ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Input realisasi kerja</p>
                                            <p class="text-xs text-gray-400">Deadline: {{ $deadlineRealisasi ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
