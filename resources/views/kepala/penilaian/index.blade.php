@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Penilaian SKP Pegawai</h1>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($periodeAktif)
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-blue-900">Periode Aktif: {{ $periodeAktif->nama_periode }}</h3>
                <p class="text-sm text-blue-700">{{ $periodeAktif->tanggal_mulai->format('d/m/Y') }} - {{ $periodeAktif->tanggal_selesai->format('d/m/Y') }}</p>
            </div>

            @if($sasaranApproved->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sasaran Kerja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Penilaian</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sasaranApproved as $sasaran)
                            @php
                                $sudahDinilai = \App\Models\PenilaianSkp::where('pegawai_id', $sasaran->pegawai_id)
                                    ->where('periode_id', $sasaran->periode_id)
                                    ->exists();
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">{{ substr($sasaran->pegawai->user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $sasaran->pegawai->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $sasaran->pegawai->user->nip }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $sasaran->uraian_kegiatan }}</div>
                                    <div class="text-sm text-gray-500">Kode: {{ $sasaran->kode_sasaran }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>Kuantitas: {{ $sasaran->target_kuantitas }}</div>
                                    <div>Kualitas: {{ $sasaran->target_kualitas }}</div>
                                    <div>Waktu: {{ $sasaran->target_waktu }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($sasaran->realisasi)
                                        <div>Kuantitas: {{ $sasaran->realisasi->realisasi_kuantitas ?? '-' }}</div>
                                        <div>Kualitas: {{ $sasaran->realisasi->realisasi_kualitas ?? '-' }}</div>
                                        <div>Waktu: {{ $sasaran->realisasi->realisasi_waktu ?? '-' }}</div>
                                    @else
                                        <span class="text-red-500">Belum ada realisasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sudahDinilai)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Sudah Dinilai
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Belum Dinilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(!$sudahDinilai)
                                        <a href="{{ route('kepala.penilaian.create', $sasaran->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Beri Nilai
                                        </a>
                                    @else
                                        <span class="text-gray-400">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500">Belum ada sasaran kerja yang disetujui untuk periode ini</div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="text-gray-500">Tidak ada periode penilaian yang aktif</div>
            </div>
        @endif
    </div>
</div>
@endsection