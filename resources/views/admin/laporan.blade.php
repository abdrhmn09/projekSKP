@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Laporan SKP</h1>

        <!-- Filter Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode</label>
                    <select name="periode_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Periode</option>
                        @foreach($periodePenilaian as $periode)
                            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mr-2">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.laporan.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Penilaian</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalPenilaian }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Periode Aktif</dt>
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
                            <i class="fas fa-chart-line text-orange-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Nilai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($rataRataNilai, 1) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Nilai SKP</h3>
                <div class="h-64">
                    <canvas id="distribusiChart"></canvas>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Penilaian per Bulan</h3>
                <div class="h-64">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Laporan SKP</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai SKP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dataPenilaian as $penilaian)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $penilaian->pegawai->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $penilaian->pegawai->user->nip }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $penilaian->periode->nama_periode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($penilaian->nilai_akhir, 1) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $kategoriColor = match($penilaian->kategori_nilai) {
                                        'Sangat Baik' => 'green',
                                        'Baik' => 'blue',
                                        'Butuh Perbaikan' => 'yellow',
                                        'Kurang' => 'orange',
                                        'Sangat Kurang' => 'red',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $kategoriColor }}-100 text-{{ $kategoriColor }}-800">
                                    {{ $penilaian->kategori_nilai }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($penilaian->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.penilaian.detail', $penilaian->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data penilaian
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $dataPenilaian->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log data yang diterima
    console.log('Distribusi Data:', @json($distribusiNilai));
    console.log('Progress Data:', @json($progressBulanan));

    // Distribusi Chart
    const distribusiCtx = document.getElementById('distribusiChart');
    if (distribusiCtx) {
        const distribusiData = @json($distribusiNilai);
        const distribusiLabels = Object.keys(distribusiData);
        const distribusiValues = Object.values(distribusiData);

        new Chart(distribusiCtx, {
            type: 'pie',
            data: {
                labels: distribusiLabels,
                datasets: [{
                    data: distribusiValues,
                    backgroundColor: [
                        '#34D399', // Sangat Baik - Green
                        '#60A5FA', // Baik - Blue
                        '#FBBF24', // Butuh Perbaikan - Yellow
                        '#FB923C', // Kurang - Orange
                        '#F87171'  // Sangat Kurang - Red
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    }
                }
            }
        });
    } else {
        console.error('Canvas distribusiChart tidak ditemukan');
    }

    // Progress Chart
    const progressCtx = document.getElementById('progressChart');
    if (progressCtx) {
        const progressData = @json($progressBulanan);
        const bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const progressValues = Object.values(progressData);

        new Chart(progressCtx, {
            type: 'bar',
            data: {
                labels: bulanNames,
                datasets: [{
                    label: 'Jumlah Penilaian',
                    data: progressValues,
                    backgroundColor: '#60A5FA',
                    borderColor: '#2563EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        console.error('Canvas progressChart tidak ditemukan');
    }
});
</script>
@endpush

@endsection
