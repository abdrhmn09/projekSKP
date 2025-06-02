@extends('layouts.app')

@section('title', 'Form Penilaian SKP')

@push('styles')
<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .input-group {
        margin-bottom: 1rem;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
    }
    .input-group label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
    }
    .input-group .info-text {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Formulir Penilaian SKP</h1>
            <p class="text-sm text-gray-500">Pegawai: <span class="font-medium">{{ $sasaranKerja->pegawai->user->name }}</span></p>
            <p class="text-sm text-gray-500">Periode: <span class="font-medium">{{ $sasaranKerja->periode->nama }} ({{ $sasaranKerja->periode->tanggal_mulai_formatted }} - {{ $sasaranKerja->periode->tanggal_selesai_formatted }})</span></p>
            <p class="text-sm text-gray-500">Sasaran Kerja: <span class="font-medium">{{ $sasaranKerja->uraian_sasaran }}</span></p>
            <p class="text-sm text-gray-500">Indikator Kinerja Utama: <span class="font-medium">{{ $sasaranKerja->indikator_kinerja }}</span></p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops! Ada kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kepala.penilaian-skp.store', $sasaranKerja->id) }}" method="POST">
            @csrf

            <!-- Navigasi Tab -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="penilaian-skp-tab" data-tabs-target="#penilaian-skp" type="button" role="tab" aria-controls="penilaian-skp" aria-selected="true">Penilaian SKP</button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="penilaian-perilaku-tab" data-tabs-target="#penilaian-perilaku" type="button" role="tab" aria-controls="penilaian-perilaku" aria-selected="false">Penilaian Perilaku Kerja</button>
                    </li>
                </ul>
            </div>

            <!-- Konten Tab -->
            <div id="tabContent">
                <!-- Tab Penilaian SKP -->
                <div class="tab-content active" id="penilaian-skp" role="tabpanel" aria-labelledby="penilaian-skp-tab">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Detail Target dan Realisasi</h2>

                    @php
                        $realisasi = $sasaranKerja->realisasiKerja; // HasOne relation, so it's a single object or null
                        $penilaianData = optional($sasaranKerja->penilaianSkp)->detail_penilaian; // JSON field from PenilaianSkp
                    @endphp

                    @if($realisasi)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Penilaian Aspek Kuantitas -->
                            <div class="input-group">
                                <label for="nilai_kuantitas_realisasi">Aspek: Kuantitas</label>
                                <p class="info-text">Target: {{ $sasaranKerja->target_kuantitas ?? '-' }} {{ $sasaranKerja->satuan_kuantitas ?? '' }}</p>
                                <p class="info-text">Realisasi Pegawai: {{ $realisasi->realisasi_kuantitas ?? '-' }} {{ $sasaranKerja->satuan_kuantitas ?? '' }}</p>
                                
                                <label for="nilai_kuantitas_ekspektasi" class="mt-2">Ekspektasi Pimpinan:</label>
                                <textarea name="nilai[kuantitas][ekspektasi]" id="nilai_kuantitas_ekspektasi" rows="2" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('nilai.kuantitas.ekspektasi', data_get($penilaianData, 'kuantitas.ekspektasi', '')) }}</textarea>
                                
                                <label for="nilai_kuantitas_realisasi_dinilai" class="mt-2">Nilai Realisasi Dinilai (%):</label>
                                <input type="number" name="nilai[kuantitas][realisasi_dinilai]" id="nilai_kuantitas_realisasi_dinilai" min="0" max="100" class="form-input mt-1 block w-full" value="{{ old('nilai.kuantitas.realisasi_dinilai', data_get($penilaianData, 'kuantitas.realisasi_dinilai', '')) }}" @if($isFinal) disabled @endif>
                            </div>

                            <!-- Penilaian Aspek Kualitas -->
                            <div class="input-group">
                                <label for="nilai_kualitas_realisasi">Aspek: Kualitas</label>
                                <p class="info-text">Target: {{ $sasaranKerja->target_kualitas ?? '-' }} %</p>
                                <p class="info-text">Realisasi Pegawai: {{ $realisasi->realisasi_kualitas ?? '-' }} %</p>
                                
                                <label for="nilai_kualitas_ekspektasi" class="mt-2">Ekspektasi Pimpinan:</label>
                                <textarea name="nilai[kualitas][ekspektasi]" id="nilai_kualitas_ekspektasi" rows="2" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('nilai.kualitas.ekspektasi', data_get($penilaianData, 'kualitas.ekspektasi', '')) }}</textarea>
                                
                                <label for="nilai_kualitas_realisasi_dinilai" class="mt-2">Nilai Realisasi Dinilai (%):</label>
                                <input type="number" name="nilai[kualitas][realisasi_dinilai]" id="nilai_kualitas_realisasi_dinilai" min="0" max="100" class="form-input mt-1 block w-full" value="{{ old('nilai.kualitas.realisasi_dinilai', data_get($penilaianData, 'kualitas.realisasi_dinilai', '')) }}" @if($isFinal) disabled @endif>
                            </div>

                            <!-- Penilaian Aspek Waktu -->
                            @if($sasaranKerja->target_waktu)
                            <div class="input-group">
                                <label for="nilai_waktu_realisasi">Aspek: Waktu</label>
                                <p class="info-text">Target: {{ \Carbon\Carbon::parse($sasaranKerja->target_waktu)->translatedFormat('d M Y') ?? '-' }}</p>
                                <p class="info-text">Realisasi Pegawai: {{ $realisasi->realisasi_waktu ? \Carbon\Carbon::parse($realisasi->realisasi_waktu)->translatedFormat('d M Y') : '-' }}</p>
                                
                                <label for="nilai_waktu_ekspektasi" class="mt-2">Ekspektasi Pimpinan:</label>
                                <textarea name="nilai[waktu][ekspektasi]" id="nilai_waktu_ekspektasi" rows="2" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('nilai.waktu.ekspektasi', data_get($penilaianData, 'waktu.ekspektasi', '')) }}</textarea>
                                
                                <label for="nilai_waktu_realisasi_dinilai" class="mt-2">Nilai Realisasi Dinilai (%):</label>
                                <input type="number" name="nilai[waktu][realisasi_dinilai]" id="nilai_waktu_realisasi_dinilai" min="0" max="100" class="form-input mt-1 block w-full" value="{{ old('nilai.waktu.realisasi_dinilai', data_get($penilaianData, 'waktu.realisasi_dinilai', '')) }}" @if($isFinal) disabled @endif>
                            </div>
                            @endif

                            <!-- Penilaian Aspek Biaya (jika ada) -->
                            @if($sasaranKerja->target_biaya > 0 || ($realisasi && $realisasi->realisasi_biaya > 0) )
                            <div class="input-group">
                                <label for="nilai_biaya_realisasi">Aspek: Biaya</label>
                                <p class="info-text">Target: Rp {{ number_format($sasaranKerja->target_biaya ?? 0, 0, ',', '.') }}</p>
                                <p class="info-text">Realisasi Pegawai: Rp {{ number_format($realisasi->realisasi_biaya ?? 0, 0, ',', '.') }}</p>
                                
                                <label for="nilai_biaya_ekspektasi" class="mt-2">Ekspektasi Pimpinan:</label>
                                <textarea name="nilai[biaya][ekspektasi]" id="nilai_biaya_ekspektasi" rows="2" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('nilai.biaya.ekspektasi', data_get($penilaianData, 'biaya.ekspektasi', '')) }}</textarea>
                                
                                <label for="nilai_biaya_realisasi_dinilai" class="mt-2">Nilai Realisasi Dinilai (%):</label>
                                <input type="number" name="nilai[biaya][realisasi_dinilai]" id="nilai_biaya_realisasi_dinilai" min="0" max="100" class="form-input mt-1 block w-full" value="{{ old('nilai.biaya.realisasi_dinilai', data_get($penilaianData, 'biaya.realisasi_dinilai', '')) }}" @if($isFinal) disabled @endif>
                            </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="bukti_dukung_display" class="block text-sm font-medium text-gray-700">Bukti Dukung dari Pegawai</label>
                            @if ($realisasi->bukti_dukung)
                                <a href="{{ route('kepala.penilaian-skp.bukti_dukung', basename($realisasi->bukti_dukung)) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Lihat Bukti Dukung</a>
                            @else
                                <p class="text-gray-500">Tidak ada bukti dukung yang diunggah.</p>
                            @endif
                        </div>
                        
                    @else
                        <p class="text-gray-500 py-4">Belum ada data realisasi kerja yang diinput oleh pegawai untuk sasaran kerja ini.</p>
                    @endif

                    <div class="mb-4">
                        <label for="catatan_kepala_sekolah" class="block text-sm font-medium text-gray-700">Catatan Penilaian Keseluruhan SKP</label>
                        <textarea id="catatan_kepala_sekolah" name="catatan_kepala_sekolah" rows="3" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('catatan_kepala_sekolah', optional($sasaranKerja->penilaianSkp)->catatan_kepala_sekolah ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Tab Penilaian Perilaku Kerja -->
                <div class="tab-content" id="penilaian-perilaku" role="tabpanel" aria-labelledby="penilaian-perilaku-tab">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Penilaian Perilaku Kerja</h2>
                    @php
                        $aspekPerilaku = [
                            'Berorientasi Pelayanan', 
                            'Akuntabel', 
                            'Kompeten', 
                            'Harmonis', 
                            'Loyal', 
                            'Adaptif', 
                            'Kolaboratif'
                        ];
                        $penilaianPerilakuData = optional($sasaranKerja->penilaianSkp)->penilaianPerilaku ? 
                                                $sasaranKerja->penilaianSkp->penilaianPerilaku->keyBy('aspek_perilaku') : collect();
                    @endphp

                    @foreach($aspekPerilaku as $index => $aspek)
                    <div class="mb-4 p-4 border rounded-md">
                        <label class="block text-sm font-medium text-gray-700">Aspek: {{ $aspek }}</label>
                        <input type="hidden" name="penilaian_perilaku[{{ $index }}][aspek]" value="{{ $aspek }}" @if($isFinal) disabled @endif>
                        <select name="penilaian_perilaku[{{ $index }}][skor]"
                                class="form-select mt-1 block w-full" @if($isFinal) disabled @endif>
                            <option value="">Pilih Skor</option>
                            @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('penilaian_perilaku.'.$index.'.skor', optional($penilaianPerilakuData->get($aspek))->skor ?? '' ) == $i ? 'selected' : '' }}>
                                {{ $i }} - 
                                @switch($i)
                                    @case(1) Sangat Kurang @break
                                    @case(2) Kurang @break
                                    @case(3) Cukup @break
                                    @case(4) Baik @break
                                    @case(5) Sangat Baik @break
                                @endswitch
                            </option>
                            @endfor
                        </select>
                    </div>
                    @endforeach
                    
                    <div class="mb-4">
                        <label for="feedback_perilaku" class="block text-sm font-medium text-gray-700">Umpan Balik Tambahan Perilaku Kerja</label>
                        <textarea id="feedback_perilaku" name="feedback_perilaku" rows="3" class="form-textarea mt-1 block w-full" @if($isFinal) disabled @endif>{{ old('feedback_perilaku', optional($sasaranKerja->penilaianSkp)->feedback_perilaku ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="status_penilaian" class="block text-sm font-medium text-gray-700">Status Penilaian</label>
                <select id="status_penilaian" name="status_penilaian" class="form-select mt-1 block w-full" @if($isFinal) disabled @endif>
                    <option value="draft" {{ old('status_penilaian', optional($sasaranKerja->penilaianSkp)->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                    <option value="final" {{ old('status_penilaian', optional($sasaranKerja->penilaianSkp)->status ?? '') == 'final' ? 'selected' : '' }}>Simpan sebagai Final</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('kepala.penilaian-skp.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    @if($isFinal) Kembali @else Batal @endif
                </a>
                @if(!$isFinal)
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Penilaian
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('.tab-content');

        let activeTab = document.querySelector('[aria-selected="true"]');
        if (activeTab) {
            const target = activeTab.dataset.tabsTarget;
            document.querySelector(target).classList.add('active');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
            activeTab.classList.remove('hover:text-gray-600', 'hover:border-gray-300');
        } else if (tabs.length > 0) {
            tabs[0].setAttribute('aria-selected', 'true');
            tabs[0].classList.add('border-indigo-500', 'text-indigo-600');
            tabs[0].classList.remove('hover:text-gray-600', 'hover:border-gray-300');
            const defaultTarget = tabs[0].dataset.tabsTarget;
            document.querySelector(defaultTarget).classList.add('active');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    t.classList.remove('border-indigo-500', 'text-indigo-600');
                    t.classList.add('hover:text-gray-600', 'hover:border-gray-300', 'border-transparent');
                });
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });

                this.setAttribute('aria-selected', 'true');
                this.classList.add('border-indigo-500', 'text-indigo-600');
                this.classList.remove('hover:text-gray-600', 'hover:border-gray-300', 'border-transparent');
                const target = this.dataset.tabsTarget;
                document.querySelector(target).classList.add('active');
            });
        });
    });
</script>
@endpush 