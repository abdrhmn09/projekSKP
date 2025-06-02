@extends('layouts.app')

@section('title', 'Edit Sasaran Kerja')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Sasaran Kerja</h1>
                <p class="mt-1 text-sm text-gray-600">Perbarui target kerja Anda untuk periode ini.</p>
            </div>

            <form method="POST" action="{{ route('pegawai.sasaran.update', $sasaran->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Periode Penilaian (Info) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode Penilaian</label>
                    <div class="mt-1 p-2 border border-gray-300 rounded-md bg-gray-50">
                        @if($periodeAktif) {{-- Di controller, $periodeAktif diambil dari $sasaran->periode --}}
                            <p class="text-sm text-gray-900"><strong>{{ $periodeAktif->nama_periode }}</strong> ({{ $periodeAktif->tanggal_mulai->format('d M Y') }} - {{ $periodeAktif->tanggal_selesai->format('d M Y') }})</p>
                        @else
                            <p class="text-sm text-red-600">Periode untuk sasaran kerja ini tidak ditemukan.</p>
                        @endif
                    </div>
                </div>

                @if($periodeAktif) 
                    <!-- Kode Sasaran (Indikator Kinerja) -->
                    <div>
                        <label for="kode_sasaran_selector" class="block text-sm font-medium text-gray-700">Indikator Kinerja Utama (IKU) / Kode Sasaran</label>
                        
                        <select id="kode_sasaran_selector" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-2">
                            <option value="">-- Pilih dari daftar --</option>
                            <option value="IKU-001: Peningkatan mutu layanan akademik">IKU-001: Peningkatan mutu layanan akademik</option>
                            <option value="IKU-002: Pengembangan jumlah publikasi ilmiah">IKU-002: Pengembangan jumlah publikasi ilmiah</option>
                            <option value="IKU-003: Optimalisasi serapan anggaran penelitian">IKU-003: Optimalisasi serapan anggaran penelitian</option>
                            {{-- Tambahkan opsi IKU lainnya di sini jika perlu --}}
                            <option value="lainnya">Lainnya (Isi manual di bawah)</option>
                        </select>

                        <div id="kode_sasaran_input_wrapper" class="hidden">
                            <input type="text" name="kode_sasaran" id="kode_sasaran" value="{{ old('kode_sasaran', $sasaran->indikator_kinerja) }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ketik IKU manual di sini">
                            @error('kode_sasaran')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         @if(!old('kode_sasaran', $sasaran->indikator_kinerja) && !$errors->has('kode_sasaran')) 
                            @error('kode_sasaran_selector_dummy_for_validation_trigger') 
                                <p class="mt-1 text-sm text-red-600">Indikator Kinerja Utama harus diisi.</p>
                            @enderror
                         @endif
                    </div>

                    <!-- Uraian Kegiatan (Uraian Sasaran) -->
                    <div>
                        <label for="uraian_kegiatan" class="block text-sm font-medium text-gray-700">Uraian Sasaran Kinerja / Kegiatan</label>
                        <textarea name="uraian_kegiatan" id="uraian_kegiatan" rows="4" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Jelaskan secara detail kegiatan yang akan dilakukan">{{ old('uraian_kegiatan', $sasaran->uraian_sasaran) }}</textarea>
                        @error('uraian_kegiatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="target_kuantitas" class="block text-sm font-medium text-gray-700">Target Kuantitas</label>
                            <input type="number" name="target_kuantitas" id="target_kuantitas" value="{{ old('target_kuantitas', $sasaran->target_kuantitas) }}" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Jumlah" min="0">
                             @error('target_kuantitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="satuan_kuantitas" class="block text-sm font-medium text-gray-700">Satuan Kuantitas</label>
                            <input type="text" name="satuan_kuantitas" id="satuan_kuantitas" value="{{ old('satuan_kuantitas', $sasaran->satuan_kuantitas) }}" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Contoh: Dokumen, Kegiatan, Laporan">
                            @error('satuan_kuantitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="target_kualitas" class="block text-sm font-medium text-gray-700">Target Kualitas (%)</label>
                            <input type="number" name="target_kualitas" id="target_kualitas" value="{{ old('target_kualitas', $sasaran->target_kualitas) }}" required
                                   min="0" max="100" step="1"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0-100">
                            @error('target_kualitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="target_waktu" class="block text-sm font-medium text-gray-700">Target Waktu (Deadline)</label>
                            <input type="date" name="target_waktu" id="target_waktu" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      value="{{ old('target_waktu', $sasaran->target_waktu->format('Y-m-d')) }}">
                            @error('target_waktu')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="target_biaya" class="block text-sm font-medium text-gray-700">Target Biaya (Rp)</label>
                            <input type="number" name="target_biaya" id="target_biaya" value="{{ old('target_biaya', $sasaran->target_biaya) }}"
                                   min="0" step="1000"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: 500000 (jika ada)">
                            @error('target_biaya')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bobot -->
                    <div>
                        <label for="bobot_persen" class="block text-sm font-medium text-gray-700">Bobot (%)</label>
                        <input type="number" name="bobot_persen" id="bobot_persen" value="{{ old('bobot_persen', $sasaran->bobot_persen) }}" 
                               min="0.01" max="100" step="0.01" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: 20">
                        <p class="mt-1 text-sm text-gray-500">Total bobot seluruh sasaran kerja pada satu periode harus mendekati 100%.</p>
                        @error('bobot_persen')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('pegawai.sasaran') }}" 
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-md shadow-sm">
                            Batal
                        </a>
                        <button type="submit" name="status" value="draft"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-md shadow-sm">
                            Simpan Perubahan Draft
                        </button>
                        <button type="submit" name="status" value="submitted"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm">
                            Update & Ajukan Persetujuan
                        </button>
                    </div>
                @endif 
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ikuSelector = document.getElementById('kode_sasaran_selector');
        const kodeSasaranInput = document.getElementById('kode_sasaran');
        const kodeSasaranInputWrapper = document.getElementById('kode_sasaran_input_wrapper');

        function isPredefinedIKU(value) {
            for (let i = 0; i < ikuSelector.options.length; i++) {
                const optionValue = ikuSelector.options[i].value;
                if (optionValue === value && optionValue !== '' && optionValue !== 'lainnya') {
                    return true;
                }
            }
            return false;
        }

        function updateFormStateBasedOnSelector() {
            const selectedValueInDropdown = ikuSelector.value;

            if (selectedValueInDropdown === 'lainnya') {
                kodeSasaranInputWrapper.classList.remove('hidden');
                if (isPredefinedIKU(kodeSasaranInput.value)) {
                    kodeSasaranInput.value = '';
                }
                kodeSasaranInput.setAttribute('required', 'required');
            } else { 
                kodeSasaranInputWrapper.classList.add('hidden');
                if (selectedValueInDropdown) { 
                    kodeSasaranInput.value = selectedValueInDropdown;
                    kodeSasaranInput.removeAttribute('required'); 
                } else { 
                    kodeSasaranInput.value = '';
                    kodeSasaranInput.setAttribute('required', 'required'); 
                }
            }
        }

        const initialInputValue = kodeSasaranInput.value; // Already set by Blade old() or $sasaran->...

        if (initialInputValue) {
            let foundInSelectorOptions = false;
            for (let i = 0; i < ikuSelector.options.length; i++) {
                if (ikuSelector.options[i].value === initialInputValue) {
                    ikuSelector.value = initialInputValue; 
                    foundInSelectorOptions = true;
                    break;
                }
            }
            if (!foundInSelectorOptions) { 
                ikuSelector.value = 'lainnya'; 
            }
        } else {
            ikuSelector.value = ''; 
        }
        updateFormStateBasedOnSelector(); 

        ikuSelector.addEventListener('change', function() {
            updateFormStateBasedOnSelector();
            if (this.value === 'lainnya') {
                kodeSasaranInput.focus(); 
            }
        });

        kodeSasaranInput.addEventListener('input', function() {
            const currentTypedValue = this.value;
            if (isPredefinedIKU(currentTypedValue)) {
                ikuSelector.value = currentTypedValue; 
                updateFormStateBasedOnSelector(); 
            } else if (!currentTypedValue && ikuSelector.value !== 'lainnya') {
                // Optional: Revert to placeholder if input cleared and not 'lainnya'
                // ikuSelector.value = ''; 
                // updateFormStateBasedOnSelector(); 
            }
            else if (currentTypedValue && !isPredefinedIKU(currentTypedValue) && ikuSelector.value !== 'lainnya') {
                ikuSelector.value = 'lainnya'; 
            }
        });
    });
</script>
@endpush 