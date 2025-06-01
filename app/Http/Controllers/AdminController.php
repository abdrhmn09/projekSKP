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
use App\Models\SasaranKerja;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = \App\Models\Pegawai::count();
        $periodeAktif = \App\Models\PeriodePenilaian::where('is_active', true)->first();
        $skpMenunggu = \App\Models\SasaranKerja::where('status', 'submitted')->count();
        $skpSelesai = \App\Models\SasaranKerja::where('status', 'approved')->count();

        // Recent Activities
        $recentActivities = collect();

        // Sasaran kerja yang baru disubmit
        $newSasaran = \App\Models\SasaranKerja::with('pegawai.user')
            ->where('status', 'submitted')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach($newSasaran as $sasaran) {
            $recentActivities->push([
                'message' => '<strong>' . $sasaran->pegawai->user->name . '</strong> mengajukan sasaran kerja baru',
                'time' => $sasaran->created_at->diffForHumans(),
                'icon' => 'fa-file-plus',
                'color' => 'blue'
            ]);
        }

        // Sasaran kerja yang disetujui
        $approvedSasaran = \App\Models\SasaranKerja::with('pegawai.user')
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        foreach($approvedSasaran as $sasaran) {
            $recentActivities->push([
                'message' => 'Sasaran kerja <strong>' . $sasaran->pegawai->user->name . '</strong> telah disetujui',
                'time' => $sasaran->updated_at->diffForHumans(),
                'icon' => 'fa-check',
                'color' => 'green'
            ]);
        }

        $recentActivities = $recentActivities->sortByDesc('time')->take(8);

        return view('admin.dashboard', compact(
            'totalPegawai',
            'periodeAktif', 
            'skpMenunggu',
            'skpSelesai',
            'recentActivities'
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

    public function pegawaiEdit($id)
    {
        $pegawai = Pegawai::with(['user', 'jabatan'])->findOrFail($id);
        $jabatan = Jabatan::all();
        return view('admin.pegawai.edit', compact('pegawai', 'jabatan'));
    }

    public function pegawaiUpdate(Request $request, $id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pegawai->user_id,
            'nip' => 'required|string|unique:users,nip,' . $pegawai->user_id,
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
            // Update user
            $pegawai->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'nip' => $request->nip,
                'phone' => $request->phone,
                'role' => $request->role,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $pegawai->user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Update pegawai
            $pegawai->update([
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
            return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memperbarui data pegawai');
        }
    }

    public function pegawaiDestroy($id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete related records first
            PenilaianSkp::where('pegawai_id', $id)->delete();
            SasaranKerja::where('pegawai_id', $id)->delete();
            
            // Delete pegawai and user
            $pegawai->delete();
            $pegawai->user->delete();

            DB::commit();
            return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data pegawai');
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

    public function periodeEdit($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);
        return view('admin.periode.edit', compact('periode'));
    }

    public function periodeUpdate(Request $request, $id)
    {
        $periode = PeriodePenilaian::findOrFail($id);

        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'jenis_periode' => 'required|in:semester,tahunan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string'
        ]);

        $periode->update($request->all());
        return redirect()->route('admin.periode')->with('success', 'Periode penilaian berhasil diperbarui');
    }

    public function periodeDestroy($id)
    {
        $periode = PeriodePenilaian::findOrFail($id);

        // Check if there are any related records
        if ($periode->penilaianSkp()->exists() || $periode->sasaranKerja()->exists()) {
            return back()->with('error', 'Periode ini tidak dapat dihapus karena masih memiliki data penilaian atau sasaran kerja terkait');
        }

        $periode->delete();
        return redirect()->route('admin.periode')->with('success', 'Periode penilaian berhasil dihapus');
    }

    public function periodeActivate($id)
    {
        // Deactivate all periods first
        PeriodePenilaian::query()->update(['is_active' => false]);

        // Activate selected period
        PeriodePenilaian::findOrFail($id)->update(['is_active' => true]);

        return redirect()->route('admin.periode')->with('success', 'Periode berhasil diaktifkan');
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
            'kode_jabatan' => 'required|string|unique:jabatan',
            'deskripsi' => 'nullable|string',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil ditambahkan');
    }

    public function jabatanEdit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function jabatanUpdate(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'kode_jabatan' => 'required|string|unique:jabatan,kode_jabatan,' . $id,
            'deskripsi' => 'nullable|string',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        $jabatan->update($request->all());
        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil diperbarui');
    }

    public function jabatanDestroy($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        // Check if there are any related records
        if ($jabatan->pegawai()->exists()) {
            return back()->with('error', 'Jabatan ini tidak dapat dihapus karena masih digunakan oleh pegawai');
        }

        $jabatan->delete();
        return redirect()->route('admin.jabatan')->with('success', 'Data jabatan berhasil dihapus');
    }

    public function laporan()
    {
        $totalPegawai = Pegawai::count();
        
        // Get active period and all periods for filter
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $periodePenilaian = PeriodePenilaian::orderBy('tanggal_mulai', 'desc')->get();
        
        // Base query for penilaian
        $baseQuery = PenilaianSkp::query()->where('status', 'final');
        
        // Apply filters if any
        if (request('periode_id')) {
            $baseQuery->where('periode_id', request('periode_id'));
        }
        
        if (request('status')) {
            $baseQuery->where('status', request('status'));
        }
        
        // Calculate statistics
        $totalPenilaian = (clone $baseQuery)->count();
        $rataRataNilai = (clone $baseQuery)->avg('nilai_akhir') ?? 0;
        $nilaiTertinggi = (clone $baseQuery)->max('nilai_akhir') ?? 0;
        $nilaiTerendah = (clone $baseQuery)->min('nilai_akhir') ?? 0;
        
        // Get distribution
        $distribusiNilai = [
            'Sangat Baik' => (clone $baseQuery)->where('kategori_nilai', 'Sangat Baik')->count(),
            'Baik' => (clone $baseQuery)->where('kategori_nilai', 'Baik')->count(),
            'Butuh Perbaikan' => (clone $baseQuery)->where('kategori_nilai', 'Butuh Perbaikan')->count(),
            'Kurang' => (clone $baseQuery)->where('kategori_nilai', 'Kurang')->count(),
            'Sangat Kurang' => (clone $baseQuery)->where('kategori_nilai', 'Sangat Kurang')->count(),
        ];
        
        // Get monthly progress for current year
        $progressBulanan = DB::table('penilaian_skp')
            ->selectRaw('MONTH(tanggal_penilaian) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_penilaian', date('Y'))
            ->where('status', 'final')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('total', 'bulan')
            ->toArray();
            
        // Fill in missing months with zero
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($progressBulanan[$i])) {
                $progressBulanan[$i] = 0;
            }
        }
        ksort($progressBulanan);
        
        // Get paginated data with relationships
        $dataPenilaian = (clone $baseQuery)
            ->with(['pegawai.user', 'periode'])
            ->latest('tanggal_penilaian')
            ->paginate(10);
            
        return view('admin.laporan', compact(
            'totalPegawai',
            'totalPenilaian',
            'periodeAktif',
            'periodePenilaian',
            'rataRataNilai',
            'nilaiTertinggi',
            'nilaiTerendah',
            'distribusiNilai',
            'progressBulanan',
            'dataPenilaian'
        ));
    }

    public function penilaianDetail($id)
    {
        $penilaian = PenilaianSkp::with([
            'pegawai.user', 
            'periode',
            'penilai',
            'sasaranKerja.realisasiKerja'
        ])->findOrFail($id);

        return view('admin.penilaian.detail', compact('penilaian'));
    }
}