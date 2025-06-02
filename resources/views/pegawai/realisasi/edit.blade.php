@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Realisasi Kerja</h1>

        <form action="{{ route('pegawai.realisasi.update', $realisasi->id) }}" method="POST" class="bg-white shadow-sm rounded-lg p-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sasaran Kerja</label>
                    <input type="text" value="{{ $realisasi->sasaranKerja->uraian_sasaran ?? 'Tidak Ditemukan' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                    <input type="hidden" name="sasaran_kerja_id" value="{{ $realisasi->sasaran_kerja_id }}">
                    @error('sasaran_kerja_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="uraian_realisasi" class="block text-sm font-medium text-gray-700">Uraian Realisasi</label>
                    <textarea id="uraian_realisasi" name="uraian_realisasi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('uraian_realisasi', $realisasi->uraian_realisasi) }}</textarea>
                    @error('uraian_realisasi')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="realisasi_kuantitas" class="block text-sm font-medium text-gray-700">Realisasi Kuantitas</label>
                        <input id="realisasi_kuantitas" type="number" name="realisasi_kuantitas" value="{{ old('realisasi_kuantitas', $realisasi->realisasi_kuantitas) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('realisasi_kuantitas')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="realisasi_kualitas" class="block text-sm font-medium text-gray-700">Realisasi Kualitas (%)</label>
                        <input id="realisasi_kualitas" type="number" name="realisasi_kualitas" min="0" max="100" step="0.01" value="{{ old('realisasi_kualitas', $realisasi->realisasi_kualitas) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('realisasi_kualitas')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="realisasi_waktu" class="block text-sm font-medium text-gray-700">Realisasi Waktu</label>
                        <input id="realisasi_waktu" type="date" name="realisasi_waktu" value="{{ old('realisasi_waktu', $realisasi->realisasi_waktu ? \Carbon\Carbon::parse($realisasi->realisasi_waktu)->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('realisasi_waktu')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="realisasi_biaya" class="block text-sm font-medium text-gray-700">Realisasi Biaya</label>
                        <input id="realisasi_biaya" type="number" name="realisasi_biaya" min="0" step="0.01" value="{{ old('realisasi_biaya', $realisasi->realisasi_biaya) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('realisasi_biaya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bukti Dukung Saat Ini</label>
                    @if($realisasi->bukti_dukung)
                        <div class="mt-1 mb-2 p-2 border border-gray-300 rounded-md bg-gray-50">
                            <a href="{{ route('pegawai.realisasi.bukti_dukung', basename($realisasi->bukti_dukung)) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                {{ basename($realisasi->bukti_dukung) }}
                            </a>
                        </div>
                    @else
                        <p class="mt-1 text-sm text-gray-500">Tidak ada bukti dukung yang diunggah.</p>
                    @endif
                    
                    <label for="bukti_dukung" class="block text-sm font-medium text-gray-700 mt-2">Ganti Bukti Dukung (Opsional)</label>
                    <input id="bukti_dukung" type="file" name="bukti_dukung" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file: 4MB. Format: JPG, PNG, PDF, DOC, DOCX. Kosongkan jika tidak ingin mengganti.</p>
                    @error('bukti_dukung')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pegawai.realisasi') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Update Realisasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 