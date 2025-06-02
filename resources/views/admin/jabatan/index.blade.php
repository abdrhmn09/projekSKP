@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kelola Jabatan</h1>
            <a href="{{ route('admin.jabatan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Jabatan
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tunjangan Jabatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jabatan as $j)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $j->nama_jabatan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $j->kode_jabatan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $j->tunjangan_jabatan ? 'Rp ' . number_format($j->tunjangan_jabatan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.jabatan.edit', $j->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.jabatan.destroy', $j->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini? Tindakan ini tidak dapat diurungkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                    <i class="fas fa-trash-alt mr-1"></i>Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada data jabatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="mt-4">
            {{ $jabatan->links() }}
        </div>
    </div>
</div>
@endsection
