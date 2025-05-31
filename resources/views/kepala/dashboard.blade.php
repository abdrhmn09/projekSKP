
@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Kepala Sekolah</h1>
            <p class="mt-1 text-sm text-gray-600">Monitoring dan persetujuan SKP pegawai</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Pegawai -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
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

        <!-- Menunggu Persetujuan -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Persetujuan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $menungguPersetujuan ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- SKP Disetujui -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">SKP Disetujui</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $skpDisetujui ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Nilai</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $rataRataNilai ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">SKP Menunggu Persetujuan</h3>
                <a href="{{ route('kepala.persetujuan') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sasaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Ajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($sasaranMenunggu) && $sasaranMenunggu->count() > 0)
                            @foreach($sasaranMenunggu as $sasaran)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $sasaran->pegawai->nama_lengkap ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $sasaran->pegawai->nip ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($sasaran->uraian_kegiatan, 50) }}</div>
                                    <div class="text-sm text-gray-500">Bobot: {{ $sasaran->bobot_persen }}%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sasaran->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('kepala.persetujuan.detail', $sasaran->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Review</a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada sasaran yang menunggu persetujuan
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Performance Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Distribusi Nilai SKP</h3>
                <div class="mt-5">
                    <div class="space-y-3">
                        @foreach(['Sangat Baik' => 'green', 'Baik' => 'blue', 'Butuh Perbaikan' => 'yellow', 'Kurang' => 'orange', 'Sangat Kurang' => 'red'] as $kategori => $color)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">{{ $kategori }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-{{ $color }}-600 h-2 rounded-full" style="width: {{ $distribusiNilai[$kategori] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $jumlahNilai[$kategori] ?? 0 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Aktivitas Terbaru</h3>
                <div class="mt-5">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">SKP <span class="font-medium text-gray-900">Ahmad Subagja</span> disetujui</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time>1 jam yang lalu</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-file text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Sasaran baru dari <span class="font-medium text-gray-900">Siti Aminah</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time>3 jam yang lalu</time>
                                            </div>
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
