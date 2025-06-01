@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Edit Data Pegawai</h2>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kolom Kiri -->
                        <div>
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $pegawai->nama_lengkap) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $pegawai->user->email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                                <input type="text" name="nip" id="nip" value="{{ old('nip', $pegawai->user->nip) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('nip') border-red-500 @enderror" required>
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $pegawai->user->phone) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" name="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('role') border-red-500 @enderror" required>
                                    <option value="guru" {{ old('role', $pegawai->user->role) == 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="staff" {{ old('role', $pegawai->user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="jabatan_id" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <select name="jabatan_id" id="jabatan_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('jabatan_id') border-red-500 @enderror" required>
                                    @foreach($jabatan as $jab)
                                        <option value="{{ $jab->id }}" {{ old('jabatan_id', $pegawai->jabatan_id) == $jab->id ? 'selected' : '' }}>
                                            {{ $jab->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan_id')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div>
                            <div class="mb-4">
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('tanggal_lahir') border-red-500 @enderror" required>
                                @error('tanggal_lahir')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('jenis_kelamin') border-red-500 @enderror" required>
                                    <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea name="alamat" id="alamat" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('alamat') border-red-500 @enderror" required>{{ old('alamat', $pegawai->alamat) }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('pendidikan_terakhir') border-red-500 @enderror" required>
                                @error('pendidikan_terakhir')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="tanggal_masuk_kerja" class="block text-sm font-medium text-gray-700">Tanggal Masuk Kerja</label>
                                <input type="date" name="tanggal_masuk_kerja" id="tanggal_masuk_kerja" value="{{ old('tanggal_masuk_kerja', $pegawai->tanggal_masuk_kerja) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('tanggal_masuk_kerja') border-red-500 @enderror" required>
                                @error('tanggal_masuk_kerja')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="status_kepegawaian" class="block text-sm font-medium text-gray-700">Status Kepegawaian</label>
                                <select name="status_kepegawaian" id="status_kepegawaian" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status_kepegawaian') border-red-500 @enderror" required>
                                    <option value="PNS" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                    <option value="PPPK" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                    <option value="Honorer" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                                </select>
                                @error('status_kepegawaian')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="golongan" class="block text-sm font-medium text-gray-700">Golongan</label>
                                <input type="text" name="golongan" id="golongan" value="{{ old('golongan', $pegawai->golongan) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('golongan') border-red-500 @enderror">
                                @error('golongan')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.pegawai') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Kembali
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 