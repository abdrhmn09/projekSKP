<?php

namespace App\Http\Controllers;

use App\Models\SasaranKerja;
use App\Models\RealisasiKerja;
use App\Models\PenilaianSkp;
use App\Models\PeriodePenilaian;
use App\Models\RencanaTindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    public function dashboard()
    {
        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        $totalSasaran = 0;
        $sasaranApprovedCount = 0;
        $statusPenilaian = 'Belum dimulai';
        $progressRealisasi = 0;
        $nilaiSKP = '-';
        $kategoriNilai = 'Belum dinilai';
        $sasaranKerjaDetails = collect();
        $deadlineSasaran = '-';
        $deadlineRealisasi = '-';

        if ($pegawai && $periodeAktif) {
            $totalSasaran = SasaranKerja::forPegawai($pegawai->id)
                ->forPeriode($periodeAktif->id)
                ->count();

            $sasaranApproved = SasaranKerja::forPegawai($pegawai->id)
                ->forPeriode($periodeAktif->id)
                ->byStatus('approved')
                ->get();
            $sasaranApprovedCount = $sasaranApproved->count();

            $penilaian = PenilaianSkp::forPegawai($pegawai->id)
                ->forPeriode($periodeAktif->id)
                ->first();

            if ($penilaian) {
                $statusPenilaian = ucfirst($penilaian->status);
                $nilaiSKP = $penilaian->nilai_akhir ?? '-';
                $kategoriNilai = $penilaian->kategori_nilai ?? 'Belum dinilai';
            }

            if ($sasaranApprovedCount > 0) {
                $sasaranDenganRealisasiCount = RealisasiKerja::whereHas('sasaranKerja', function ($query) use ($pegawai, $periodeAktif) {
                    $query->forPegawai($pegawai->id)
                          ->forPeriode($periodeAktif->id)
                          ->byStatus('approved');
                })->count();
                $progressRealisasi = ($sasaranDenganRealisasiCount / $sasaranApprovedCount) * 100;
            }

            $sasaranKerjaDetails = SasaranKerja::with('realisasiKerja')
                ->forPegawai($pegawai->id)
                ->forPeriode($periodeAktif->id)
                ->get()
                ->map(function ($sasaran) {
                    $progress = 0.0; // Initialize as float
                    $realisasiItem = $sasaran->realisasiKerja;

                    // If realisasiKerja relationship returns a collection, get the first item.
                    // This handles cases where the relationship might be hasMany but logically one-to-one for this context.
                    if ($realisasiItem instanceof \Illuminate\Database\Eloquent\Collection) {
                        $realisasiItem = $realisasiItem->first();
                    }

                    // Now $realisasiItem should be a single RealisasiKerja model instance or null.
                    // Also ensure target_kuantitas is valid for division.
                    if ($realisasiItem && $sasaran->target_kuantitas) { // Check if target_kuantitas is not null/empty
                        $targetKuantitasValue = floatval($sasaran->target_kuantitas);

                        if ($targetKuantitasValue > 0) { // Ensure target_kuantitas is numeric and positive
                            $realisasiKuantitasValue = 0.0;
                            // Check if realisasi_kuantitas exists and is numeric on the model instance
                            if (isset($realisasiItem->realisasi_kuantitas) && is_numeric($realisasiItem->realisasi_kuantitas)) {
                                $realisasiKuantitasValue = floatval($realisasiItem->realisasi_kuantitas);
                            }
                            $progress = ($realisasiKuantitasValue / $targetKuantitasValue) * 100;
                        }
                    }
                    $sasaran->progress = round($progress, 2);
                    return $sasaran;
                });

            $deadlineSasaran = $periodeAktif->tanggal_selesai->format('d M Y');
            $deadlineRealisasi = $periodeAktif->tanggal_selesai->format('d M Y');
        }

        return view('pegawai.dashboard', compact(
            'totalSasaran',
            'sasaranApprovedCount',
            'statusPenilaian',
            'periodeAktif',
            'progressRealisasi',
            'nilaiSKP',
            'kategoriNilai',
            'sasaranKerjaDetails',
            'deadlineSasaran',
            'deadlineRealisasi'
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
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->route('pegawai.sasaran')->with('error', 'Tidak ada periode penilaian yang aktif saat ini. Silakan hubungi Admin.');
        }

        return view('pegawai.sasaran.create', compact('periodeAktif'));
    }

    public function sasaranStore(Request $request)
    {
        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();

        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode penilaian yang aktif untuk membuat sasaran. Silakan hubungi Admin.')->withInput();
        }

        $request->validate([
            'kode_sasaran' => 'required|string|max:255',
            'uraian_kegiatan' => 'required|string',
            'target_kuantitas' => 'required|numeric|min:0',
            'satuan_kuantitas' => 'required|string|max:50',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0',
            'bobot_persen' => 'required|numeric|min:0.01|max:100',
            'status' => 'required|in:draft,submitted'
        ]);

        SasaranKerja::create([
            'pegawai_id' => $pegawai->id,
            'periode_id' => $periodeAktif->id,
            'indikator_kinerja' => $request->kode_sasaran,
            'uraian_sasaran' => $request->uraian_kegiatan,
            'target_kuantitas' => $request->target_kuantitas,
            'satuan_kuantitas' => $request->satuan_kuantitas,
            'target_kualitas' => $request->target_kualitas,
            'target_waktu' => $request->target_waktu,
            'target_biaya' => $request->target_biaya,
            'bobot_persen' => $request->bobot_persen,
            'status' => $request->status,
        ]);

        $message = $request->status == 'submitted' ? 'Sasaran kerja berhasil diajukan.' : 'Sasaran kerja berhasil disimpan sebagai draft.';
        return redirect()->route('pegawai.sasaran')->with('success', $message);
    }

    public function sasaranEdit(SasaranKerja $sasaran)
    {
        $pegawai = auth()->user()->pegawai;

        // Logging sementara
        Log::info('PegawaiController@sasaranEdit - Attempting to edit sasaran', [
            'sasaran_id' => $sasaran->id,
            'sasaran_pegawai_id' => $sasaran->pegawai_id,
            'auth_user_id' => auth()->id(),
            'auth_pegawai_id' => $pegawai ? $pegawai->id : null,
            'auth_pegawai_name' => $pegawai && $pegawai->user ? $pegawai->user->name : 'N/A'
        ]);

        // Pastikan sasaran kerja ini milik pegawai yang login
        if (!$pegawai || $sasaran->pegawai_id !== $pegawai->id) {
            Log::warning('PegawaiController@sasaranEdit - Authorization failed', [
                'sasaran_id' => $sasaran->id,
                'expected_pegawai_id' => $sasaran->pegawai_id,
                'actual_pegawai_id' => $pegawai ? $pegawai->id : 'Pegawai data not found for authenticated user'
            ]);
            return redirect()->route('pegawai.sasaran')->with('error', 'Anda tidak memiliki hak untuk mengedit sasaran kerja ini.');
        }

        // Hanya sasaran dengan status draft atau rejected yang boleh diedit
        if (!in_array($sasaran->status, ['draft', 'rejected'])) {
            return redirect()->route('pegawai.sasaran')->with('error', 'Sasaran kerja dengan status ini tidak dapat diedit.');
        }
        
        $periodeAktif = $sasaran->periode; // Menggunakan periode dari sasaran yang diedit

        return view('pegawai.sasaran.edit', compact('sasaran', 'periodeAktif'));
    }

    public function sasaranUpdate(Request $request, SasaranKerja $sasaran)
    {
        $pegawai = auth()->user()->pegawai;

        if ($sasaran->pegawai_id !== $pegawai->id) {
            return redirect()->route('pegawai.sasaran')->with('error', 'Anda tidak memiliki hak untuk mengupdate sasaran kerja ini.');
        }

        if (!in_array($sasaran->status, ['draft', 'rejected'])) {
            return redirect()->route('pegawai.sasaran')->with('error', 'Sasaran kerja dengan status ini tidak dapat diupdate.');
        }

        $request->validate([
            'kode_sasaran' => 'required|string|max:255',
            'uraian_kegiatan' => 'required|string',
            'target_kuantitas' => 'required|numeric|min:0',
            'satuan_kuantitas' => 'required|string|max:50',
            'target_kualitas' => 'required|numeric|min:0|max:100',
            'target_waktu' => 'required|date',
            'target_biaya' => 'nullable|numeric|min:0',
            'bobot_persen' => 'required|numeric|min:0.01|max:100',
            'status' => 'required|in:draft,submitted'
        ]);

        $sasaran->update([
            'indikator_kinerja' => $request->kode_sasaran,
            'uraian_sasaran' => $request->uraian_kegiatan,
            'target_kuantitas' => $request->target_kuantitas,
            'satuan_kuantitas' => $request->satuan_kuantitas,
            'target_kualitas' => $request->target_kualitas,
            'target_waktu' => $request->target_waktu,
            'target_biaya' => $request->target_biaya,
            'bobot_persen' => $request->bobot_persen,
            'status' => $request->status,
        ]);

        $message = $request->status == 'submitted' ? 'Sasaran kerja berhasil diajukan setelah diupdate.' : 'Sasaran kerja berhasil diupdate sebagai draft.';
        return redirect()->route('pegawai.sasaran')->with('success', $message);
    }

    public function sasaranShow(SasaranKerja $sasaran)
    {
        $pegawai = auth()->user()->pegawai;

        // Pastikan sasaran kerja ini milik pegawai yang login
        if (!$pegawai || $sasaran->pegawai_id !== $pegawai->id) {
            Log::warning('PegawaiController@sasaranShow - Authorization failed', [
                'sasaran_id' => $sasaran->id,
                'expected_pegawai_id' => $sasaran->pegawai_id,
                'actual_pegawai_id' => $pegawai ? $pegawai->id : 'Pegawai data not found for authenticated user'
            ]);
            return redirect()->route('pegawai.sasaran')->with('error', 'Anda tidak memiliki hak untuk melihat detail sasaran kerja ini.');
        }

        // Logging sementara untuk memastikan data sasaran terload dengan benar
        Log::info('PegawaiController@sasaranShow - Displaying sasaran detail', [
            'sasaran_id' => $sasaran->id,
            'sasaran_pegawai_id' => $sasaran->pegawai_id,
            'auth_pegawai_id' => $pegawai->id
        ]);

        return view('pegawai.sasaran.show', compact('sasaran'));
    }

    // Realisasi Kerja
    public function realisasiIndex()
    {
        $pegawai = auth()->user()->pegawai;
        $realisasi = RealisasiKerja::whereHas('sasaranKerja', function($query) use ($pegawai) {
            $query->forPegawai($pegawai->id);
        })->with('sasaranKerja.periode')->paginate(10);

        return view('pegawai.realisasi.index', compact('realisasi'));
    }

    public function realisasiCreate()
    {
        $pegawai = auth()->user()->pegawai;
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        $sasaranApprovedQuery = SasaranKerja::forPegawai($pegawai->id)->byStatus('approved');

        if ($periodeAktif) {
            $sasaranApprovedQuery->forPeriode($periodeAktif->id);
        }

        $sasaranApproved = $sasaranApprovedQuery->doesntHave('realisasiKerja')
                            ->orderBy('uraian_sasaran')
            ->get();

        if ($sasaranApproved->isEmpty()) {
            $message = $periodeAktif ? 
                'Tidak ada sasaran kerja yang disetujui pada periode aktif dan dapat diisi realisasinya saat ini.' :
                'Tidak ada sasaran kerja yang disetujui dan dapat diisi realisasinya saat ini (Tidak ada periode aktif).';
            return redirect()->route('pegawai.realisasi')->with('info', $message);
        }

        return view('pegawai.realisasi.create', compact('sasaranApproved', 'periodeAktif'));
    }

    public function realisasiStore(Request $request)
    {
        Log::info('PegawaiController@realisasiStore: Method dipanggil.');
        Log::info('Request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'sasaran_kerja_id' => 'required|exists:sasaran_kerja,id',
            'uraian_realisasi' => 'required|string',
            'realisasi_kuantitas' => 'required|integer|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096', // Maks 4MB
        ]);

        if ($validator->fails()) {
            Log::warning('PegawaiController@realisasiStore: Validasi gagal.', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Log::info('PegawaiController@realisasiStore: Validasi berhasil.');

        $pegawai = auth()->user()->pegawai;
        Log::info('Pegawai data:', ['id' => $pegawai ? $pegawai->id : null]);

        $sasaranKerja = SasaranKerja::where('id', $request->sasaran_kerja_id)
                                    ->forPegawai($pegawai->id)
                                    ->byStatus('approved')
                                    ->first();

        if (!$sasaranKerja) {
            Log::warning('PegawaiController@realisasiStore: Sasaran kerja tidak valid atau tidak dapat diisi realisasinya.');
            return redirect()->back()->with('error', 'Sasaran kerja tidak valid atau tidak dapat diisi realisasinya.')->withInput();
        }
        Log::info('PegawaiController@realisasiStore: Sasaran kerja ditemukan.', ['id' => $sasaranKerja->id]);
        
        if (RealisasiKerja::where('sasaran_kerja_id', $request->sasaran_kerja_id)->exists()) {
            Log::warning('PegawaiController@realisasiStore: Realisasi untuk sasaran kerja ini sudah pernah diinput.');
            return redirect()->back()->with('error', 'Realisasi untuk sasaran kerja ini sudah pernah diinput.')->withInput();
        }
        Log::info('PegawaiController@realisasiStore: Pengecekan realisasi duplikat lolos.');

        $data = $request->except('bukti_dukung'); // Ambil semua kecuali bukti_dukung dulu

        if ($request->hasFile('bukti_dukung')) {
            Log::info('PegawaiController@realisasiStore: Request memiliki file bukti_dukung.');
            $file = $request->file('bukti_dukung');
            Log::info('File details:', ['original_name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'mime_type' => $file->getMimeType(), 'is_valid' => $file->isValid()]);
            
            if (!$file->isValid()) {
                Log::error('PegawaiController@realisasiStore: File bukti_dukung tidak valid.');
                return redirect()->back()->with('error', 'File bukti dukung tidak valid.')->withInput();
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $pathTarget = 'public/bukti_dukung_realisasi';
            Log::info('PegawaiController@realisasiStore: Mencoba menyimpan file.', ['filename' => $filename, 'target_path' => $pathTarget]);

            try {
                // Coba simpan file
                $path = $file->storeAs($pathTarget, $filename);
                Log::info('PegawaiController@realisasiStore: File BERHASIL disimpan.', ['path' => $path]);
                $data['bukti_dukung'] = 'bukti_dukung_realisasi/' . $filename;
            } catch (Exception $e) {
                Log::error('PegawaiController@realisasiStore: GAGAL menyimpan file.', [
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString() // Ini akan sangat panjang, tapi detail
                ]);
                return redirect()->back()->with('error', 'Gagal menyimpan file bukti dukung: ' . $e->getMessage())->withInput();
            }
        } else {
            Log::info('PegawaiController@realisasiStore: Request TIDAK memiliki file bukti_dukung.');
            $data['bukti_dukung'] = null;
        }
        
        Log::info('PegawaiController@realisasiStore: Data yang akan dibuat untuk RealisasiKerja:', $data);
        try {
            RealisasiKerja::create($data);
            Log::info('PegawaiController@realisasiStore: RealisasiKerja BERHASIL dibuat.');
        } catch (Exception $e) {
            Log::error('PegawaiController@realisasiStore: GAGAL membuat RealisasiKerja.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan data realisasi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil diinput');
    }

    public function realisasiEdit(RealisasiKerja $realisasi)
    {
        $pegawai = auth()->user()->pegawai;

        // Pastikan realisasi ini milik pegawai yang login
        if (!$pegawai || $realisasi->sasaranKerja->pegawai_id !== $pegawai->id) {
            return redirect()->route('pegawai.realisasi')->with('error', 'Anda tidak memiliki hak untuk mengedit realisasi ini.');
        }

        // Pastikan realisasi ini belum dinilai atau difinalisasi
        // Tambahkan logika ini jika diperlukan, misalnya:
        // if ($realisasi->penilaianSkp && $realisasi->penilaianSkp->status == 'final') {
        //     return redirect()->route('pegawai.realisasi')->with('error', 'Realisasi yang sudah dinilai final tidak dapat diedit.');
        // }

        return view('pegawai.realisasi.edit', compact('realisasi'));
    }

    public function realisasiUpdate(Request $request, RealisasiKerja $realisasi)
    {
        $pegawai = auth()->user()->pegawai;

        if (!$pegawai || $realisasi->sasaranKerja->pegawai_id !== $pegawai->id) {
            return redirect()->route('pegawai.realisasi')->with('error', 'Anda tidak memiliki hak untuk mengupdate realisasi ini.');
        }

        // Validasi, mirip dengan store, tapi bukti_dukung boleh kosong (jika tidak diganti)
        $request->validate([
            // sasaran_kerja_id tidak perlu diubah saat edit, jadi tidak divalidasi lagi dari request
            'uraian_realisasi' => 'required|string',
            'realisasi_kuantitas' => 'required|integer|min:0',
            'realisasi_kualitas' => 'required|numeric|min:0|max:100',
            'realisasi_waktu' => 'required|date',
            'realisasi_biaya' => 'nullable|numeric|min:0',
            'bukti_dukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:4096', // Maks 4MB
        ]);

        $data = $request->only([
            'uraian_realisasi', 'realisasi_kuantitas', 'realisasi_kualitas', 
            'realisasi_waktu', 'realisasi_biaya'
        ]);

        if ($request->hasFile('bukti_dukung')) {
            $file = $request->file('bukti_dukung');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/bukti_dukung_realisasi', $filename);
            
            // Hapus file lama jika ada dan berhasil menyimpan file baru
            if ($realisasi->bukti_dukung) {
                Storage::delete('public/' . $realisasi->bukti_dukung);
            }
            $data['bukti_dukung'] = 'bukti_dukung_realisasi/' . $filename;
        }
        // Jika tidak ada file baru diunggah, $data['bukti_dukung'] tidak akan diset,
        // sehingga nilai lama di database tidak akan tertimpa oleh null.

        $realisasi->update($data);

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil diupdate.');
    }

    public function realisasiDestroy(RealisasiKerja $realisasi)
    {
        $pegawai = auth()->user()->pegawai;

        // Otorisasi: Pastikan realisasi ini milik pegawai yang login
        if (!$pegawai || $realisasi->sasaranKerja->pegawai_id !== $pegawai->id) {
            return redirect()->route('pegawai.realisasi')->with('error', 'Anda tidak memiliki hak untuk menghapus realisasi ini.');
        }

        // Hapus file bukti dukung dari storage jika ada
        if ($realisasi->bukti_dukung) {
            Storage::delete('public/' . $realisasi->bukti_dukung);
        }

        // Hapus record realisasi dari database
        $realisasi->delete();

        return redirect()->route('pegawai.realisasi')->with('success', 'Realisasi kerja berhasil dihapus.');
    }

    public function showBuktiDukungRealisasi($filename)
    {
        $pegawai = auth()->user()->pegawai;

        // Path lengkap ke file di dalam storage
        $filePath = 'public/bukti_dukung_realisasi/' . $filename;

        // 1. Periksa apakah file ada
        if (!Storage::exists($filePath)) {
            Log::warning('PegawaiController@showBuktiDukungRealisasi: File tidak ditemukan.', ['path' => $filePath]);
            abort(404, 'File tidak ditemukan.');
        }

        // 2. Otorisasi (CONTOH SEDERHANA - PERLU DISESUAIKAN)
        //    Kita perlu cara untuk memastikan bahwa file ini milik realisasi yang boleh diakses oleh pegawai ini.
        //    Untuk saat ini, kita akan mencari realisasi yang memiliki bukti dukung dengan nama file ini
        //    dan terkait dengan pegawai yang login.
        $realisasi = RealisasiKerja::where('bukti_dukung', 'bukti_dukung_realisasi/' . $filename)
                                  ->whereHas('sasaranKerja', function($query) use ($pegawai) {
                                      $query->where('pegawai_id', $pegawai->id);
                                  })
                                  ->first();

        if (!$realisasi) {
            Log::warning('PegawaiController@showBuktiDukungRealisasi: Otorisasi gagal atau file tidak terkait dengan realisasi pegawai.', [
                'filename' => $filename,
                'pegawai_id' => $pegawai->id
            ]);
            abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
        }

        Log::info('PegawaiController@showBuktiDukungRealisasi: Akses file diizinkan.', ['filename' => $filename, 'pegawai_id' => $pegawai->id]);
        // 3. Kembalikan file sebagai respons
        //    Storage::path() akan memberikan path absolut ke file.
        return response()->file(Storage::path($filePath));
    }

    public function penilaian()
    {
        $pegawai = auth()->user()->pegawai;
        $penilaian = PenilaianSkp::forPegawai($pegawai->id)
            ->with(['periode', 'penilai'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pegawai.penilaian', compact('penilaian'));
    }

    public function rencanaIndex()
    {
        $pegawai = auth()->user()->pegawai;
        $rencana = RencanaTindakLanjut::whereHas('penilaianSkp', function($query) use ($pegawai) {
            $query->forPegawai($pegawai->id);
        })->with('penilaianSkp.periode')->paginate(10);

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

        $pegawai = auth()->user()->pegawai;
        $penilaianSkp = PenilaianSkp::where('id', $request->penilaian_skp_id)
                                    ->forPegawai($pegawai->id)
                                    ->first();
        if (!$penilaianSkp) {
            return redirect()->back()->with('error', 'Penilaian SKP tidak valid.')->withInput();
        }

        RencanaTindakLanjut::create($request->all());

        return redirect()->route('pegawai.rencana')->with('success', 'Rencana tindak lanjut berhasil disimpan');
    }
}
