@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Tambah Jabatan</h1>

        <form action="{{ route('admin.jabatan.store') }}" method="POST" class="bg-white shadow-sm rounded-lg p-6">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                    <input type="text" name="nama_jabatan" value="{{ old('nama_jabatan') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('nama_jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Jabatan</label>
                    <input type="text" name="kode_jabatan" value="{{ old('kode_jabatan') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                     <p class="mt-1 text-sm text-gray-500">Contoh: GR, WK, KS. Kode ini harus unik.</p>
                    @error('kode_jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                    <input type="number" name="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', 0) }}" min="0" step="1000" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('tunjangan_jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.jabatan') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
