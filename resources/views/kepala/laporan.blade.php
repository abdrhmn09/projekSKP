@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Laporan Penilaian SKP</h1>
            
            <!-- Filter Periode -->
            <form action="{{ route('kepala.laporan') }}" method="GET" class="flex items-center space-x-4">
                <select name="periode_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Periode</option>
                    @foreach($periodePenilaian as $periode)
                        <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama_periode }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-white px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Filter
                </button>
            </form>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Dinilai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $statistik['total_dinilai'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Nilai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($statistik['rata_rata'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Nilai Tertinggi</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($statistik['nilai_tertinggi'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Nilai Terendah</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($statistik['nilai_terendah'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Nilai -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Nilai</h3>
            <div class="space-y-3">
                @foreach(['Sangat Baik' => 'green', 'Baik' => 'blue', 'Butuh Perbaikan' => 'yellow', 'Kurang' => 'orange', 'Sangat Kurang' => 'red'] as $kategori => $color)
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">{{ $kategori }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            @php
                                $persentase = $statistik['total_dinilai'] > 0 
                                    ? ($distribusi[$kategori] / $statistik['total_dinilai']) * 100 
                                    : 0;
                            @endphp
                            <div class="bg-{{ $color }}-600 h-2 rounded-full" style="width: {{ $persentase }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $distribusi[$kategori] }}
                            <span class="text-xs text-gray-400">({{ number_format($persentase, 1) }}%)</span>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai SKP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Perilaku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $penilaian)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $penilaian->pegawai->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $penilaian->pegawai->user->nip }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $penilaian->periode->nama_periode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($penilaian->nilai_skp, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($penilaian->nilai_perilaku, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($penilaian->nilai_akhir, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $penilaian->kategori_nilai == 'Sangat Baik' ? 'bg-green-100 text-green-800' : 
                                   ($penilaian->kategori_nilai == 'Baik' ? 'bg-blue-100 text-blue-800' : 
                                   ($penilaian->kategori_nilai == 'Butuh Perbaikan' ? 'bg-yellow-100 text-yellow-800' :
                                   ($penilaian->kategori_nilai == 'Kurang' ? 'bg-orange-100 text-orange-800' :
                                   'bg-red-100 text-red-800'))) }}">
                                {{ $penilaian->kategori_nilai }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $penilaian->tanggal_penilaian->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection
