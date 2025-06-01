@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header dengan background biru tua -->
        <div class="bg-blue-900 rounded-t-lg p-6 mb-0">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Daftar Sasaran Kerja</h2>
                <a href="{{ route('pegawai.sasaran.create') }}" 
                   class="bg-white text-blue-900 hover:bg-blue-100 font-bold py-2 px-4 rounded inline-flex items-center transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Sasaran
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-600 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Sukses!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-600 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Table Container with shadow and rounded corners -->
        <div class="bg-white shadow-lg rounded-b-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian Kegiatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sasaran as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->kode_sasaran }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($item->uraian_kegiatan, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <span class="font-medium text-blue-900 w-24">Kuantitas:</span>
                                    <span>{{ $item->target_kuantitas }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium text-blue-900 w-24">Kualitas:</span>
                                    <span>{{ $item->target_kualitas }}%</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium text-blue-900 w-24">Waktu:</span>
                                    <span>{{ $item->target_waktu }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $item->status === 'approved' ? 'bg-green-100 text-green-800 border border-green-600' : 
                                   ($item->status === 'rejected' ? 'bg-red-100 text-red-800 border border-red-600' : 
                                   ($item->status === 'submitted' ? 'bg-yellow-100 text-yellow-800 border border-yellow-600' : 'bg-gray-100 text-gray-800 border border-gray-600')) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->periode->nama_periode }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <!-- Detail Button -->
                                <a href="{{ route('pegawai.sasaran.detail', $item->id) }}" 
                                   class="text-blue-900 hover:text-blue-700 transition duration-150 ease-in-out"
                                   title="Lihat Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <!-- Edit Button -->
                                @if(in_array($item->status, ['draft', 'rejected']))
                                <a href="{{ route('pegawai.sasaran.edit', $item->id) }}" 
                                   class="text-amber-600 hover:text-amber-500 transition duration-150 ease-in-out"
                                   title="Edit Sasaran">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif

                                <!-- Delete Button -->
                                @if(in_array($item->status, ['draft', 'rejected']))
                                <form action="{{ route('pegawai.sasaran.destroy', $item->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-500 transition duration-150 ease-in-out"
                                            title="Hapus Sasaran"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus sasaran kerja ini?')">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 bg-gray-50">
                            <div class="flex flex-col items-center justify-center py-6">
                                <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-lg font-medium">Tidak ada sasaran kerja yang ditemukan</span>
                                <p class="text-gray-500 mt-1">Silakan tambah sasaran kerja baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination with custom styling -->
        <div class="mt-6">
            {{ $sasaran->links() }}
        </div>
    </div>
</div>
@endsection
