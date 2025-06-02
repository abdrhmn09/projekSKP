@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Realisasi Kerja</h1>
            <a href="{{ route('pegawai.realisasi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Realisasi
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sasaran Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Realisasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kualitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Dukung</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($realisasi as $r)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $r->sasaranKerja->uraian_sasaran ?? 'N/A' }}">{{ $r->sasaranKerja->uraian_sasaran ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $r->uraian_realisasi }}">{{ $r->uraian_realisasi }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $r->realisasi_kuantitas }} / {{ $r->sasaranKerja->target_kuantitas ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $r->realisasi_kualitas }}% / {{ $r->sasaranKerja->target_kualitas ?? 'N/A' }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $r->realisasi_waktu ? \Carbon\Carbon::parse($r->realisasi_waktu)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($r->bukti_dukung)
                                <a href="{{ route('pegawai.realisasi.bukti_dukung', basename($r->bukti_dukung)) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline"><i class="fas fa-paperclip mr-1"></i>Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('pegawai.realisasi.edit', $r->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3"><i class="fas fa-edit mr-1"></i>Edit</a>
                            <form action="{{ route('pegawai.realisasi.destroy', $r->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus realisasi ini beserta bukti dukungnya?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash mr-1"></i>Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada data realisasi kerja.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $realisasi->links() }}
        </div>
    </div>
</div>
@endsection
