@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('pegawai.sasaran') }}" class="text-blue-600 hover:text-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6">Edit Sasaran Kerja</h2>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pegawai.sasaran.update', $sasaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="periode_id" class="block text-sm font-medium text-gray-700">Periode Penilaian</label>
                            <select id="periode_id" name="periode_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($periode as $p)
                                    <option value="{{ $p->id }}" {{ $sasaran->periode_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="kode_sasaran" class="block text-sm font-medium text-gray-700">Kode Sasaran</label>
                            <input type="text" name="kode_sasaran" id="kode_sasaran" value="{{ old('kode_sasaran', $sasaran->kode_sasaran) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="uraian_kegiatan" class="block text-sm font-medium text-gray-700">Uraian Kegiatan</label>
                            <textarea name="uraian_kegiatan" id="uraian_kegiatan" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('uraian_kegiatan', $sasaran->uraian_kegiatan) }}</textarea>
                        </div>

                        <div>
                            <label for="target_kuantitas" class="block text-sm font-medium text-gray-700">Target Kuantitas</label>
                            <input type="text" name="target_kuantitas" id="target_kuantitas" value="{{ old('target_kuantitas', $sasaran->target_kuantitas) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="target_kualitas" class="block text-sm font-medium text-gray-700">Target Kualitas (%)</label>
                            <input type="number" name="target_kualitas" id="target_kualitas" value="{{ old('target_kualitas', $sasaran->target_kualitas) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" max="100">
                        </div>

                        <div>
                            <label for="target_waktu" class="block text-sm font-medium text-gray-700">Target Waktu</label>
                            <input type="date" name="target_waktu" id="target_waktu" value="{{ old('target_waktu', $sasaran->target_waktu) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="bobot_persen" class="block text-sm font-medium text-gray-700">Bobot (%)</label>
                            <input type="number" name="bobot_persen" id="bobot_persen" value="{{ old('bobot_persen', $sasaran->bobot_persen) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" max="100">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="submit" name="status" value="draft" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" name="status" value="diajukan" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Ajukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 