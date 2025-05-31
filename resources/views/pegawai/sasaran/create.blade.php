
@extends('layouts.app')

@section('title', 'Buat Sasaran Kerja')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Buat Sasaran Kerja</h1>
                <p class="mt-1 text-sm text-gray-600">Tentukan target kerja Anda untuk periode ini</p>
            </div>

            <form method="POST" action="{{ route('pegawai.sasaran.store') }}" class="space-y-6">
                @csrf
                
                <!-- Periode Penilaian -->
                <div>
                    <label for="periode_id" class="block text-sm font-medium text-gray-700">Periode Penilaian</label>
                    <select name="periode_id" id="periode_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Periode</option>
                        @if(isset($periodes))
                            @foreach($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ old('periode_id') == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('periode_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Sasaran -->
                <div>
                    <label for="kode_sasaran" class="block text-sm font-medium text-gray-700">Kode Sasaran</label>
                    <input type="text" name="kode_sasaran" id="kode_sasaran" value="{{ old('kode_sasaran') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('kode_sasaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Uraian Kegiatan -->
                <div>
                    <label for="uraian_kegiatan" class="block text-sm font-medium text-gray-700">Uraian Kegiatan</label>
                    <textarea name="uraian_kegiatan" id="uraian_kegiatan" rows="4" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Jelaskan secara detail kegiatan yang akan dilakukan">{{ old('uraian_kegiatan') }}</textarea>
                    @error('uraian_kegiatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="target_kuantitas" class="block text-sm font-medium text-gray-700">Target Kuantitas</label>
                        <textarea name="target_kuantitas" id="target_kuantitas" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Contoh: 100 siswa, 20 kelas">{{ old('target_kuantitas') }}</textarea>
                        @error('target_kuantitas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="target_kualitas" class="block text-sm font-medium text-gray-700">Target Kualitas</label>
                        <textarea name="target_kualitas" id="target_kualitas" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Contoh: 90% siswa lulus, minimal nilai 80">{{ old('target_kualitas') }}</textarea>
                        @error('target_kualitas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="target_waktu" class="block text-sm font-medium text-gray-700">Target Waktu</label>
                        <textarea name="target_waktu" id="target_waktu" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Contoh: Selesai dalam 6 bulan">{{ old('target_waktu') }}</textarea>
                        @error('target_waktu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bobot -->
                <div>
                    <label for="bobot_persen" class="block text-sm font-medium text-gray-700">Bobot (%)</label>
                    <input type="number" name="bobot_persen" id="bobot_persen" value="{{ old('bobot_persen') }}" 
                           min="1" max="100" step="0.01" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Total bobot seluruh sasaran kerja harus 100%</p>
                    @error('bobot_persen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('pegawai.sasaran') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Batal
                    </a>
                    <button type="submit" name="status" value="draft"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        Simpan Draft
                    </button>
                    <button type="submit" name="status" value="diajukan"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Ajukan Persetujuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto generate kode sasaran
document.addEventListener('DOMContentLoaded', function() {
    const periodeSelect = document.getElementById('periode_id');
    const kodeInput = document.getElementById('kode_sasaran');
    
    periodeSelect.addEventListener('change', function() {
        if (this.value) {
            // Generate kode sasaran otomatis
            const now = new Date();
            const timestamp = now.getFullYear().toString().substr(-2) + 
                            (now.getMonth() + 1).toString().padStart(2, '0') + 
                            now.getDate().toString().padStart(2, '0') + 
                            now.getHours().toString().padStart(2, '0') + 
                            now.getMinutes().toString().padStart(2, '0');
            kodeInput.value = 'SK-' + timestamp;
        }
    });
});
</script>
@endsection
