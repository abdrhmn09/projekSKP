@extends('layouts.app')

@section('title', 'Buat Sasaran Kerja')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Buat Sasaran Kerja</h1>
                <p class="mt-1 text-sm text-gray-600">Tentukan target kerja Anda untuk periode ini.</p>
            </div>

            <form method="POST" action="{{ route('pegawai.sasaran.store') }}" class="space-y-6">
                @csrf

                <!-- Periode Penilaian (Info) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode Penilaian Aktif</label>
                    <div class="mt-1 p-2 border border-gray-300 rounded-md bg-gray-50">
                        @if($periodeAktif)
                            <p class="text-sm text-gray-900"><strong>{{ $periodeAktif->nama_periode }}</strong> ({{ $periodeAktif->tanggal_mulai->format('d M Y') }} - {{ $periodeAktif->tanggal_selesai->format('d M Y') }})</p>
                            {{-- Input hidden tidak lagi diperlukan karena controller mengambilnya langsung --}}
                    @else
                            <p class="text-sm text-red-600">Tidak ada periode penilaian yang aktif. Tidak dapat membuat sasaran kerja.</p>
                        @endif
                        </div>
                </div>

                @if($periodeAktif) {{-- Hanya tampilkan sisa form jika ada periode aktif --}}
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
                            <input type="text" name="kode_sasaran" id="kode_sasaran" value="{{ old('kode_sasaran') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ketik IKU manual di sini">
                    @error('kode_sasaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                        </div>
                         {{-- Hidden input to ensure kode_sasaran is always submitted if selector has a value but wrapper is hidden. Replaced by JS logic setting the actual input value. --}}
                         {{-- The main input "kode_sasaran" is now always in the DOM, just hidden/shown. Its value is what matters. --}}
                         @if(!old('kode_sasaran') && !$errors->has('kode_sasaran')) {{-- Error for main field if nothing is selected/entered --}}
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
                              placeholder="Jelaskan secara detail kegiatan yang akan dilakukan">{{ old('uraian_kegiatan') }}</textarea>
                    @error('uraian_kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="target_kuantitas" class="block text-sm font-medium text-gray-700">Target Kuantitas</label>
                            <input type="number" name="target_kuantitas" id="target_kuantitas" value="{{ old('target_kuantitas') }}" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Jumlah" min="0">
                        @error('target_kuantitas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        <div>
                            <label for="satuan_kuantitas" class="block text-sm font-medium text-gray-700">Satuan Kuantitas</label>
                            <input type="text" name="satuan_kuantitas" id="satuan_kuantitas" value="{{ old('satuan_kuantitas') }}" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Contoh: Dokumen, Kegiatan, Laporan">
                            @error('satuan_kuantitas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    <div>
                            <label for="target_kualitas" class="block text-sm font-medium text-gray-700">Target Kualitas (%)</label>
                            <input type="number" name="target_kualitas" id="target_kualitas" value="{{ old('target_kualitas') }}" required
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
                                      value="{{ old('target_waktu') }}">
                        @error('target_waktu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        <div>
                            <label for="target_biaya" class="block text-sm font-medium text-gray-700">Target Biaya (Rp)</label>
                            <input type="number" name="target_biaya" id="target_biaya" value="{{ old('target_biaya') }}"
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
                    <input type="number" name="bobot_persen" id="bobot_persen" value="{{ old('bobot_persen') }}" 
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
                            Simpan Draft
                        </button>
                        <button type="submit" name="status" value="submitted"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm">
                            Ajukan Persetujuan
                        </button>
                </div>
                @endif {{-- End @if($periodeAktif) --}}
            </form>
        </div>
    </div>
</div>

{{-- Script auto-generate kode sasaran tidak lagi relevan jika kode sasaran diinput manual --}}
{{-- Jika masih ingin auto-generate berdasarkan input lain (selain periode_id), script perlu disesuaikan --}}
{{-- Untuk saat ini saya hapus scriptnya karena periode_id select sudah tidak ada --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
        const ikuSelector = document.getElementById('kode_sasaran_selector');
        const kodeSasaranInput = document.getElementById('kode_sasaran');
        const kodeSasaranInputWrapper = document.getElementById('kode_sasaran_input_wrapper');

        // Helper function to check if a value is one of the predefined IKU options (excluding 'lainnya' and '')
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
                // If the input field currently holds a predefined IKU value, clear it.
                // Otherwise, retain its current value (e.g., from old() or previous manual input).
                if (isPredefinedIKU(kodeSasaranInput.value)) {
                    kodeSasaranInput.value = '';
                }
                // Requirement for kode_sasaran input is handled by its 'required' attribute if visible.
                kodeSasaranInput.setAttribute('required', 'required');
            } else { // A predefined IKU or the placeholder "-- Pilih dari daftar --" is selected
                kodeSasaranInputWrapper.classList.add('hidden');
                if (selectedValueInDropdown) { // A predefined IKU is selected
                    kodeSasaranInput.value = selectedValueInDropdown;
                    kodeSasaranInput.removeAttribute('required'); // Not required if hidden & auto-filled
                } else { // Placeholder "-- Pilih dari daftar --" is selected
                    kodeSasaranInput.value = '';
                     // If placeholder, the main input must be conceptually required, so ensure validation triggers.
                     // Server-side validation for kode_sasaran will still apply.
                     // For client-side, if nothing is chosen, 'kode_sasaran' will be empty.
                    kodeSasaranInput.setAttribute('required', 'required'); 
                }
            }
        }

        // --- Initial Setup ---
        // `kodeSasaranInput.value` is pre-filled by Blade's `old('kode_sasaran')`
        const initialInputValue = kodeSasaranInput.value;

        if (initialInputValue) {
            let foundInSelectorOptions = false;
            for (let i = 0; i < ikuSelector.options.length; i++) {
                if (ikuSelector.options[i].value === initialInputValue) {
                    ikuSelector.value = initialInputValue; // Sync dropdown to the old input value
                    foundInSelectorOptions = true;
                    break;
                }
            }
            if (!foundInSelectorOptions) { // The old input value was a custom one
                ikuSelector.value = 'lainnya'; // Set dropdown to 'Lainnya'
            }
        } else {
            // No old input value, ensure dropdown is at placeholder
            ikuSelector.value = ''; 
        }
        // Apply initial visibility and input state based on the (now synced) dropdown
        updateFormStateBasedOnSelector(); 
        // --- End Initial Setup ---


        // Event listener for dropdown changes
        ikuSelector.addEventListener('change', function() {
            updateFormStateBasedOnSelector();
            if (this.value === 'lainnya') {
                kodeSasaranInput.focus(); // Focus the manual input field when 'Lainnya' is chosen
            }
        });

        // Event listener for manual input changes (when the text field is visible)
        kodeSasaranInput.addEventListener('input', function() {
            const currentTypedValue = this.value;
            // If user types something that exactly matches a predefined IKU in the dropdown
            if (isPredefinedIKU(currentTypedValue)) {
                ikuSelector.value = currentTypedValue; // Change dropdown to that IKU
                updateFormStateBasedOnSelector(); // This will hide the input field and update its value
            } else if (!currentTypedValue && ikuSelector.value !== 'lainnya') {
                 // If user clears the input field while it was (incorrectly) visible and selector was not 'lainnya'
                 // (e.g. they typed an IKU, then deleted it all)
                 // ikuSelector.value = ''; // Revert to placeholder
                 // updateFormStateBasedOnSelector(); // This will correctly set input to required.
                 // This scenario is less likely with the current logic, as input is hidden when not 'lainnya'.
            }
            // If they type something custom, the selector should already be 'lainnya'.
            // If it's not, and they type custom, it means something odd happened.
            // For safety, if input is active and selector is not 'lainnya', but value is custom:
            else if (currentTypedValue && !isPredefinedIKU(currentTypedValue) && ikuSelector.value !== 'lainnya') {
                ikuSelector.value = 'lainnya'; // force selector to 'lainnya'
                // updateFormStateBasedOnSelector(); // no need to call, wrapper is already visible
        }
    });

});
</script>
@endpush
