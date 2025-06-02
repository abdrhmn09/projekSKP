@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-200 border-b border-gray-300">
            <h1 class="text-xl font-semibold text-gray-700">Edit Data Pegawai: {{ $pegawai->user->name }}</h1>
        </div>

        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda:</span>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun Pengguna</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $pegawai->user->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $pegawai->user->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="nip" class="block text-gray-700 text-sm font-bold mb-2">NIP:</label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip', $pegawai->user->nip) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">No. Telepon (Opsional):</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $pegawai->user->phone) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                        <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="guru" {{ old('role', $pegawai->user->role) == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="staff" {{ old('role', $pegawai->user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="kepala_sekolah" {{ old('role', $pegawai->user->role) == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                             <option value="admin" {{ old('role', $pegawai->user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <h2 class="text-lg font-semibold text-gray-700 mb-4 mt-8">Informasi Kepegawaian</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                     <div>
                        <label for="jabatan_id" class="block text-gray-700 text-sm font-bold mb-2">Jabatan:</label>
                        <select name="jabatan_id" id="jabatan_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @foreach($jabatan as $j)
                                <option value="{{ $j->id }}" {{ old('jabatan_id', $pegawai->jabatan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_lahir" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir:</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? ( $pegawai->tanggal_lahir instanceof \Carbon\Carbon ? $pegawai->tanggal_lahir->format('Y-m-d') : $pegawai->tanggal_lahir ) : '' ) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="jenis_kelamin" class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin:</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="pendidikan_terakhir" class="block text-gray-700 text-sm font-bold mb-2">Pendidikan Terakhir:</label>
                        <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="tanggal_masuk_kerja" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Masuk Kerja:</label>
                        <input type="date" name="tanggal_masuk_kerja" id="tanggal_masuk_kerja" value="{{ old('tanggal_masuk_kerja', $pegawai->tanggal_masuk_kerja ? ( $pegawai->tanggal_masuk_kerja instanceof \Carbon\Carbon ? $pegawai->tanggal_masuk_kerja->format('Y-m-d') : $pegawai->tanggal_masuk_kerja ) : '' ) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label for="status_kepegawaian" class="block text-gray-700 text-sm font-bold mb-2">Status Kepegawaian:</label>
                        <select name="status_kepegawaian" id="status_kepegawaian" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="PNS" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="Honorer" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat:</label>
                        <textarea name="alamat" id="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('alamat', $pegawai->alamat) }}</textarea>
                    </div>
                     <div>
                        <label for="golongan" class="block text-gray-700 text-sm font-bold mb-2">Golongan (Opsional):</label>
                        <input type="text" name="golongan" id="golongan" value="{{ old('golongan', $pegawai->golongan) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <h2 class="text-lg font-semibold text-gray-700 mb-4 mt-8">Ubah Password (Opsional)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
                        <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-8">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.pegawai') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 