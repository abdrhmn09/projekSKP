<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SasaranKerja;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = [];

        if ($user->role == 'admin') {
            $notifications = $this->getAdminNotifications();
        } elseif ($user->role == 'kepala_sekolah') {
            $notifications = $this->getKepalaSekolahNotifications();
        } else {
            $notifications = $this->getPegawaiNotifications();
        }

        return response()->json($notifications);
    }

    private function getAdminNotifications()
    {
        $notifications = [];

        // Check for inactive periods
        $activePeriod = PeriodePenilaian::where('is_active', true)->first();
        if (!$activePeriod) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Periode Tidak Aktif',
                'message' => 'Tidak ada periode penilaian yang aktif saat ini.',
                'url' => route('admin.periode'),
                'time' => now()
            ];
        }

        // Check for periods ending soon
        if ($activePeriod && $activePeriod->tanggal_selesai <= now()->addDays(7)) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Periode Akan Berakhir',
                'message' => 'Periode ' . $activePeriod->nama_periode . ' akan berakhir dalam 7 hari.',
                'url' => route('admin.periode'),
                'time' => now()
            ];
        }

        return $notifications;
    }

    private function getKepalaSekolahNotifications()
    {
        $notifications = [];

        // Check for pending approvals
        $pendingCount = SasaranKerja::where('status', 'submitted')->count();
        if ($pendingCount > 0) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'SKP Menunggu Persetujuan',
                'message' => "Ada {$pendingCount} SKP yang menunggu persetujuan Anda.",
                'url' => route('kepala.persetujuan'),
                'time' => now()
            ];
        }

        return $notifications;
    }

    private function getPegawaiNotifications()
    {
        $notifications = [];
        $pegawai = Auth::user()->pegawai;

        if (!$pegawai) {
            return $notifications;
        }

        // Check for active period without sasaran
        $activePeriod = PeriodePenilaian::where('is_active', true)->first();
        if ($activePeriod) {
            $sasaranCount = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $activePeriod->id)
                ->count();

            if ($sasaranCount == 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Belum Membuat Sasaran',
                    'message' => 'Anda belum membuat sasaran kerja untuk periode ' . $activePeriod->nama_periode,
                    'url' => route('pegawai.sasaran.create'),
                    'time' => now()
                ];
            }
        }

        // Check for rejected sasaran
        $rejectedCount = SasaranKerja::where('pegawai_id', $pegawai->id)
            ->where('status', 'rejected')
            ->count();

        if ($rejectedCount > 0) {
            $notifications[] = [
                'type' => 'error',
                'title' => 'SKP Ditolak',
                'message' => "Ada {$rejectedCount} sasaran kerja yang ditolak. Silakan perbaiki.",
                'url' => route('pegawai.sasaran'),
                'time' => now()
            ];
        }

        return $notifications;
    }

    public function markAsRead(Request $request)
    {
        // In a real implementation, you would store notifications in database
        // and mark them as read here
        return response()->json(['success' => true]);
    }
}
