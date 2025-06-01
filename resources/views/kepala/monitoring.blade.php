
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Monitoring Progress SKP</h1>

        @if($periodeAktif)
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
                <p><strong>Periode Aktif:</strong> {{ $periodeAktif->nama_periode }}</p>
                <p><strong>Durasi:</strong> {{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d/m/Y') }}</p>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sasaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sasaranData as $sasaran)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $sasaran->pegawai->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $sasaran->pegawai->user->nip }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $sasaran->uraian_sasaran }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sasaran->target_kuantitas }} {{ $sasaran->satuan_kuantitas }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($sasaran->realisasiKerja->count() > 0)
                                    {{ $sasaran->realisasiKerja->sum('realisasi_kuantitas') }} {{ $sasaran->satuan_kuantitas }}
                                @else
                                    Belum ada
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $progress = $sasaran->realisasiKerja->count() > 0 
                                        ? ($sasaran->realisasiKerja->sum('realisasi_kuantitas') / $sasaran->target_kuantitas) * 100 
                                        : 0;
                                    $progress = min(100, $progress);
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ number_format($progress, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                Tidak ada periode penilaian yang aktif saat ini.
            </div>
        @endif
    </div>
</div>
@endsection
