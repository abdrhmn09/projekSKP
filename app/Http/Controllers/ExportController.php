
<?php

namespace App\Http\Controllers;

use App\Models\PenilaianSkp;
use App\Models\SasaranKerja;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportLaporan(Request $request)
    {
        $periode_id = $request->get('periode_id');
        $status = $request->get('status');

        $query = PenilaianSkp::with(['pegawai.user', 'periode']);

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->get();

        $csv = "Nama,NIP,Periode,Nilai SKP,Kategori,Status,Tanggal Penilaian\n";

        foreach ($data as $item) {
            $csv .= sprintf(
                "%s,%s,%s,%.2f,%s,%s,%s\n",
                $item->pegawai->user->name,
                $item->pegawai->user->nip,
                $item->periode->nama_periode,
                $item->nilai_akhir,
                $item->kategori_nilai,
                $item->status,
                $item->created_at->format('d/m/Y')
            );
        }

        $filename = 'laporan_skp_' . date('Y-m-d') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportSasaran(Request $request)
    {
        $periode_id = $request->get('periode_id');
        $status = $request->get('status');

        $query = SasaranKerja::with(['pegawai.user', 'periode']);

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->get();

        $csv = "Nama,NIP,Periode,Uraian Sasaran,Target Kuantitas,Target Kualitas,Status\n";

        foreach ($data as $item) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%d %s,%.2f%%,%s\n",
                $item->pegawai->user->name,
                $item->pegawai->user->nip,
                $item->periode->nama_periode,
                $item->uraian_sasaran,
                $item->target_kuantitas,
                $item->satuan_kuantitas,
                $item->target_kualitas,
                $item->status
            );
        }

        $filename = 'sasaran_kerja_' . date('Y-m-d') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPegawai()
    {
        $data = Pegawai::with(['user', 'jabatan'])->get();

        $csv = "Nama,NIP,Email,Jabatan,Status Kepegawaian,Golongan,Tanggal Masuk\n";

        foreach ($data as $item) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $item->user->name,
                $item->user->nip,
                $item->user->email,
                $item->jabatan->nama_jabatan,
                $item->status_kepegawaian,
                $item->golongan ?? '-',
                $item->tanggal_masuk_kerja
            );
        }

        $filename = 'data_pegawai_' . date('Y-m-d') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
