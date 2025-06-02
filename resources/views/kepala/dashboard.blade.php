@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Kepala Sekolah</h1>
            <p class="mt-1 text-sm text-gray-600">Monitoring dan persetujuan SKP pegawai. Periode Aktif: {{ $periodeAktif ? $periodeAktif->nama . ' (' . $periodeAktif->tanggal_mulai->format('d M Y') . ' - ' . $periodeAktif->tanggal_selesai->format('d M Y') . ')' : 'Tidak ada periode aktif' }}</p>
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
                            <dd class="text-lg font-medium text-gray-900">{{ $menungguPersetujuanCount ?? 0 }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">SKP Disetujui (Periode Aktif)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $skpDisetujuiCount ?? 0 }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Nilai (Periode Aktif)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $rataRataNilai ?? 'N/A' }}</dd>
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
                <h3 class="text-lg leading-6 font-medium text-gray-900">SKP Terbaru Menunggu Persetujuan (Maks 5)</h3>
                @if($menungguPersetujuanCount > 0)
                <a href="{{ route('kepala.persetujuan') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Lihat Semua ({{ $menungguPersetujuanCount }}) <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Ajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($latestSasaranMenunggu as $sasaran)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <!-- Placeholder for avatar, can be improved with actual images or initials -->
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i> 
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $sasaran->pegawai->user->name ?? ($sasaran->pegawai->nama_lengkap ?? 'N/A') }}</div>
                                        <div class="text-sm text-gray-500">NIP: {{ $sasaran->pegawai->nip ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sasaran->periode->nama ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sasaran->created_at ? $sasaran->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('kepala.persetujuan.detail', $sasaran->id) }}" class="text-blue-600 hover:text-blue-900">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada sasaran yang menunggu persetujuan saat ini.
                            </td>
                        </tr>
                        @endforelse
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
                <h3 class="text-lg leading-6 font-medium text-gray-900">Distribusi Nilai SKP (Final - {{ $periodeAktif ? 'Periode Aktif' : 'Semua Periode' }})</h3>
                <div class="mt-5">
                    @if(!empty($distribusiKategori))
                        @php
                            $kategoriOrder = ['Sangat Baik', 'Baik', 'Cukup', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang'];
                            $colorMap = [
                                'Sangat Baik' => 'green',
                                'Baik' => 'blue',
                                'Cukup' => 'teal', // Changed from yellow for better visibility
                                'Butuh Perbaikan' => 'yellow',
                                'Kurang' => 'orange',
                                'Sangat Kurang' => 'red'
                            ];
                            $totalNilaiDalamDistribusi = array_sum($distribusiKategori);
                        @endphp
                        <div class="space-y-3">
                            @foreach($kategoriOrder as $kategori)
                                @if(isset($distribusiKategori[$kategori]) && $distribusiKategori[$kategori] > 0)
                                    @php
                                        $jumlah = $distribusiKategori[$kategori];
                                        $persentase = ($totalNilaiDalamDistribusi > 0) ? ($jumlah / $totalNilaiDalamDistribusi) * 100 : 0;
                                        $warna = $colorMap[$kategori] ?? 'gray';
                                    @endphp
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700 w-1/3">{{ $kategori }}</span>
                                        <div class="w-2/3 flex items-center space-x-2">
                                            <div class="w-full bg-gray-200 rounded-full h-4">
                                                <div class="bg-{{ $warna }}-500 h-4 rounded-full text-xs font-medium text-white text-center p-0.5 leading-none" style="width: {{ round($persentase) }}%">
                                                   {{ round($persentase) }}% 
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-600 font-semibold w-8 text-right">{{ $jumlah }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada data distribusi nilai untuk ditampilkan.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Aktivitas Terbaru (30 Hari Terakhir)</h3>
                <div class="mt-5">
                    <div class="flow-root">
                        @if(!empty($recentActivities))
                        <ul class="-mb-8">
                            @foreach($recentActivities as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ explode(' ', $activity['icon'])[1] ?? 'bg-gray-400' }} flex items-center justify-center ring-8 ring-white">
                                                <i class="fas {{ explode(' ', $activity['icon'])[0] }} text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-700">{!! $activity['message'] !!}</p> 
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $activity['activity_timestamp']->toIso8601String() }}">{{ $activity['time_diff'] }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-sm text-gray-500">Tidak ada aktivitas terbaru untuk ditampilkan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
