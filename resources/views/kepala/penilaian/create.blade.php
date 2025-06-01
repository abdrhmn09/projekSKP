@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('kepala.penilaian.index') }}" class="text-blue-600 hover:text-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Penilaian SKP</h1>

        <!-- Informasi Pegawai -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Pegawai</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->pegawai->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">NIP</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->pegawai->user->nip }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Periode</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->periode->nama_periode }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Detail Sasaran -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Sasaran Kerja</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kode Sasaran</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->kode_sasaran }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uraian Kegiatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->uraian_kegiatan }}</dd>
                    </div>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Kuantitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_kuantitas }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Kualitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_kualitas }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Waktu</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_waktu }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Realisasi -->
        @if($sasaran->realisasi)
            @php $realisasi = $sasaran->realisasi; @endphp
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Realisasi Kerja</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Realisasi Kuantitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realisasi->realisasi_kuantitas ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Realisasi Kualitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realisasi->realisasi_kualitas ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Realisasi Waktu</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realisasi->realisasi_waktu ?? '-' }}</dd>
                        </div>
                    </dl>
                    @if($realisasi->keterangan)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realisasi->keterangan }}</dd>
                        </div>
                    @endif
                    @if($realisasi->bukti_pendukung)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Bukti Pendukung</dt>
                            <dd class="mt-1">
                                <a href="{{ Storage::url('bukti_pendukung/' . $realisasi->bukti_pendukung) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Bukti Pendukung
                                </a>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Form Penilaian -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Form Penilaian</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Berikan penilaian untuk sasaran kerja pegawai</p>
            </div>
            <div class="border-t border-gray-200">
                <form action="{{ route('kepala.penilaian.store', $sasaran->id) }}" method="POST" class="px-4 py-5 sm:px-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="nilai_skp" class="block text-sm font-medium text-gray-700">
                                Nilai SKP (0-100)
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="nilai_skp" 
                                       id="nilai_skp" 
                                       min="0" 
                                       max="100" 
                                       step="0.01"
                                       value="{{ old('nilai_skp') }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            @error('nilai_skp')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nilai_perilaku" class="block text-sm font-medium text-gray-700">
                                Nilai Perilaku (0-100)
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="nilai_perilaku" 
                                       id="nilai_perilaku" 
                                       min="0" 
                                       max="100" 
                                       step="0.01"
                                       value="{{ old('nilai_perilaku') }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            @error('nilai_perilaku')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="catatan_penilaian" class="block text-sm font-medium text-gray-700">
                            Catatan Penilaian (Optional)
                        </label>
                        <div class="mt-1">
                            <textarea name="catatan_penilaian" 
                                      id="catatan_penilaian" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                      placeholder="Berikan catatan atau feedback untuk penilaian ini...">{{ old('catatan_penilaian') }}</textarea>
                        </div>
                        @error('catatan_penilaian')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 bg-gray-50 p-4 rounded-md">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Kategori Nilai:</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• 90-100: Sangat Baik</li>
                            <li>• 76-89: Baik</li>
                            <li>• 61-75: Cukup</li>
                            <li>• 51-60: Kurang</li>
                            <li>• 0-50: Sangat Kurang</li>
                        </ul>
                        <p class="text-xs text-gray-500 mt-2">
                            Nilai akhir = (Nilai SKP × 60%) + (Nilai Perilaku × 40%)
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('kepala.penilaian.index') }}" 
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center items-center bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection