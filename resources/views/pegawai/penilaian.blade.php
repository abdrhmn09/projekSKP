
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Hasil Penilaian SKP</h1>

        @if($penilaian->count() > 0)
            <div class="space-y-6">
                @foreach($penilaian as $p)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $p->periode->nama_periode }}</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Hasil penilaian SKP periode {{ $p->periode->nama_periode }}</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nilai SKP</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($p->nilai_skp, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nilai Perilaku</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($p->nilai_perilaku, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nilai Akhir</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($p->nilai_akhir, 2) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="mt-1">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                        {{ $p->kategori_nilai == 'Sangat Baik' ? 'bg-green-100 text-green-800' : 
                                           ($p->kategori_nilai == 'Baik' ? 'bg-blue-100 text-blue-800' : 
                                           'bg-yellow-100 text-yellow-800') }}">
                                        {{ $p->kategori_nilai }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                Belum ada hasil penilaian yang tersedia.
            </div>
        @endif
    </div>
</div>
@endsection
