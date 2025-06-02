<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SasaranKerja;
use App\Models\PenilaianSkp;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\RealisasiKerja;
use Illuminate\Support\Facades\Log;

class KepalaSekolahController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::count();
        $menungguPersetujuanCount = SasaranKerja::where('status', 'submitted')->count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        $totalPenilaianFinal = 0;
        $distribusiKategori = [];
        $skpDisetujuiCount = 0;
        $rataRataNilai = 0;
        $latestSasaranMenunggu = collect();

        if ($periodeAktif) {
            $totalPenilaianFinal = PenilaianSkp::where('periode_id', $periodeAktif->id)
                                            ->where('status', 'final')
                                            ->count();
            
            $distribusiKategori = PenilaianSkp::where('periode_id', $periodeAktif->id)
                ->where('status', 'final')
                ->select('kategori_nilai', DB::raw('count(*) as total'))
                ->groupBy('kategori_nilai')
                ->orderBy('kategori_nilai')
                ->pluck('total', 'kategori_nilai')
                ->toArray();

            $skpDisetujuiCount = SasaranKerja::where('periode_id', $periodeAktif->id)
                                            ->where('status', 'approved')
                                            ->count();

            $rataRataNilai = PenilaianSkp::where('periode_id', $periodeAktif->id)
                                        ->where('status', 'final')
                                        ->avg('nilai_akhir');
            
            $latestSasaranMenunggu = SasaranKerja::with(['pegawai.user', 'periode'])
                ->where('status', 'submitted')
                ->where('periode_id', $periodeAktif->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        } else {
            $distribusiKategori = PenilaianSkp::where('status', 'final')
                ->select('kategori_nilai', DB::raw('count(*) as total'))
                ->groupBy('kategori_nilai')
                ->orderBy('kategori_nilai')
                ->pluck('total', 'kategori_nilai')
                ->toArray();
            
            $skpDisetujuiCount = SasaranKerja::where('status', 'approved')->count();
            $rataRataNilai = PenilaianSkp::where('status', 'final')->avg('nilai_akhir');

            $latestSasaranMenunggu = SasaranKerja::with(['pegawai.user', 'periode'])
                ->where('status', 'submitted')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
        $rataRataNilai = $rataRataNilai ? round($rataRataNilai, 2) : 'N/A';

        $activitiesCollection = new Collection();

        Pegawai::with('user')->where('created_at', '>=', Carbon::now()->subDays(30))->orderBy('created_at', 'desc')->take(5)->get()->each(function ($pegawai) use ($activitiesCollection) {
            $pegawaiName = $pegawai->user->name ?? 'N/A';
            $activitiesCollection->push([
                'message' => "Pegawai baru: " . $pegawaiName,
                'activity_timestamp' => $pegawai->created_at,
                'icon' => 'fa-user-plus text-blue-500',
                'time_diff' => $pegawai->created_at->diffForHumans()
            ]);
        });

        SasaranKerja::with('pegawai.user')->where('updated_at', '>=', Carbon::now()->subDays(30))->orderBy('updated_at', 'desc')->take(10)->get()->each(function ($sasaran) use ($activitiesCollection) {
            $pegawaiName = $sasaran->pegawai->user->name ?? 'N/A';
            $message = '';
            $icon = 'fa-file-alt';
            $timestamp = $sasaran->updated_at;

            if ($sasaran->created_at == $sasaran->updated_at && $sasaran->status == 'draft') {
                $message = "Sasaran kerja (draft) untuk {$pegawaiName} telah dibuat.";
                $icon = 'fa-file-medical text-gray-500';
            } elseif ($sasaran->status == 'submitted') {
                $message = "Sasaran kerja dari {$pegawaiName} menunggu persetujuan.";
                $icon = 'fa-file-import text-yellow-500';
            } elseif ($sasaran->status == 'approved') {
                $message = "Sasaran kerja {$pegawaiName} telah disetujui.";
                $icon = 'fa-file-check text-green-500';
            } elseif ($sasaran->status == 'rejected') {
                $message = "Sasaran kerja {$pegawaiName} telah ditolak.";
                $icon = 'fa-file-excel text-red-500';
            }

            if (!empty($message)) {
                $activitiesCollection->push([
                    'message' => $message,
                    'activity_timestamp' => $timestamp,
                    'icon' => $icon,
                    'time_diff' => $timestamp->diffForHumans()
                ]);
            }
        });

        PenilaianSkp::with('pegawai.user')->where('status', 'final')->where('updated_at', '>=', Carbon::now()->subDays(30))->orderBy('updated_at', 'desc')->take(5)->get()->each(function ($penilaian) use ($activitiesCollection) {
            $pegawaiName = $penilaian->pegawai->user->name ?? 'N/A';
            $activitiesCollection->push([
                'message' => "Penilaian SKP untuk " . $pegawaiName . " telah difinalisasi.",
                'activity_timestamp' => $penilaian->updated_at,
                'icon' => 'fa-award text-purple-500',
                'time_diff' => $penilaian->updated_at->diffForHumans()
            ]);
        });

        $recentActivities = $activitiesCollection->sortByDesc('activity_timestamp')->take(10)->values()->all();

        return view('kepala.dashboard', compact(
            'totalPegawai',
            'menungguPersetujuanCount',
            'periodeAktif',
            'totalPenilaianFinal',
            'distribusiKategori',
            'skpDisetujuiCount',
            'rataRataNilai',
            'latestSasaranMenunggu',
            'recentActivities'
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

    public function penilaianSkpIndex(Request $request)
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $query = SasaranKerja::query()
            ->with(['pegawai.user', 'periode', 'penilaianSkp'])
            ->where('status', 'approved'); // Hanya SKP yang sudah disetujui

        if ($periodeAktif) {
            $query->where('periode_id', $periodeAktif->id);
        }

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('pegawai.user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $sasaranKerja = $query->paginate(10);

        return view('kepala.penilaian-skp.index', compact('sasaranKerja', 'periodeAktif'));
    }

    public function penilaianSkpCreate($id)
    {
        $sasaranKerja = SasaranKerja::with(['pegawai.user', 'periode', 'realisasiKerja', 'penilaianSkp'])
            ->findOrFail($id);
        
        // Cek apakah sudah ada penilaian final
        if ($sasaranKerja->penilaianSkp && $sasaranKerja->penilaianSkp->status == 'final') {
             return redirect()->route('kepala.penilaian-skp.index')->with('warning', 'SKP ini sudah dinilai final.');
        }

        return view('kepala.penilaian-skp.create', compact('sasaranKerja'));
    }

    public function penilaianSkpStore(Request $request, $id)
    {
        $request->validate([
            // Validasi untuk setiap aspek yang dinilai
            'nilai.kuantitas.ekspektasi' => 'nullable|string',
            'nilai.kuantitas.realisasi_dinilai' => 'required|numeric|min:0|max:100',
            'nilai.kualitas.ekspektasi' => 'nullable|string',
            'nilai.kualitas.realisasi_dinilai' => 'required|numeric|min:0|max:100',
            'nilai.waktu.ekspektasi' => 'nullable|string',
            'nilai.waktu.realisasi_dinilai' => 'nullable|numeric|min:0|max:100', // Nullable jika waktu tidak selalu dinilai
            'nilai.biaya.ekspektasi' => 'nullable|string',
            'nilai.biaya.realisasi_dinilai' => 'nullable|numeric|min:0|max:100', // Nullable jika biaya tidak selalu dinilai

            'catatan_kepala_sekolah' => 'nullable|string',
            'feedback_perilaku' => 'nullable|string',
            'penilaian_perilaku.*.aspek' => 'required|string',
            'penilaian_perilaku.*.skor' => 'required|integer|min:1|max:5',
            'status_penilaian' => 'required|in:draft,final',
        ]);

        $sasaranKerja = SasaranKerja::findOrFail($id);
        $periodeAktif = PeriodePenilaian::where('is_active', true)->firstOrFail();

        DB::beginTransaction();
        try {
            $detailPenilaianInput = $request->input('nilai', []);
            $totalNilaiRealisasiDinilai = 0;
            $jumlahAspekDinilai = 0;

            // Kuantitas dan Kualitas wajib ada
            if(isset($detailPenilaianInput['kuantitas']['realisasi_dinilai'])){
                $totalNilaiRealisasiDinilai += (float)$detailPenilaianInput['kuantitas']['realisasi_dinilai'];
                $jumlahAspekDinilai++;
            }
            if(isset($detailPenilaianInput['kualitas']['realisasi_dinilai'])){
                $totalNilaiRealisasiDinilai += (float)$detailPenilaianInput['kualitas']['realisasi_dinilai'];
                $jumlahAspekDinilai++;
            }
            // Waktu dan Biaya opsional (jika ada inputnya)
            if(isset($detailPenilaianInput['waktu']['realisasi_dinilai']) && is_numeric($detailPenilaianInput['waktu']['realisasi_dinilai'])){
                $totalNilaiRealisasiDinilai += (float)$detailPenilaianInput['waktu']['realisasi_dinilai'];
                $jumlahAspekDinilai++;
            }
            if(isset($detailPenilaianInput['biaya']['realisasi_dinilai']) && is_numeric($detailPenilaianInput['biaya']['realisasi_dinilai'])){
                $totalNilaiRealisasiDinilai += (float)$detailPenilaianInput['biaya']['realisasi_dinilai'];
                $jumlahAspekDinilai++;
            }

            $rataRataRealisasi = $jumlahAspekDinilai > 0 ? $totalNilaiRealisasiDinilai / $jumlahAspekDinilai : 0;

            $penilaian = PenilaianSkp::updateOrCreate(
                [
                    'sasaran_kerja_id' => $sasaranKerja->id,
                    'pegawai_id' => $sasaranKerja->pegawai_id,
                    'periode_id' => $periodeAktif->id,
                ],
                [
                    'detail_penilaian' => $detailPenilaianInput, // Simpan semua input nilai aspek
                    'nilai_rata_rata_realisasi' => round($rataRataRealisasi, 2),
                    'catatan_kepala_sekolah' => $request->catatan_kepala_sekolah,
                    'feedback_perilaku' => $request->feedback_perilaku,
                    'status' => $request->status_penilaian,
                    'penilai_id' => auth()->id(), // Simpan ID kepala sekolah yang menilai
                    'tanggal_penilaian' => now(),
                ]
            );

            // Simpan Penilaian Perilaku
            if ($request->has('penilaian_perilaku')) {
                // Hapus penilaian perilaku lama jika ada untuk menghindari duplikasi
                $penilaian->penilaianPerilaku()->delete(); 
                foreach ($request->penilaian_perilaku as $perilaku) {
                    $penilaian->penilaianPerilaku()->create([
                        'aspek_perilaku' => $perilaku['aspek'],
                        'skor' => $perilaku['skor'],
                        // Anda mungkin perlu menambahkan `periode_id` dan `pegawai_id` di sini 
                        // tergantung pada struktur tabel `penilaian_perilaku`
                    ]);
                }
            }
            
            // Hitung nilai akhir dan kategori jika status final
            if ($request->status_penilaian === 'final') {
                // Asumsi bobot: 70% SKP, 30% Perilaku
                // Anda perlu mengambil rata-rata skor perilaku
                $rataRataPerilaku = $penilaian->penilaianPerilaku()->avg('skor'); 
                // Skala perilaku 1-5, konversi ke 0-100 jika perlu, atau sesuaikan perhitungan nilai akhir
                // Contoh sederhana: $rataRataPerilaku = ($rataRataPerilaku / 5) * 100;

                $nilaiAkhir = ($rataRataRealisasi * 0.7) + ($rataRataPerilaku * 0.3); // Sesuaikan dengan skala skor perilaku
                $penilaian->nilai_akhir = round($nilaiAkhir, 2);
                $penilaian->kategori_nilai = $this->tentukanKategoriNilai($nilaiAkhir);
                $penilaian->save();
            }

            DB::commit();
            return redirect()->route('kepala.penilaian-skp.index')->with('success', 'Penilaian SKP berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    public function showBuktiDukungPenilaian($filename)
    {
        // Validasi filename dasar untuk mencegah directory traversal dari input URL
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            abort(404, 'Invalid filename.');
        }

        // Path yang disimpan di DB adalah 'bukti_dukung_realisasi/namafile.ext'
        // Kita perlu mencocokkannya dengan file di storage.
        // Jika file disimpan di storage/app/public/bukti_dukung_realisasi/, maka path untuk Storage::disk('local') adalah 'public/bukti_dukung_realisasi/'.
        $fullPathInStorage = 'public/bukti_dukung_realisasi/' . $filename;

        // Otorisasi tambahan: Pastikan file ini memang terkait dengan suatu realisasi yang bisa dilihat KS
        // Ini penting untuk mencegah seseorang menebak nama file dan mengaksesnya langsung.
        // Untuk implementasi yang lebih aman, Anda mungkin ingin meneruskan ID realisasi atau SKP
        // dan memverifikasi bahwa $filename adalah bukti dukung yang sah untuk record tersebut.
        $realisasiExists = RealisasiKerja::where('bukti_dukung', 'bukti_dukung_realisasi/' . $filename)->exists();
        if (!$realisasiExists) {
             abort(403, 'File not linked to any realization record or unauthorized.');
        }

        if (!Storage::disk('local')->exists($fullPathInStorage)) {
             Log::warning('KepalaSekolahController@showBuktiDukungPenilaian: File tidak ditemukan di storage.', ['path' => $fullPathInStorage, 'filename_param' => $filename]);
            abort(404, 'File not found in storage.');
        }

        Log::info('KepalaSekolahController@showBuktiDukungPenilaian: File ditemukan, mengirim respons.', ['path' => $fullPathInStorage]);
        return Storage::disk('local')->response($fullPathInStorage);
    }

    private function tentukanKategoriNilai($nilai)
    {
        if ($nilai >= 90) return 'Sangat Baik';
        if ($nilai >= 75) return 'Baik';
        if ($nilai >= 60) return 'Cukup';
        if ($nilai >= 50) return 'Kurang';
        return 'Sangat Kurang';
    }
}