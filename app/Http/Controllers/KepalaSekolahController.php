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
        
        // Tambahkan data sasaran yang menunggu persetujuan
        $sasaranMenunggu = SasaranKerja::with(['pegawai.user', 'periode'])
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();

        // Ambil aktivitas terbaru (gabungan dari sasaran baru dan yang disetujui/ditolak)
        $aktivitasTerbaru = SasaranKerja::with(['pegawai'])
            ->where(function($query) {
                $query->where('status', 'submitted')
                      ->orWhere('status', 'approved')
                      ->orWhere('status', 'rejected');
            })
            ->latest()
            ->take(5)
            ->get();

        // Hitung rata-rata nilai
        $rataRataNilai = PenilaianSkp::where('status', 'final')
            ->avg('nilai_akhir');

        // Hitung distribusi nilai
        $totalPenilaian = PenilaianSkp::where('status', 'final')->count();
        
        $distribusiNilai = [
            'Sangat Baik' => 0,
            'Baik' => 0,
            'Butuh Perbaikan' => 0,
            'Kurang' => 0,
            'Sangat Kurang' => 0
        ];

        $jumlahNilai = [
            'Sangat Baik' => 0,
            'Baik' => 0,
            'Butuh Perbaikan' => 0,
            'Kurang' => 0,
            'Sangat Kurang' => 0
        ];

        if ($totalPenilaian > 0) {
            foreach ($distribusiNilai as $kategori => $nilai) {
                $jumlah = PenilaianSkp::where('status', 'final')
                    ->where('kategori_nilai', $kategori)
                    ->count();
                
                $distribusiNilai[$kategori] = ($jumlah / $totalPenilaian) * 100;
                $jumlahNilai[$kategori] = $jumlah;
            }
        }

        return view('kepala.dashboard', compact(
            'totalPegawai',
            'menungguPersetujuan',
            'sudahDiapprove',
            'periodeAktif',
            'sasaranMenunggu',
            'aktivitasTerbaru',
            'rataRataNilai',
            'distribusiNilai',
            'jumlahNilai'
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
        $sasaran = SasaranKerja::with(['pegawai.user', 'periode', 'realisasi'])
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
        $sasaranData = collect(); // Initialize as empty collection instead of array

        if ($periodeAktif) {
            $sasaranData = SasaranKerja::with(['pegawai.user', 'realisasi'])
                ->where('periode_id', $periodeAktif->id)
                ->get();
        } else {
            session()->flash('warning', 'Tidak ada periode aktif saat ini.');
        }

        // Prepare statistics
        $totalSasaran = $sasaranData->count();
        $sasaranSelesai = $sasaranData->filter(function($sasaran) {
            return $sasaran->realisasi !== null;
        })->count();
        $sasaranBelumSelesai = $totalSasaran - $sasaranSelesai;
        
        $statistics = [
            'total' => $totalSasaran,
            'selesai' => $sasaranSelesai,
            'belum_selesai' => $sasaranBelumSelesai,
            'persentase_selesai' => $totalSasaran > 0 ? round(($sasaranSelesai / $totalSasaran) * 100) : 0
        ];

        return view('kepala.monitoring', compact('sasaranData', 'periodeAktif', 'statistics'));
    }

    public function laporan()
    {
        // Get active period
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        // Get all periods for filter
        $periodePenilaian = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        
        // Query base
        $baseQuery = PenilaianSkp::query()->where('status', 'final');
            
        // Filter by period if requested
        if (request('periode_id')) {
            $baseQuery->where('periode_id', request('periode_id'));
        }
        
        // Get paginated results with relationships
        $data = (clone $baseQuery)
            ->with(['pegawai.user', 'periode', 'penilai'])
            ->latest('tanggal_penilaian')
            ->paginate(10);
        
        // Calculate statistics
        $statistik = [
            'total_dinilai' => (clone $baseQuery)->count(),
            'rata_rata' => (clone $baseQuery)->avg('nilai_akhir'),
            'nilai_tertinggi' => (clone $baseQuery)->max('nilai_akhir'),
            'nilai_terendah' => (clone $baseQuery)->min('nilai_akhir'),
        ];
        
        // Get distribution with separate queries
        $distribusi = [
            'Sangat Baik' => (clone $baseQuery)->where('kategori_nilai', 'Sangat Baik')->count(),
            'Baik' => (clone $baseQuery)->where('kategori_nilai', 'Baik')->count(),
            'Butuh Perbaikan' => (clone $baseQuery)->where('kategori_nilai', 'Butuh Perbaikan')->count(),
            'Kurang' => (clone $baseQuery)->where('kategori_nilai', 'Kurang')->count(),
            'Sangat Kurang' => (clone $baseQuery)->where('kategori_nilai', 'Sangat Kurang')->count(),
        ];

        return view('kepala.laporan', compact(
            'data', 
            'periodeAktif',
            'periodePenilaian',
            'statistik',
            'distribusi'
        ));
    }

    public function penilaian()
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $sasaranApproved = [];

        if ($periodeAktif) {
            $sasaranApproved = SasaranKerja::with(['pegawai.user', 'realisasi'])
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'approved')
                ->get();

            // Debug information
            if ($sasaranApproved->isEmpty()) {
                session()->flash('error', 'Tidak ada sasaran yang disetujui untuk periode ini');
            } else {
                foreach ($sasaranApproved as $sasaran) {
                    $sudahDinilai = PenilaianSkp::where('pegawai_id', $sasaran->pegawai_id)
                        ->where('periode_id', $sasaran->periode_id)
                        ->exists();
                    
                    if (!$sudahDinilai) {
                        session()->flash('info', 'Ada sasaran yang belum dinilai');
                        break;
                    }
                }
            }
        } else {
            session()->flash('error', 'Tidak ada periode aktif saat ini');
        }

        return view('kepala.penilaian.index', compact('sasaranApproved', 'periodeAktif'));
    }

    public function penilaianCreate($sasaranId)
    {
        $sasaran = SasaranKerja::with(['pegawai.user', 'periode', 'realisasi'])
            ->where('status', 'approved')
            ->findOrFail($sasaranId);

        // Check if already evaluated
        $existingPenilaian = PenilaianSkp::where('pegawai_id', $sasaran->pegawai_id)
            ->where('periode_id', $sasaran->periode_id)
            ->first();

        if ($existingPenilaian) {
            return redirect()->route('kepala.penilaian.index')->with('error', 'Pegawai ini sudah dinilai untuk periode tersebut');
        }

        return view('kepala.penilaian.create', compact('sasaran'));
    }

    public function penilaianStore(Request $request, $sasaranId)
    {
        $request->validate([
            'nilai_skp' => 'required|numeric|min:0|max:100',
            'nilai_perilaku' => 'required|numeric|min:0|max:100',
            'catatan_penilaian' => 'nullable|string',
        ]);

        $sasaran = SasaranKerja::with(['pegawai', 'periode'])
            ->where('status', 'approved')
            ->findOrFail($sasaranId);

        // Calculate final score (weighted average)
        $nilaiAkhir = ($request->nilai_skp * 0.6) + ($request->nilai_perilaku * 0.4);

        // Determine category
        $kategoriNilai = '';
        if ($nilaiAkhir >= 90) {
            $kategoriNilai = 'Sangat Baik';
        } elseif ($nilaiAkhir >= 76) {
            $kategoriNilai = 'Baik';
        } elseif ($nilaiAkhir >= 61) {
            $kategoriNilai = 'Butuh Perbaikan';
        } elseif ($nilaiAkhir >= 51) {
            $kategoriNilai = 'Kurang';
        } else {
            $kategoriNilai = 'Sangat Kurang';
        }

        PenilaianSkp::create([
            'pegawai_id' => $sasaran->pegawai_id,
            'periode_id' => $sasaran->periode_id,
            'sasaran_kerja_id' => $sasaran->id,
            'nilai_skp' => $request->nilai_skp,
            'nilai_perilaku' => $request->nilai_perilaku,
            'nilai_akhir' => $nilaiAkhir,
            'kategori_nilai' => $kategoriNilai,
            'catatan_penilaian' => $request->catatan_penilaian,
            'penilai_id' => auth()->id(),
            'status' => 'final',
            'tanggal_penilaian' => now(),
        ]);

        return redirect()->route('kepala.penilaian.index')->with('success', 'Penilaian SKP berhasil disimpan');
    }
}