<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PeriodePenilaian;
use App\Models\PenilaianSkp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Exports\LaporanAdminExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::count();
        $totalJabatan = Jabatan::count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $totalPenilaian = PenilaianSkp::count();

        // Fetch recent activities
        $recentPegawai = Pegawai::with('user')->latest()->take(5)->get()->map(function ($item) {
            return (object) [
                'type' => 'Pegawai',
                'description' => 'Pegawai baru ' . ($item->user->name ?? 'N/A') . ' ditambahkan.',
                'time' => $item->created_at,
                'icon' => 'fa-user-plus',
                'color' => 'blue'
            ];
        });

        $recentJabatan = Jabatan::latest()->take(5)->get()->map(function ($item) {
            return (object) [
                'type' => 'Jabatan',
                'description' => 'Jabatan baru ' . $item->nama_jabatan . ' ditambahkan.',
                'time' => $item->created_at,
                'icon' => 'fa-briefcase',
                'color' => 'purple'
            ];
        });

        $recentPeriode = PeriodePenilaian::latest()->take(5)->get()->map(function ($item) {
            $action = $item->wasRecentlyCreated ? 'dibuat' : 'diperbarui';
             if ($item->is_active && !$item->wasRecentlyCreated && $item->getOriginal('is_active') == false) {
                $action = 'diaktifkan';
            }
            return (object) [
                'type' => 'Periode',
                'description' => 'Periode ' . $item->nama_periode . ' ' . $action . '.',
                'time' => $item->updated_at, // Use updated_at to capture activations as well
                'icon' => 'fa-calendar-alt',
                'color' => 'green'
            ];
        });
        
        // Combine and sort activities
        $activities = collect()
            ->merge($recentPegawai)
            ->merge($recentJabatan)
            ->merge($recentPeriode)
            ->sortByDesc('time')
            ->take(5);

        return view('admin.dashboard', compact(
            'totalPegawai',
            'totalJabatan', 
            'periodeAktif',
            'totalPenilaian',
            'activities'
        ));
    }

    // Pegawai Management
    public function pegawaiIndex()
    {
        $pegawai = Pegawai::with(['user', 'jabatan'])->paginate(10);
        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function pegawaiCreate()
    {
        $jabatan = Jabatan::all();
        return view('admin.pegawai.create', compact('jabatan'));
    }

    public function pegawaiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nip' => 'required|string|unique:users',
            'phone' => 'nullable|string',
            'role' => 'required|in:guru,staff',
            'jabatan_id' => 'required|exists:jabatan,id',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'tanggal_masuk_kerja' => 'required|date',
            'status_kepegawaian' => 'required|in:PNS,PPPK,Honorer',
            'golongan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'nip' => $request->nip,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => Hash::make('password123'), // Default password
            ]);

            // Create pegawai
            Pegawai::create([
                'user_id' => $user->id,
                'jabatan_id' => $request->jabatan_id,
                'nama_lengkap' => $request->name,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'tanggal_masuk_kerja' => $request->tanggal_masuk_kerja,
                'status_kepegawaian' => $request->status_kepegawaian,
                'golongan' => $request->golongan,
            ]);

            DB::commit();
            return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan data pegawai');
        }
    }

    public function pegawaiEdit(Pegawai $pegawai)
    {
        Log::info("[PegawaiEdit] Attempting to edit Pegawai ID: " . $pegawai->id . ", User ID from Pegawai table: " . $pegawai->user_id);

        $pegawai->loadMissing('user');

        if (is_null($pegawai->user)) {
            Log::error("[PegawaiEdit] User object is null for Pegawai ID: {$pegawai->id}. Referenced User ID in pegawai table: {$pegawai->user_id}. The user record might be missing in the 'users' table, or user_id is null in 'pegawai' table, or the relationship is not resolving.");
            
            return redirect()->route('admin.pegawai')->with('error', "Gagal membuka halaman edit. Data pegawai dengan ID {$pegawai->id} (User ID Referensi: {$pegawai->user_id}) tidak memiliki data pengguna (user) terkait. Mohon periksa integritas data atau hapus dan buat ulang data pegawai ini.");
        }

        $jabatan = Jabatan::all();
        return view('admin.pegawai.edit', compact('pegawai', 'jabatan'));
    }

    public function pegawaiUpdate(Request $request, Pegawai $pegawai)
    {
        // Ensure user exists before proceeding with validation or update
        if (is_null($pegawai->user)) {
            return redirect()->route('admin.pegawai')->with('error', "Gagal memperbarui. Data pengguna (user) untuk pegawai dengan ID {$pegawai->id} tidak ditemukan.");
        }
        $user = $pegawai->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nip' => 'required|string|unique:users,nip,' . $user->id,
            'phone' => 'nullable|string',
            'role' => 'required|in:guru,staff,kepala_sekolah,admin',
            'jabatan_id' => 'required|exists:jabatan,id',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'tanggal_masuk_kerja' => 'required|date',
            'status_kepegawaian' => 'required|in:PNS,PPPK,Honorer',
            'golongan' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'nip' => $request->nip,
                'phone' => $request->phone,
                'role' => $request->role,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $pegawaiDataToUpdate = [
                'jabatan_id' => $request->jabatan_id,
                'nama_lengkap' => $request->name, // ensure nama_lengkap in pegawai is also updated
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'tanggal_masuk_kerja' => $request->tanggal_masuk_kerja,
                'status_kepegawaian' => $request->status_kepegawaian,
                'golongan' => $request->golongan,
            ];
            
            // Update nama_lengkap in pegawai table if it exists as a column
            if (Schema::hasColumn('pegawai', 'nama_lengkap')) {
                 $pegawaiDataToUpdate['nama_lengkap'] = $request->name;
            }

            $pegawai->update($pegawaiDataToUpdate);

            DB::commit();
            return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'Gagal memperbarui data pegawai: ' . $e->getMessage());
        }
    }

    public function pegawaiDestroy(Pegawai $pegawai)
    {
        DB::beginTransaction();
        try {
            // It's possible the user doesn't exist, handle that.
            $user = $pegawai->user; 
            
            $pegawai->delete(); // Delete pegawai record first
            
            if ($user) {
                // Check if other pegawai records are linked to this user
                // This might not be necessary if one user maps to one pegawai
                $otherPegawaiExists = Pegawai::where('user_id', $user->id)->exists();
                if (!$otherPegawaiExists) {
                    $user->delete(); // Delete user only if no other pegawai records use it
                }
            }
            
            DB::commit();
            return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.pegawai')->with('error', 'Gagal menghapus data pegawai: ' . $e->getMessage());
        }
    }

    // Periode Management
    public function periodeIndex()
    {
        $periode = PeriodePenilaian::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.periode.index', compact('periode'));
    }

    public function periodeCreate()
    {
        return view('admin.periode.create');
    }

    public function periodeStore(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'jenis_periode' => 'required|in:semester,tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string'
        ]);

        PeriodePenilaian::create($request->all());
        return redirect()->route('admin.periode')->with('success', 'Periode penilaian berhasil ditambahkan');
    }

    public function periodeActivate($id)
    {
        // Deactivate all periods first
        PeriodePenilaian::query()->update(['is_active' => false]);

        // Activate selected period
        PeriodePenilaian::findOrFail($id)->update(['is_active' => true]);

        return redirect()->route('admin.periode')->with('success', 'Periode berhasil diaktifkan');
    }

    public function periodeEdit(PeriodePenilaian $periode)
    {
        return view('admin.periode.edit', compact('periode'));
    }

    public function periodeUpdate(Request $request, PeriodePenilaian $periode)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'jenis_periode' => 'required|in:semester,tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string'
        ]);

        // Prevent editing if the period is active and has related SKP or Penilaian
        if ($periode->is_active && (PenilaianSkp::where('periode_id', $periode->id)->exists() || DB::table('sasaran_kerja')->where('periode_id', $periode->id)->exists())) {
             return redirect()->route('admin.periode')->with('error', 'Periode aktif tidak dapat diubah jika sudah memiliki SKP atau Penilaian terkait.');
        }

        $periode->update($request->all());
        return redirect()->route('admin.periode')->with('success', 'Periode penilaian berhasil diperbarui');
    }

    public function periodeDestroy(PeriodePenilaian $periode)
    {
        if ($periode->is_active) {
            return redirect()->route('admin.periode')->with('error', 'Periode yang sedang aktif tidak dapat dihapus.');
        }

        // Check for related records
        $hasPenilaian = PenilaianSkp::where('periode_id', $periode->id)->exists();
        // Assuming sasaran_kerja table has periode_id, if not, adjust or remove this check
        $hasSasaranKerja = DB::table('sasaran_kerja')->where('periode_id', $periode->id)->exists(); 

        if ($hasPenilaian || $hasSasaranKerja) {
            return redirect()->route('admin.periode')->with('error', 'Tidak dapat menghapus periode karena masih memiliki Penilaian SKP atau Sasaran Kerja terkait.');
        }

        $periode->delete();
        return redirect()->route('admin.periode')->with('success', 'Periode penilaian berhasil dihapus');
    }

    // Jabatan Management
    public function jabatanIndex()
    {
        $jabatan = Jabatan::paginate(10);
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function jabatanCreate()
    {
        return view('admin.jabatan.create');
    }

    public function jabatanStore(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'kode_jabatan' => 'required|string|unique:jabatan,kode_jabatan,' . ($request->route('jabatan') ? $request->route('jabatan')->id : 'NULL') . ',id',
            'deskripsi' => 'nullable|string',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil ditambahkan');
    }

    public function jabatanEdit(Jabatan $jabatan)
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function jabatanUpdate(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'kode_jabatan' => 'required|string|unique:jabatan,kode_jabatan,' . $jabatan->id . ',id',
            'deskripsi' => 'nullable|string',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        $jabatan->update($request->all());

        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil diperbarui');
    }

    public function jabatanDestroy(Jabatan $jabatan)
    {
        // Check if any Pegawai is associated with this Jabatan
        if ($jabatan->pegawai()->exists()) {
            return redirect()->route('admin.jabatan')->with('error', 'Tidak dapat menghapus jabatan karena masih terkait dengan pegawai.');
        }
        
        $jabatan->delete();
        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil dihapus');
    }

    public function laporan(Request $request)
    {
        $totalPegawai = Pegawai::count();
        $totalPenilaianGlobal = PenilaianSkp::count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $semuaPeriode = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();

        $selectedPeriodeId = $request->input('periode_id', $periodeAktif->id ?? null);

        $penilaianQuery = PenilaianSkp::query();

        if ($selectedPeriodeId && $selectedPeriodeId !== 'semua') {
            $penilaianQuery->where('periode_id', $selectedPeriodeId);
        }

        $penilaianSKP = $penilaianQuery->with(['pegawai.user', 'pegawai.jabatan', 'periode'])->get();
        $totalPenilaianTerfilter = $penilaianSKP->count();

        $rataRataNilaiAkhir = $penilaianSKP->avg('nilai_akhir');

        $distribusiKategori = $penilaianSKP->groupBy('kategori_nilai')
            ->map->count()
            ->all(); 
            
        // Ensure all categories are present, even if count is 0
        $kategoriEnum = ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang'];
        foreach ($kategoriEnum as $kategori) {
            if (!isset($distribusiKategori[$kategori])) {
                $distribusiKategori[$kategori] = 0;
            }
        }

        // Pegawai Statistics
        $pegawaiByStatus = Pegawai::select('status_kepegawaian', DB::raw('count(*) as total'))
                                ->groupBy('status_kepegawaian')
                                ->pluck('total', 'status_kepegawaian')
                                ->all();
        $statusKepegawaianEnum = ['PNS', 'PPPK', 'Honorer'];
         foreach ($statusKepegawaianEnum as $status) {
            if (!isset($pegawaiByStatus[$status])) {
                $pegawaiByStatus[$status] = 0;
            }
        }

        $pegawaiByJabatan = Pegawai::join('jabatan', 'pegawai.jabatan_id', '=', 'jabatan.id')
                                ->select('jabatan.nama_jabatan', DB::raw('count(pegawai.id) as total'))
                                ->groupBy('jabatan.nama_jabatan')
                                ->pluck('total', 'nama_jabatan')
                                ->all();

        return view('admin.laporan', compact(
            'totalPegawai',
            'totalPenilaianGlobal',
            'periodeAktif',
            'semuaPeriode',
            'selectedPeriodeId',
            'penilaianSKP', // This will be a collection for the table
            'totalPenilaianTerfilter',
            'rataRataNilaiAkhir',
            'distribusiKategori',
            'pegawaiByStatus',
            'pegawaiByJabatan',
            'kategoriEnum' // Pass enum for chart labels
        ));
    }

    public function exportLaporan(Request $request)
    {
        $selectedPeriodeId = $request->input('periode_id', PeriodePenilaian::where('is_active', true)->first()->id ?? 'semua');
        
        $periodeInfo = 'SemuaPeriode';
        if ($selectedPeriodeId !== 'semua') {
            $periode = PeriodePenilaian::find($selectedPeriodeId);
            if ($periode) {
                $periodeInfo = preg_replace('/[^A-Za-z0-9_\\[\]-]/ ', '_', $periode->nama_periode); // Sanitize for filename
            }
        }
        $fileName = 'Laporan_SKP_Admin_' . $periodeInfo . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LaporanAdminExport($selectedPeriodeId), $fileName);
    }
}