<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SasaranKerja;
use App\Models\PeriodePenilaian;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        // Step 1: Generate notifications based on current state (if applicable for the role)
        if ($user->role == 'admin') {
            $this->generateAdminNotifications($user);
        } elseif ($user->role == 'kepala_sekolah') {
            $this->generateKepalaSekolahNotifications($user);
        } elseif ($user->role == 'pegawai') {
            $this->generatePegawaiNotifications($user);
        }

        // Step 2: Fetch unread notifications from the database
        $unreadNotifications = Notification::where('user_id', $user->id)
                                           ->whereNull('read_at')
                                           ->orderBy('created_at', 'desc')
                                           ->get()
                                           ->map(function ($notification) {
                                                return [
                                                    'id' => $notification->id,
                                                    'type' => $notification->type,
                                                    'title' => $notification->title,
                                                    'message' => $notification->message,
                                                    'url' => $notification->url,
                                                    // Format time for frontend consistency, using created_at from DB
                                                    'time' => $notification->created_at->toISOString() 
                                                ];
                                           });

        return response()->json($unreadNotifications);
    }

    private function createNotification(User $user, string $type, string $title, string $message, ?string $url = null)
    {
        // Check if an identical notification (user, title, message) already exists, regardless of read_at status.
        $existing = Notification::where('user_id', $user->id)
                                ->where('title', $title)
                                ->where('message', $message)
                                ->first();

        if (!$existing) {
            Notification::create([
                'user_id' => $user->id,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
                'url'     => $url,
            ]);
        }
    }

    private function generateAdminNotifications(User $adminUser)
    {
        $activePeriod = PeriodePenilaian::where('is_active', true)->first();

        if ($activePeriod) {
            if ($activePeriod->tanggal_selesai->isPast()) {
                $this->createNotification(
                    $adminUser,
                    'danger',
                    'Periode Telah Berakhir!',
                    'Periode aktif (' . $activePeriod->nama_periode . ') seharusnya sudah berakhir pada ' . $activePeriod->tanggal_selesai->isoFormat('LL') . '. Mohon perbarui status periode.',
                    route('admin.periode')
                );
            } elseif ($activePeriod->tanggal_selesai->isFuture() && $activePeriod->tanggal_selesai <= now()->addDays(7)) {
                $this->createNotification(
                    $adminUser,
                    'warning',
                    'Periode Segera Berakhir',
                    'Periode ' . $activePeriod->nama_periode . ' akan berakhir pada ' . $activePeriod->tanggal_selesai->isoFormat('LL') . ' (' . $activePeriod->tanggal_selesai->diffForHumans() . ').',
                    route('admin.periode')
                );
            } else { // Active and not ending very soon - could be an FYI notification if desired
                // Potentially add a general info notification if needed, e.g., if a new period was just activated.
                // For now, focusing on critical/warning ones.
            }
        } else {
            $this->createNotification(
                $adminUser,
                'danger',
                'Tidak Ada Periode Aktif',
                'Tidak ada periode penilaian yang aktif. Mohon atur periode.',
                route('admin.periode')
            );
        }
        // Add other admin-specific notification generation logic here if any
    }

    private function generateKepalaSekolahNotifications(User $kepalaUser)
    {
        $pendingCount = SasaranKerja::where('status', 'submitted')->count();
        if ($pendingCount > 0) {
            $this->createNotification(
                $kepalaUser,
                'warning',
                'SKP Menunggu Persetujuan',
                "Ada {$pendingCount} SKP yang menunggu persetujuan Anda.",
                route('kepala.persetujuan')
            );
        }
    }

    private function generatePegawaiNotifications(User $pegawaiUser)
    {
        $pegawai = $pegawaiUser->pegawai; // Assumes Pegawai relationship on User model
        if (!$pegawai) return;

        $activePeriod = PeriodePenilaian::where('is_active', true)->first();
        if ($activePeriod) {
            $sasaranCount = SasaranKerja::where('pegawai_id', $pegawai->id)
                ->where('periode_id', $activePeriod->id)
                ->count();
            if ($sasaranCount == 0) {
                $this->createNotification(
                    $pegawaiUser,
                    'warning',
                    'Belum Membuat Sasaran Kerja',
                    'Anda belum membuat sasaran kerja untuk periode ' . $activePeriod->nama_periode . '.',
                    route('pegawai.sasaran.create')
                );
            }
        }

        $rejectedCount = SasaranKerja::where('pegawai_id', $pegawai->id)
            ->where('status', 'rejected')
            ->count();
        if ($rejectedCount > 0) {
            $this->createNotification(
                $pegawaiUser,
                'error',
                'SKP Ditolak',
                "Ada {$rejectedCount} sasaran kerja yang ditolak. Silakan perbaiki.",
                route('pegawai.sasaran')
            );
        }
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Notification::where('user_id', $user->id)
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
