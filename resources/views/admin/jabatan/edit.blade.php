@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-200 border-b border-gray-300">
            <h1 class="text-xl font-semibold text-gray-700">Edit Jabatan</h1>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.jabatan.update', $jabatan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nama_jabatan" class="block text-gray-700 text-sm font-bold mb-2">Nama Jabatan:</label>
                    <input type="text" name="nama_jabatan" id="nama_jabatan" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="kode_jabatan" class="block text-gray-700 text-sm font-bold mb-2">Kode Jabatan:</label>
                    <input type="text" name="kode_jabatan" id="kode_jabatan" value="{{ old('kode_jabatan', $jabatan->kode_jabatan) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi (Opsional):</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi', $jabatan->deskripsi) }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="tunjangan_jabatan" class="block text-gray-700 text-sm font-bold mb-2">Tunjangan Jabatan:</label>
                    <input type="number" name="tunjangan_jabatan" id="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', $jabatan->tunjangan_jabatan) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.jabatan') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 