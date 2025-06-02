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
                    <p class="text-sm text-gray-500">NIP: {{ auth()->user()->pegawai->nip ?? auth()->user()->nip ?? '-' }}</p>
                    @if($periodeAktif)
                        <p class="text-sm text-gray-500">
                            Periode Aktif: 
                            <span class="font-semibold text-gray-700">{{ $periodeAktif->nama_periode }}</span> 
                            ({{ $periodeAktif->tanggal_mulai->format('d M Y') }} - {{ $periodeAktif->tanggal_selesai->format('d M Y') }})
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Periode Aktif: <span class="font-semibold text-red-600">Tidak Ada</span></p>
                    @endif
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
                            <dd class="text-xs text-gray-500">{{ $sasaranApprovedCount ?? 0 }} disetujui</dd>
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
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($progressRealisasi ?? 0, 2) }}%</dd>
                            <dd class="text-xs text-gray-500">sasaran terealisasi</dd>
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
                <a href="{{ route('pegawai.sasaran.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Sasaran
                </a>
                <a href="{{ route('pegawai.realisasi.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>
                    Input Realisasi
                </a>
                <a href="{{ route('pegawai.penilaian') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Penilaian
                </a>
                <a href="{{ route('pegawai.rencana') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                    <i class="fas fa-tasks mr-2"></i>
                    Rencana Tindak Lanjut
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Chart -->
    <div class="grid grid-cols-1 @if($sasaranKerjaDetails->count() > 0) lg:grid-cols-2 @endif gap-6">
        <!-- Sasaran Kerja Progress -->
        @if($sasaranKerjaDetails->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Progress Sasaran Kerja</h3>
                <div class="mt-5 space-y-4">
                    @foreach($sasaranKerjaDetails as $sasaran)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700 truncate" title="{{ $sasaran->uraian_sasaran ?? $sasaran->uraian_kegiatan }}">
                                {{ Str::limit($sasaran->uraian_sasaran ?? $sasaran->uraian_kegiatan, 50) }}
                            </span>
                            <span class="text-gray-600 font-semibold">{{ number_format($sasaran->progress ?? 0, 2) }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $sasaran->progress ?? 0 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="bg-white shadow rounded-lg lg:col-span-1">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Progress Sasaran Kerja</h3>
                <p class="text-gray-500 text-center py-8">Belum ada sasaran kerja yang dibuat atau disetujui pada periode aktif ini.</p>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white shadow rounded-lg @if($sasaranKerjaDetails->isEmpty()) lg:col-span-2 @endif">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Timeline Periode Aktif</h3>
                <div class="mt-5">
                    @if($periodeAktif)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-calendar-alt text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Periode Dimulai</p>
                                            <p class="text-xs text-gray-400 font-medium">{{ $periodeAktif->tanggal_mulai->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="relative pb-8">
                                     <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-flag-checkered text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Batas Akhir Pengajuan Sasaran & Realisasi</p>
                                            <p class="text-xs text-gray-400 font-medium">{{ $deadlineSasaran ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                             <li>
                                <div class="relative pb-2">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-calendar-check text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Periode Selesai (Penilaian)</p>
                                            <p class="text-xs text-gray-400 font-medium">{{ $periodeAktif->tanggal_selesai->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">Tidak ada periode penilaian yang aktif.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

