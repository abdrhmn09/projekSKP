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
    // Added periode data to sasaran create view
    public function dashboard()
    {
        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        $totalSasaran = 0;
        $sasaranDisetujui = 0;
        $progressRealisasi = 0;
        $statusPenilaian = 'Belum dimulai';
        $sasaranKerja = collect();

        if ($pegawai && $periodeAktif) {
            // Get all sasaran kerja for current period
            $sasaranKerja = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->with('realisasi')
                ->get();

            $totalSasaran = $sasaranKerja->count();
            $sasaranDisetujui = $sasaranKerja->where('status', 'approved')->count();

            // Calculate overall progress
            if ($sasaranKerja->count() > 0) {
                $totalProgress = 0;
                foreach ($sasaranKerja as $sasaran) {
                    if ($sasaran->realisasi) {
                        $realisasiProgress = ($sasaran->realisasi->realisasi_kuantitas / $sasaran->target_kuantitas) * 100;
                        $totalProgress += $realisasiProgress * ($sasaran->bobot_persen / 100);
                    }
                }
                $progressRealisasi = round($totalProgress);
            }

            // Get penilaian status
            $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

            if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
            }
        }

        // Get timeline data
        $timeline = [];
        
        // Add sasaran creation to timeline
        if ($sasaranKerja->isNotEmpty()) {
            $timeline[] = [
                'event' => 'Sasaran kerja dibuat',
                'status' => $sasaranKerja->first()->status,
                'date' => $sasaranKerja->first()->created_at,
                'deadline' => $periodeAktif ? $periodeAktif->tanggal_selesai : null
            ];
        }

        // Add realisasi to timeline if exists
        $realisasiExists = $sasaranKerja->contains(function ($sasaran) {
            return $sasaran->realisasi !== null;
        });

        if ($realisasiExists) {
            $timeline[] = [
                'event' => 'Input realisasi kerja',
                'status' => 'completed',
                'date' => now(),
                'deadline' => $periodeAktif ? $periodeAktif->tanggal_selesai : null
            ];
        }

        // Sort timeline by date
        $timeline = collect($timeline)->sortBy('date');

        return view('pegawai.dashboard', compact(
            'totalSasaran',
            'sasaranDisetujui',
            'progressRealisasi',
            'statusPenilaian',
            'periodeAktif',
            'sasaranKerja',
            'timeline'
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
        $periode = PeriodePenilaian::where('is_active', true)->get();
        return view('pegawai.sasaran.create', compact('periode'));
    }

    public function sasaranDetail($id)
    {
        $pegawai = auth()->user()->pegawai;
        $sasaran = SasaranKerja::with(['periode', 'realisasi'])
            ->where('pegawai_id', $pegawai->id)
            ->findOrFail($id);

        return view('pegawai.sasaran.detail', compact('sasaran'));
    }

    //The 'target_waktu' field in the validation rules and SasaranKerja::create method has been changed from 'date' to 'string'
    public function sasaranStore(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'kode_sasaran' => 'required|string|max:255',
            'uraian_kegiatan' => 'required|string',
            'target_kuantitas' => 'required|string',
            'target_kualitas' => 'required|string',
            'target_waktu' => 'required|date',
            'bobot_persen' => 'required|numeric|min:0|max:100',
        ]);

        $pegawai = auth()->user()->pegawai;

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');
        }

        $status = $request->input('status') === 'diajukan' ? 'submitted' : 'draft';

        SasaranKerja::create([
            'pegawai_id' => $pegawai->id,
            'periode_id' => $request->periode_id,
            'kode_sasaran' => $request->kode_sasaran,
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'target_kuantitas' => $request->target_kuantitas,
            'target_kualitas' => $request->target_kualitas,
            'target_waktu' => $request->target_waktu,
            'bobot_persen' => $request->bobot_persen,
            'status' => $status,
        ]);

        $message = $status === 'submitted' ? 'Sasaran kerja berhasil diajukan untuk persetujuan' : 'Sasaran kerja berhasil disimpan sebagai draft';

        return redirect()->route('pegawai.sasaran')->with('success', $message);
    }

    public function sasaranEdit($id)
    {
        $pegawai = auth()->user()->pegawai;
        $sasaran = SasaranKerja::where('pegawai_id', $pegawai->id)->findOrFail($id);
        
        // Prevent editing approved sasaran
        if ($sasaran->status === 'approved') {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Sasaran kerja yang sudah disetujui tidak dapat diubah');
        }

        // Only allow editing draft or rejected sasaran
        if (!in_array($sasaran->status, ['draft', 'rejected'])) {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Hanya sasaran dengan status draft atau ditolak yang dapat diedit');
        }

        $periode = PeriodePenilaian::where('is_active', true)->get();
        return view('pegawai.sasaran.edit', compact('sasaran', 'periode'));
    }

    public function sasaranUpdate(Request $request, $id)
    {
        $pegawai = auth()->user()->pegawai;
        $sasaran = SasaranKerja::where('pegawai_id', $pegawai->id)->findOrFail($id);

        // Prevent updating approved sasaran
        if ($sasaran->status === 'approved') {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Sasaran kerja yang sudah disetujui tidak dapat diubah');
        }

        // Only allow updating draft or rejected sasaran
        if (!in_array($sasaran->status, ['draft', 'rejected'])) {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Hanya sasaran dengan status draft atau ditolak yang dapat diedit');
        }

        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'kode_sasaran' => 'required|string|max:255',
            'uraian_kegiatan' => 'required|string',
            'target_kuantitas' => 'required|string',
            'target_kualitas' => 'required|string',
            'target_waktu' => 'required|date',
            'bobot_persen' => 'required|numeric|min:0|max:100',
        ]);

        $status = $request->input('status') === 'diajukan' ? 'submitted' : 'draft';

        $sasaran->update([
            'periode_id' => $request->periode_id,
            'kode_sasaran' => $request->kode_sasaran,
            'uraian_kegiatan' => $request->uraian_kegiatan,
            'target_kuantitas' => $request->target_kuantitas,
            'target_kualitas' => $request->target_kualitas,
            'target_waktu' => $request->target_waktu,
            'bobot_persen' => $request->bobot_persen,
            'status' => $status,
        ]);

        $message = $status === 'submitted' ? 'Sasaran kerja berhasil diperbarui dan diajukan' : 'Sasaran kerja berhasil diperbarui sebagai draft';
        return redirect()->route('pegawai.sasaran')->with('success', $message);
    }

    public function sasaranDestroy($id)
    {
        $pegawai = auth()->user()->pegawai;
        $sasaran = SasaranKerja::where('pegawai_id', $pegawai->id)->findOrFail($id);

        // Prevent deleting approved sasaran
        if ($sasaran->status === 'approved') {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Sasaran kerja yang sudah disetujui tidak dapat dihapus');
        }

        // Only allow deleting draft or rejected sasaran
        if (!in_array($sasaran->status, ['draft', 'rejected'])) {
            return redirect()->route('pegawai.sasaran')
                ->with('error', 'Hanya sasaran dengan status draft atau ditolak yang dapat dihapus');
        }

        $sasaran->delete();
        return redirect()->route('pegawai.sasaran')->with('success', 'Sasaran kerja berhasil dihapus');
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
            'bukti_pendukung' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // max 10MB
        ]);

        try {
            $data = $request->except('bukti_pendukung');
            
            // Handle file upload
            if ($request->hasFile('bukti_pendukung')) {
                $file = $request->file('bukti_pendukung');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/bukti_pendukung', $filename);
                $data['bukti_pendukung'] = $filename;
            }

            RealisasiKerja::create($data);

            return redirect()->route('pegawai.realisasi')
                           ->with('success', 'Realisasi kerja berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
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
```

```php
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
        $totalRealisasi = 0;
        $statusPenilaian = 'Belum dimulai';


        if ($pegawai && $periodeAktif) {
            $totalSasaran = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)->count();
            $sasaranApproved = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'approved')->count();
            $totalRealisasi = RealisasiKerja::whereHas('sasaranKerja', function($query) use ($pegawai, $periodeAktif) {
                $query->where('pegawai_id', $pegawai->id)
                      ->where('periode_id', $periodeAktif->id);
            })->count();

            $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

             if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
            }
        }

        return view('pegawai.dashboard', compact('totalSasaran', 'sasaranApproved', 'totalRealisasi', 'periodeAktif','statusPenilaian'));
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
            'target_kuantitas' => 'required|numeric|min:1',
            'satuan_kuantitas' => 'required|string',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0'
        ]);

        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif');
        }

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
            'status' => 'submitted'
        ]);

        return redirect()->route('pegawai.sasaran')->with('success', 'Sasaran kerja berhasil dibuat');
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
            'realisasi_kuantitas' => 'required|numeric|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|string'
        ]);

        RealisasiKerja::create($request->all());

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil disimpan');
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
```

```php
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
        $totalRealisasi = 0;
        $statusPenilaian = 'Belum dimulai';


        if ($pegawai && $periodeAktif) {
            $totalSasaran = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)->count();
            $sasaranApproved = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'approved')->count();
            $totalRealisasi = RealisasiKerja::whereHas('sasaranKerja', function($query) use ($pegawai, $periodeAktif) {
                $query->where('pegawai_id', $pegawai->id)
                      ->where('periode_id', $periodeAktif->id);
            })->count();

            $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

             if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
            }
        }

        return view('pegawai.dashboard', compact('totalSasaran', 'sasaranApproved', 'totalRealisasi', 'periodeAktif','statusPenilaian'));
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
            'target_kuantitas' => 'required|numeric|min:1',
            'satuan_kuantitas' => 'required|string',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0'
        ]);

        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif');
        }

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
            'status' => 'submitted'
        ]);

        return redirect()->route('pegawai.sasaran')->with('success', 'Sasaran kerja berhasil dibuat');
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
            'realisasi_kuantitas' => 'required|numeric|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|string'
        ]);

        RealisasiKerja::create($request->all());

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil disimpan');
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
```

```php
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
        $totalRealisasi = 0;
        $statusPenilaian = 'Belum dimulai';


        if ($pegawai && $periodeAktif) {
            $totalSasaran = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)->count();
            $sasaranApproved = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'approved')->count();
            $totalRealisasi = RealisasiKerja::whereHas('sasaranKerja', function($query) use ($pegawai, $periodeAktif) {
                $query->where('pegawai_id', $pegawai->id)
                      ->where('periode_id', $periodeAktif->id);
            })->count();

            $penilaian = PenilaianSkp::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();

             if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
            }
        }

        return view('pegawai.dashboard', compact('totalSasaran', 'sasaranApproved', 'totalRealisasi', 'periodeAktif','statusPenilaian'));
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
            'target_kuantitas' => 'required|numeric|min:1',
            'satuan_kuantitas' => 'required|string',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0'
        ]);

        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode penilaian yang aktif');
        }

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
            'status' => 'submitted'
        ]);

        return redirect()->route('pegawai.sasaran')->with('success', 'Sasaran kerja berhasil dibuat');
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
            'realisasi_kuantitas' => 'required|numeric|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|string'
        ]);

        RealisasiKerja::create($request->all());

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil disimpan');
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