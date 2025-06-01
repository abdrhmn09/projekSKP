<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SasaranKerja;
use App\Models\PeriodePenilaian;
use App\Models\Notification;
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

        // Store notifications in database if they don't exist
        foreach ($notifications as $notification) {
            Notification::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'type' => $notification['type']
                ],
                [
                    'url' => $notification['url'],
                    'is_read' => false
                ]
            );
        }

        // Get all notifications from database
        $dbNotifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($dbNotifications);
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

        // Check for periods ending soon or already ended
        if ($activePeriod) {
            $daysUntilEnd = (int)now()->diffInDays($activePeriod->tanggal_selesai, false);
            
            if ($daysUntilEnd < 0) {
                $notifications[] = [
                    'type' => 'error',
                    'title' => 'Periode Telah Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' telah berakhir ' . abs($daysUntilEnd) . ' hari yang lalu.',
                    'url' => route('admin.periode'),
                    'time' => now()
                ];
            } elseif ($daysUntilEnd <= 7) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Periode Akan Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' akan berakhir dalam ' . $daysUntilEnd . ' hari.',
                    'url' => route('admin.periode'),
                    'time' => now()
                ];
            }
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

        // Check active period status
        $activePeriod = PeriodePenilaian::where('is_active', true)->first();
        if ($activePeriod) {
            $daysUntilEnd = (int)now()->diffInDays($activePeriod->tanggal_selesai, false);
            
            if ($daysUntilEnd < 0) {
                $notifications[] = [
                    'type' => 'error',
                    'title' => 'Periode Telah Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' telah berakhir ' . abs($daysUntilEnd) . ' hari yang lalu.',
                    'url' => route('kepala.penilaian.index'),
                    'time' => now()
                ];
            } elseif ($daysUntilEnd <= 7) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Periode Akan Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' akan berakhir dalam ' . $daysUntilEnd . ' hari.',
                    'url' => route('kepala.penilaian.index'),
                    'time' => now()
                ];
            }
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

        // Check for active period status
        $activePeriod = PeriodePenilaian::where('is_active', true)->first();
        if ($activePeriod) {
            $daysUntilEnd = (int)now()->diffInDays($activePeriod->tanggal_selesai, false);
            
            // Check if period has ended or ending soon
            if ($daysUntilEnd < 0) {
                $notifications[] = [
                    'type' => 'error',
                    'title' => 'Periode Telah Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' telah berakhir ' . abs($daysUntilEnd) . ' hari yang lalu.',
                    'url' => route('pegawai.sasaran'),
                    'time' => now()
                ];
            } elseif ($daysUntilEnd <= 7) {
                $notifications[] = [
                    'type' => 'warning',
                    'title' => 'Periode Akan Berakhir',
                    'message' => 'Periode ' . $activePeriod->nama_periode . ' akan berakhir dalam ' . $daysUntilEnd . ' hari.',
                    'url' => route('pegawai.sasaran'),
                    'time' => now()
                ];
            }

            // Check for missing sasaran
            $sasaranCount = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $activePeriod->id)
                ->count();

            if ($sasaranCount == 0 && $daysUntilEnd > 0) {
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
        $user = Auth::user();
        
        if ($request->has('notification_id')) {
            // Mark specific notification as read
            Notification::where('id', $request->notification_id)
                ->where('user_id', $user->id)
                ->update(['is_read' => true]);
        } else {
            // Mark all notifications as read
            Notification::where('user_id', $user->id)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read successfully'
        ]);
    }
}
