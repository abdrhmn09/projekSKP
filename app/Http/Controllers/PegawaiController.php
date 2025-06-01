<?php

namespace App\Http\Controllers;

use App\Models\SasaranKerja;
use App\Models\RealisasiKerja;
use App\Models\PenilaianSkp;
use App\Models\PeriodePenilaian;
use App\Models\RencanaTindakLanjut;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function dashboard()
    {
        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        $totalSasaran = 0;
        $sasaranApproved = 0;
        $statusPenilaian = 'Belum dimulai';

        if ($pegawai && $periodeAktif) {
            $totalSasaran = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->count();

            $sasaranApproved = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'approved')
                ->count();

            $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

            if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
            }
        }

        return view('pegawai.dashboard', compact(
            'totalSasaran',
            'sasaranApproved',
            'statusPenilaian',
            'periodeAktif'
        ));
    }

    // Sasaran Kerja
    public function sasaranIndex()
    {
        $pegawai = auth()->user()->pegawai;
        $sasaran = SasaranKerja::where('pegawai_id', $pegawai->id)
            ->with('periode')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pegawai.sasaran.index', compact('sasaran'));
    }

    public function sasaranCreate()
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.sasaran')->with('error', 'Tidak ada periode penilaian yang aktif');
        }

        return view('pegawai.sasaran.create', compact('periodeAktif'));
    }

    public function sasaranStore(Request $request)
    {
        $request->validate([
            'uraian_sasaran' => 'required|string',
            'indikator_kinerja' => 'required|string',
            'target_kuantitas' => 'required|integer|min:1',
            'satuan_kuantitas' => 'required|string',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0',
        ]);

        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        SasaranKerja::create([
            'pegawai_id' => $pegawai->id,
            'periode_id' => $periodeAktif->id,
            'uraian_sasaran' => $request->uraian_sasaran,
            'indikator_kinerja' => $request->indikator_kinerja,
            'target_kuantitas' => $request->target_kuantitas,
            'satuan_kuantitas' => $request->satuan_kuantitas,
            'target_kualitas' => $request->target_kualitas,
            'target_waktu' => $request->target_waktu,
            'target_biaya' => $request->target_biaya,
            'status' => 'submitted',
        ]);

        return redirect()->route('pegawai.sasaran')->with('success', 'Sasaran kerja berhasil disubmit');
    }

    // Realisasi Kerja
    public function realisasiIndex()
    {
        $pegawai = auth()->user()->pegawai;
        $realisasi = RealisasiKerja::whereHas('sasaranKerja', function($query) use ($pegawai) {
            $query->where('pegawai_id', $pegawai->id);
        })->with('sasaranKerja')->paginate(10);

        return view('pegawai.realisasi.index', compact('realisasi'));
    }

    public function realisasiCreate()
    {
        $pegawai = auth()->user()->pegawai;
        $sasaranApproved = SasaranKerja::where('pegawai_id', $pegawai->id)
            ->where('status', 'approved')
            ->get();

        return view('pegawai.realisasi.create', compact('sasaranApproved'));
    }

    public function realisasiStore(Request $request)
    {
        $request->validate([
            'sasaran_kerja_id' => 'required|exists:sasaran_kerja,id',
            'uraian_realisasi' => 'required|string',
            'realisasi_kuantitas' => 'required|integer|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|string',
        ]);

        RealisasiKerja::create($request->all());

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil diinput');
    }

    public function penilaian()
    {
        $pegawai = auth()->user()->pegawai;
        $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
            ->with(['periode', 'penilai'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pegawai.penilaian', compact('penilaian'));
    }

    public function rencanaIndex()
    {
        $pegawai = auth()->user()->pegawai;
        $rencana = RencanaTindakLanjut::whereHas('penilaianSkp', function($query) use ($pegawai) {
            $query->where('pegawai_id', $pegawai->id);
        })->with('penilaianSkp')->paginate(10);

        return view('pegawai.rencana.index', compact('rencana'));
    }

    public function rencanaStore(Request $request)
    {
        $request->validate([
            'penilaian_skp_id' => 'required|exists:penilaian_skp,id',
            'rencana_perbaikan' => 'required|string',
            'strategi_pencapaian' => 'required|string',
            'target_penyelesaian' => 'required|date',
            'indikator_keberhasilan' => 'required|string',
        ]);

        RencanaTindakLanjut::create($request->all());

        return redirect()->route('pegawai.rencana')->with('success', 'Rencana tindak lanjut berhasil disimpan');
    }
}