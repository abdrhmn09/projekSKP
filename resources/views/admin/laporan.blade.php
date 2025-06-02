@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Laporan Administrasi SKP</h1>

        <!-- Filter Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
            <form method="GET" action="{{ route('admin.laporan') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="periode_id" class="block text-sm font-medium text-gray-700">Periode Penilaian</label>
                    <select name="periode_id" id="periode_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="semua" {{ $selectedPeriodeId == 'semua' ? 'selected' : '' }}>Semua Periode</option>
                        @foreach($semuaPeriode as $periode)
                            <option value="{{ $periode->id }}" {{ $selectedPeriodeId == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} ({{ $periode->tanggal_mulai->isoFormat('D MMM Y') }} - {{ $periode->tanggal_selesai->isoFormat('D MMM Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.laporan', request()->except('export')) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                    <a href="{{ route('admin.laporan.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Export Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pegawai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalPegawai }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-check text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Penilaian ({{ $selectedPeriodeId == 'semua' || !$selectedPeriodeId ? 'Global' : ($penilaianSKP->first()->periode->nama_periode ?? 'N/A') }})</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalPenilaianTerfilter }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Periode Aktif Saat Ini</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $periodeAktif->nama_periode ?? 'Tidak Ada' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-star-half-alt text-orange-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Nilai Akhir (Filter)</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $rataRataNilaiAkhir !== null ? number_format($rataRataNilaiAkhir, 2) : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Kategori Nilai SKP (Filter)</h3>
                @if($totalPenilaianTerfilter > 0)
                    <div class="relative h-80">
                        <canvas id="distribusiKategoriChart"></canvas>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-10">Tidak ada data penilaian untuk periode yang dipilih.</p>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pegawai berdasarkan Status Kepegawaian</h3>
                @if($totalPegawai > 0)
                    <div class="relative h-80">
                        <canvas id="pegawaiByStatusChart"></canvas>
                    </div>
                @else
                     <p class="text-gray-500 text-center py-10">Tidak ada data pegawai.</p>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 gap-6 mb-6">
             <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pegawai berdasarkan Jabatan</h3>
                 @if($totalPegawai > 0 && count($pegawaiByJabatan) > 0)
                    <div class="relative h-80">
                        <canvas id="pegawaiByJabatanChart"></canvas>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-10">Tidak ada data pegawai berdasarkan jabatan.</p>
                @endif
            </div>
        </div>


        <!-- Data Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Penilaian SKP (Filter: {{ $selectedPeriodeId == 'semua' || !$selectedPeriodeId ? 'Semua Periode' : ($penilaianSKP->first()->periode->nama_periode ?? 'N/A') }})</h3>
            </div>
            @if($penilaianSKP->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Penilaian</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th> --}}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($penilaianSKP as $index => $penilaian)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $penilaian->pegawai->user->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $penilaian->pegawai->user->nip ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $penilaian->pegawai->jabatan->nama_jabatan ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $penilaian->periode->nama_periode ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ number_format($penilaian->nilai_akhir, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @switch($penilaian->kategori_nilai)
                                    @case('Sangat Baik') bg-green-100 text-green-800 @break
                                    @case('Baik') bg-blue-100 text-blue-800 @break
                                    @case('Butuh Perbaikan') bg-yellow-100 text-yellow-800 @break
                                    @case('Kurang') bg-orange-100 text-orange-800 @break
                                    @case('Sangat Kurang') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                                ">
                                {{ $penilaian->kategori_nilai ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($penilaian->status == 'final') bg-green-100 text-green-800 
                                @else bg-yellow-100 text-yellow-800 
                                @endif">
                                {{ ucfirst($penilaian->status) }}
                            </span>
                        </td>
                        {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Detail</a> // Link to admin.penilaian.detail if exists
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="text-center py-10 px-6">
                    <i class="fas fa-info-circle text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">Tidak ada data penilaian SKP untuk periode yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Distribusi Kategori Chart
        const distribusiKategoriCtx = document.getElementById('distribusiKategoriChart');
        if (distribusiKategoriCtx && {{ $totalPenilaianTerfilter > 0 ? 'true' : 'false' }}) {
            const distribusiKategoriData = @json($distribusiKategori);
            new Chart(distribusiKategoriCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(distribusiKategoriData),
                    datasets: [{
                        label: 'Distribusi Kategori Nilai',
                        data: Object.values(distribusiKategoriData),
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.7)', // Sangat Baik (example color)
                            'rgba(54, 162, 235, 0.7)', // Baik
                            'rgba(255, 206, 86, 0.7)', // Butuh Perbaikan
                            'rgba(255, 159, 64, 0.7)', // Kurang
                            'rgba(255, 99, 132, 0.7)'  // Sangat Kurang
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Pegawai by Status Chart
        const pegawaiByStatusCtx = document.getElementById('pegawaiByStatusChart');
        if (pegawaiByStatusCtx && {{ $totalPegawai > 0 ? 'true' : 'false' }}) {
            const pegawaiByStatusData = @json($pegawaiByStatus);
            new Chart(pegawaiByStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(pegawaiByStatusData),
                    datasets: [{
                        label: 'Pegawai berdasarkan Status',
                        data: Object.values(pegawaiByStatusData),
                        backgroundColor: [
                            'rgba(153, 102, 255, 0.7)', // PNS
                            'rgba(255, 159, 64, 0.7)', // PPPK
                            'rgba(201, 203, 207, 0.7)'  // Honorer
                        ],
                        borderColor: [
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(201, 203, 207, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                     plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Pegawai by Jabatan Chart
        const pegawaiByJabatanCtx = document.getElementById('pegawaiByJabatanChart');
        if (pegawaiByJabatanCtx && {{ $totalPegawai > 0 && count($pegawaiByJabatan) > 0 ? 'true' : 'false' }}) {
            const pegawaiByJabatanData = @json($pegawaiByJabatan);
             // Function to generate random colors for potentially many MFNs
            const jabatanColors = Object.keys(pegawaiByJabatanData).map(() => 
                `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
            );
            const jabatanBorderColors = jabatanColors.map(color => color.replace('0.7','1'));

            new Chart(pegawaiByJabatanCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(pegawaiByJabatanData),
                    datasets: [{
                        label: 'Jumlah Pegawai',
                        data: Object.values(pegawaiByJabatanData),
                        backgroundColor: jabatanColors,
                        borderColor: jabatanBorderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Horizontal bar chart
                    scales: {
                         x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Ensure y-axis has whole numbers if counts are small
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // No need for legend with direct labels or single dataset
                        }
                    }
                }
            });
        }

    });
</script>
@endpush
