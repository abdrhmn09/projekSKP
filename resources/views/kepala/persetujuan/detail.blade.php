
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('kepala.persetujuan') }}" class="text-blue-600 hover:text-blue-500">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Detail Sasaran Kerja</h1>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
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

        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Sasaran Kerja</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uraian Sasaran</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->uraian_kegiatan }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Indikator Kinerja</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->indikator_kinerja }}</dd>
                    </div>
                    <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Kuantitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_kuantitas }} {{ $sasaran->satuan_kuantitas }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Kualitas</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_kualitas }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Waktu</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($sasaran->target_waktu)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Target Biaya</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sasaran->target_biaya ? 'Rp ' . number_format($sasaran->target_biaya, 0, ',', '.') : '-' }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <form action="{{ route('kepala.persetujuan.reject', $sasaran->id) }}" method="POST" class="inline">
                @csrf
                <textarea name="catatan" placeholder="Catatan penolakan..." class="mr-3 px-3 py-2 border border-gray-300 rounded-md" required></textarea>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Tolak
                </button>
            </form>
            
            <form action="{{ route('kepala.persetujuan.approve', $sasaran->id) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="catatan" value="Disetujui">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Setujui
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
