<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SasaranKerja;
use App\Models\PenilaianSkp;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;

class KepalaSekolahController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::count();
        $menungguPersetujuan = SasaranKerja::where('status', 'submitted')->count();
        $sudahDiapprove = SasaranKerja::where('status', 'approved')->count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        return view('kepala.dashboard', compact(
            'totalPegawai',
            'menungguPersetujuan',
            'sudahDiapprove',
            'periodeAktif'
        ));
    }

    public function persetujuan()
    {
        $sasaranMenunggu = SasaranKerja::with(['pegawai.user', 'periode'])
            ->where('status', 'submitted')
            ->paginate(10);

        return view('kepala.persetujuan.index', compact('sasaranMenunggu'));
    }

    public function persetujuanDetail($id)
    {
        $sasaran = SasaranKerja::with(['pegawai.user', 'periode', 'realisasiKerja'])
            ->findOrFail($id);

        return view('kepala.persetujuan.detail', compact('sasaran'));
    }

    public function approve(Request $request, $id)
    {
        $sasaran = SasaranKerja::findOrFail($id);
        $sasaran->update([
            'status' => 'approved',
            'catatan_penilaian' => $request->catatan ?? 'Disetujui'
        ]);

        return redirect()->route('kepala.persetujuan')->with('success', 'Sasaran kerja berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $sasaran = SasaranKerja::findOrFail($id);
        $sasaran->update([
            'status' => 'rejected',
            'catatan_penilaian' => $request->catatan
        ]);

        return redirect()->route('kepala.persetujuan')->with('success', 'Sasaran kerja telah ditolak');
    }

    public function monitoring()
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $sasaranData = [];

        if ($periodeAktif) {
            $sasaranData = SasaranKerja::with(['pegawai.user', 'realisasiKerja'])
                ->where('periode_id', $periodeAktif->id)
                ->get();
        }

        return view('kepala.monitoring', compact('sasaranData', 'periodeAktif'));
    }

    public function laporan()
    {
        $data = PenilaianSkp::with(['pegawai.user', 'periode'])->paginate(10);
        return view('kepala.laporan', compact('data'));
    }
}