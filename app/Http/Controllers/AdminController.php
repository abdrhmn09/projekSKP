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

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::count();
        $totalJabatan = Jabatan::count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        $totalPenilaian = PenilaianSkp::count();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'totalJabatan', 
            'periodeAktif',
            'totalPenilaian'
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
                'nama_lengkap' => $request->nama_lengkap,
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

    public function laporan()
    {
        $totalPegawai = Pegawai::count();
        $totalPenilaian = PenilaianSkp::count();
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        return view('admin.laporan', compact('totalPegawai', 'totalPenilaian', 'periodeAktif'));
    }
}