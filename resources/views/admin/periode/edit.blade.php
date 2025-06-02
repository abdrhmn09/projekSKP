@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-200 border-b border-gray-300">
            <h1 class="text-xl font-semibold text-gray-700">Edit Periode Penilaian</h1>
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

            <form action="{{ route('admin.periode.update', $periode->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nama_periode" class="block text-gray-700 text-sm font-bold mb-2">Nama Periode:</label>
                    <input type="text" name="nama_periode" id="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="jenis_periode" class="block text-gray-700 text-sm font-bold mb-2">Jenis Periode:</label>
                    <select name="jenis_periode" id="jenis_periode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="semester" {{ old('jenis_periode', $periode->jenis_periode) == 'semester' ? 'selected' : '' }}>Semester</option>
                        <option value="tahunan" {{ old('jenis_periode', $periode->jenis_periode) == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai:</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai:</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi (Opsional):</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi', $periode->deskripsi) }}</textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.periode') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 