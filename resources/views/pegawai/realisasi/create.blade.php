@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Input Realisasi Kerja</h1>

        <form action="{{ route('pegawai.realisasi.store') }}" method="POST" class="bg-white shadow-sm rounded-lg p-6" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sasaran Kerja</label>
                    <select name="sasaran_kerja_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih Sasaran Kerja</option>
                        @foreach($sasaranApproved as $sasaran)
                            <option value="{{ $sasaran->id }}">{{ $sasaran->uraian_sasaran }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Uraian Realisasi</label>
                    <textarea name="uraian_realisasi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Realisasi Kuantitas</label>
                        <input type="number" name="realisasi_kuantitas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Realisasi Kualitas (%)</label>
                        <input type="number" name="realisasi_kualitas" min="0" max="100" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Realisasi Waktu</label>
                        <input type="date" name="realisasi_waktu" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Realisasi Biaya</label>
                        <input type="number" name="realisasi_biaya" min="0" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bukti Dukung</label>
                    <input type="file" name="bukti_dukung" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran file: 4MB. Format: JPG, PNG, PDF, DOC, DOCX.</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pegawai.realisasi') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
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
