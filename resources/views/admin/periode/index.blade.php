@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kelola Periode Penilaian</h1>
            <a href="{{ route('admin.periode.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Tambah Periode
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periode as $p)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->nama_periode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($p->jenis_periode) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($p->tanggal_mulai)->isoFormat('LL') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($p->tanggal_selesai)->isoFormat('LL') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($p->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i>Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(!$p->is_active)
                                <form action="{{ route('admin.periode.activate', $p->id) }}" method="POST" class="inline-block mr-2 mb-1 lg:mb-0"
                                      onsubmit="return confirm('Yakin ingin mengaktifkan periode \'{{ $p->nama_periode }}\'? Periode lain yang aktif akan dinonaktifkan.');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-semibold" title="Aktifkan Periode">
                                        <i class="fas fa-toggle-on mr-1"></i>Aktifkan
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.periode.edit', $p->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2 font-semibold" title="Edit Periode">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if(!$p->is_active)
                                <form action="{{ route('admin.periode.destroy', $p->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode \'{{ $p->nama_periode }}\'? Tindakan ini tidak dapat diurungkan dan hanya bisa dilakukan jika periode tidak aktif dan tidak memiliki SKP atau Penilaian terkait.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold" title="Hapus Periode">
                                        <i class="fas fa-trash-alt mr-1"></i>Hapus
                                    </button>
                                </form>
                            @else
                                <button class="text-gray-400 cursor-not-allowed font-semibold" title="Periode aktif tidak dapat dihapus">
                                    <i class="fas fa-trash-alt mr-1"></i>Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada data periode penilaian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="mt-4">
            {{ $periode->links() }}
        </div>
    </div>
</div>
@endsection
