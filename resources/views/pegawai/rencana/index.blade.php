
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Rencana Tindak Lanjut</h1>
            <button onclick="showCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Buat Rencana
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($rencana->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="space-y-4 p-6">
                    @foreach($rencana as $r)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Periode: {{ $r->penilaianSkp->periode->nama_periode ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-500">Dibuat: {{ $r->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                {{ $r->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($r->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Rencana Perbaikan</h4>
                                <p class="text-sm text-gray-700">{{ $r->rencana_perbaikan }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Strategi Pencapaian</h4>
                                <p class="text-sm text-gray-700">{{ $r->strategi_pencapaian }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Target Penyelesaian</h4>
                                <p class="text-sm text-gray-700">{{ $r->target_penyelesaian->format('d M Y') }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Indikator Keberhasilan</h4>
                                <p class="text-sm text-gray-700">{{ $r->indikator_keberhasilan }}</p>
                            </div>
                        </div>

                        @if($r->catatan_progress)
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Catatan Progress</h4>
                            <p class="text-sm text-gray-700">{{ $r->catatan_progress }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                {{ $rencana->links() }}
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                Belum ada rencana tindak lanjut yang dibuat.
            </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Buat Rencana Tindak Lanjut</h3>
                <button onclick="hideCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('pegawai.rencana.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih Penilaian SKP</label>
                        <select name="penilaian_skp_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Penilaian</option>
                            @php
                                $pegawai = auth()->user()->pegawai;
                                $penilaianList = \App\Models\PenilaianSkp::where('pegawai_id', $pegawai->id ?? 0)
                                    ->with('periode')
                                    ->whereDoesntHave('rencanaTindakLanjut')
                                    ->get();
                            @endphp
                            @foreach($penilaianList as $penilaian)
                            <option value="{{ $penilaian->id }}">{{ $penilaian->periode->nama_periode ?? 'N/A' }} - Nilai: {{ $penilaian->nilai_akhir }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rencana Perbaikan</label>
                        <textarea name="rencana_perbaikan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Strategi Pencapaian</label>
                        <textarea name="strategi_pencapaian" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Penyelesaian</label>
                        <input type="date" name="target_penyelesaian" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Indikator Keberhasilan</label>
                        <textarea name="indikator_keberhasilan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="hideCreateModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function hideCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}
</script>
@endsection
