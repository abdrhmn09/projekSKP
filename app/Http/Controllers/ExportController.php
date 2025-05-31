<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenilaianSkp;
use App\Models\Pegawai;
use App\Models\PeriodePenilaian;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportLaporan(Request $request)
    {
        $format = $request->input('format', 'excel');
        $periode_id = $request->input('periode_id');
        $pegawai_id = $request->input('pegawai_id');

        $query = PenilaianSkp::with(['pegawai.user', 'periode']);

        if ($periode_id) {
            $query->where('periode_id', $periode_id);
        }

        if ($pegawai_id) {
            $query->where('pegawai_id', $pegawai_id);
        }

        $data = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($data);
        }

        return $this->exportToExcel($data);
    }

    private function exportToExcel($data)
    {
        $filename = 'laporan_skp_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;

            public function __construct($data) {
                $this->data = $data;
            }

            public function collection() {
                return $this->data->map(function ($item) {
                    return [
                        'NIP' => $item->pegawai->user->nip,
                        'Nama' => $item->pegawai->user->name,
                        'Periode' => $item->periode->nama_periode,
                        'Nilai SKP' => $item->nilai_skp,
                        'Nilai Perilaku' => $item->nilai_perilaku,
                        'Nilai Akhir' => $item->nilai_akhir,
                        'Kategori' => $item->kategori,
                        'Status' => $item->status,
                    ];
                });
            }

            public function headings(): array {
                return ['NIP', 'Nama', 'Periode', 'Nilai SKP', 'Nilai Perilaku', 'Nilai Akhir', 'Kategori', 'Status'];
            }
        }, $filename);
    }

    private function exportToPdf($data)
    {
        $pdf = Pdf::loadView('admin.laporan-pdf', compact('data'));
        return $pdf->download('laporan_skp_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}