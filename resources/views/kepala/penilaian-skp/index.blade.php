@extends('layouts.app')

@section('title', 'Penilaian SKP Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Daftar SKP Pegawai untuk Penilaian</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Perhatian!</strong>
            <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('kepala.penilaian-skp.index') }}" class="mb-6">
        <div class="flex">
            <input type="text" name="search" placeholder="Cari nama pegawai..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                   value="{{ request('search') }}">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Cari
            </button>
        </div>
    </form>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Penilaian</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($sasaranKerja as $index => $skp)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sasaranKerja->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $skp->pegawai->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $skp->periode->nama ?? '-' }} ({{ $skp->periode->tanggal_mulai_formatted }} - {{ $skp->periode->tanggal_selesai_formatted }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($skp->penilaianSkp)
                                @if ($skp->penilaianSkp->status == 'final')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Final</span>
                                @elseif ($skp->penilaianSkp->status == 'draft')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $skp->penilaianSkp && $skp->penilaianSkp->status == 'final' ? ($skp->penilaianSkp->nilai_akhir ?? '-') . ' (' . ($skp->penilaianSkp->kategori_nilai ?? '-') . ')' : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if (!$skp->penilaianSkp || $skp->penilaianSkp->status != 'final')
                                <a href="{{ route('kepala.penilaian-skp.create', $skp->id) }}" class="text-indigo-600 hover:text-indigo-900">Beri Nilai</a>
                            @else
                                <a href="{{ route('kepala.penilaian-skp.create', $skp->id) }}" class="text-gray-500 hover:text-gray-700">Lihat/Edit Nilai</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada SKP yang perlu dinilai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">
            {{ $sasaranKerja->links() }}
        </div>
    </div>
</div>
@endsection 