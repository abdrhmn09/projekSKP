@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Input Realisasi Kerja</h3>
            </div>
            
            <form action="{{ route('pegawai.realisasi.store') }}" method="POST" enctype="multipart/form-data" class="px-4 py-5 sm:p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="sasaran_kerja_id" class="block text-sm font-medium text-gray-700">Sasaran Kerja</label>
                        <select id="sasaran_kerja_id" name="sasaran_kerja_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih Sasaran Kerja</option>
                            @foreach($sasaranApproved as $sasaran)
                                <option value="{{ $sasaran->id }}">{{ $sasaran->uraian_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="uraian_realisasi" class="block text-sm font-medium text-gray-700">Uraian Realisasi</label>
                        <textarea id="uraian_realisasi" name="uraian_realisasi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>

                    <div>
                        <label for="realisasi_kuantitas" class="block text-sm font-medium text-gray-700">Realisasi Kuantitas</label>
                        <input type="number" name="realisasi_kuantitas" id="realisasi_kuantitas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="realisasi_kualitas" class="block text-sm font-medium text-gray-700">Realisasi Kualitas (%)</label>
                        <input type="number" name="realisasi_kualitas" id="realisasi_kualitas" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="realisasi_waktu" class="block text-sm font-medium text-gray-700">Realisasi Waktu</label>
                        <input type="date" name="realisasi_waktu" id="realisasi_waktu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="realisasi_biaya" class="block text-sm font-medium text-gray-700">Realisasi Biaya</label>
                        <input type="number" name="realisasi_biaya" id="realisasi_biaya" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="bukti_dukung" class="block text-sm font-medium text-gray-700">Keterangan Bukti Dukung</label>
                        <textarea id="bukti_dukung" name="bukti_dukung" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Uraikan bukti-bukti pendukung yang ada..."></textarea>
                    </div>

                    <div>
                        <label for="bukti_pendukung" class="block text-sm font-medium text-gray-700">
                            Upload Bukti Pendukung
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload file</span>
                                        <input id="file-upload" name="bukti_pendukung" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, DOC, DOCX hingga 10MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('pegawai.realisasi') }}" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Realisasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
