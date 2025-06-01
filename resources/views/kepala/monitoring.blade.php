@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Monitoring Sasaran Kerja</h2>

        @if(session('warning'))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tasks text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Sasaran</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $statistics['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $statistics['selesai'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Belum Selesai</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $statistics['belum_selesai'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Progress</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $statistics['persentase_selesai'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sasaran Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($periodeAktif)
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Periode: {{ $periodeAktif->nama_periode }}</h3>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d/m/Y') }}</p>
                    </div>
                @endif

                @if($sasaranData->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500">Belum ada data sasaran kerja untuk periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sasaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sasaranData as $sasaran)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sasaran->pegawai->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $sasaran->pegawai->user->nip }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $sasaran->uraian_kegiatan }}</div>
                                        <div class="text-xs text-gray-500">Kode: {{ $sasaran->kode_sasaran }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            Kuantitas: {{ $sasaran->target_kuantitas }}<br>
                                            Kualitas: {{ $sasaran->target_kualitas }}%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $sasaran->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                               ($sasaran->status == 'submitted' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($sasaran->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($sasaran->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sasaran->realisasi)
                                            <div class="text-sm text-gray-900">
                                                {{ round(($sasaran->realisasi->realisasi_kuantitas / $sasaran->target_kuantitas) * 100) }}%
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $sasaran->realisasi->realisasi_kuantitas }} / {{ $sasaran->target_kuantitas }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">Belum ada realisasi</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
