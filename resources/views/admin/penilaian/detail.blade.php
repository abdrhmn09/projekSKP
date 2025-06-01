@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Detail Penilaian SKP</h1>
            <a href="{{ route('admin.laporan') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                Kembali
            </a>
        </div>

        <!-- Info Pegawai -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Pegawai</h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->pegawai->user->name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">NIP</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->pegawai->user->nip }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Jabatan</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->pegawai->jabatan->nama_jabatan }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Info Penilaian -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Penilaian</h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Periode</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->periode->nama_periode }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tanggal Penilaian</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->tanggal_penilaian->format('d F Y') }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Penilai</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->penilai->name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nilai SKP</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($penilaian->nilai_skp, 1) }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nilai Perilaku</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($penilaian->nilai_perilaku, 1) }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nilai Akhir</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="font-semibold">{{ number_format($penilaian->nilai_akhir, 1) }}</span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($penilaian->kategori_nilai == 'Sangat Baik') bg-green-100 text-green-800
                                @elseif($penilaian->kategori_nilai == 'Baik') bg-blue-100 text-blue-800
                                @elseif($penilaian->kategori_nilai == 'Butuh Perbaikan') bg-yellow-100 text-yellow-800
                                @elseif($penilaian->kategori_nilai == 'Kurang') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $penilaian->kategori_nilai }}
                            </span>
                        </dd>
                    </div>
                    @if($penilaian->catatan_penilaian)
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Catatan Penilaian</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $penilaian->catatan_penilaian }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Sasaran dan Realisasi -->
        @if($penilaian->sasaranKerja)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sasaran dan Realisasi Kerja</h3>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Kerja</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($penilaian->sasaranKerja->realisasiKerja as $realisasi)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $realisasi->rencana_kerja }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $realisasi->target_kuantitas }} {{ $realisasi->satuan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $realisasi->realisasi_kuantitas }} {{ $realisasi->satuan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $progress = ($realisasi->realisasi_kuantitas / $realisasi->target_kuantitas) * 100;
                                    $progressColor = $progress >= 100 ? 'green' : ($progress >= 75 ? 'blue' : ($progress >= 50 ? 'yellow' : 'red'));
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $progressColor }}-600 h-2.5 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ number_format($progress, 0) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 