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
}