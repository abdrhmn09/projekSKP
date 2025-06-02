<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Models\PenilaianSkp;
use App\Models\PeriodePenilaian;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanAdminExport implements WithMultipleSheets
{
    protected $selectedPeriodeId;
    protected $dataPenilaian;
    protected $summaryStats;
    protected $distribusiKategori;
    protected $pegawaiByStatus;
    protected $pegawaiByJabatan;
    protected $kategoriEnum;

    public function __construct($selectedPeriodeId)
    {
        $this->selectedPeriodeId = $selectedPeriodeId;
        $this->kategoriEnum = ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang'];
        $this->prepareData();
    }

    protected function prepareData()
    {
        // Fetch data similar to AdminController@laporan
        $penilaianQuery = PenilaianSkp::query();
        if ($this->selectedPeriodeId && $this->selectedPeriodeId !== 'semua') {
            $penilaianQuery->where('periode_id', $this->selectedPeriodeId);
        }
        $this->dataPenilaian = $penilaianQuery->with(['pegawai.user', 'pegawai.jabatan', 'periode'])->get();

        $totalPegawai = Pegawai::count();
        $totalPenilaianTerfilter = $this->dataPenilaian->count();
        $rataRataNilaiAkhir = $this->dataPenilaian->avg('nilai_akhir');
        
        $periodeNama = 'Semua Periode';
        if ($this->selectedPeriodeId && $this->selectedPeriodeId !== 'semua') {
            $periode = PeriodePenilaian::find($this->selectedPeriodeId);
            if ($periode) {
                $periodeNama = $periode->nama_periode;
            }
        }

        $this->summaryStats = [
            'Filter Periode' => $periodeNama,
            'Total Pegawai (Global)' => $totalPegawai,
            'Total Penilaian (Filter)' => $totalPenilaianTerfilter,
            'Rata-rata Nilai Akhir (Filter)' => $rataRataNilaiAkhir !== null ? number_format($rataRataNilaiAkhir, 2) : 'N/A',
        ];

        $this->distribusiKategori = $this->dataPenilaian->groupBy('kategori_nilai')
            ->map->count()
            ->all();
        foreach ($this->kategoriEnum as $kategori) {
            if (!isset($this->distribusiKategori[$kategori])) {
                $this->distribusiKategori[$kategori] = 0;
            }
        }
        
        $this->pegawaiByStatus = Pegawai::select('status_kepegawaian', DB::raw('count(*) as total'))
                                ->groupBy('status_kepegawaian')
                                ->pluck('total', 'status_kepegawaian')
                                ->all();
        $statusKepegawaianEnum = ['PNS', 'PPPK', 'Honorer'];
         foreach ($statusKepegawaianEnum as $status) {
            if (!isset($this->pegawaiByStatus[$status])) {
                $this->pegawaiByStatus[$status] = 0;
            }
        }

        $this->pegawaiByJabatan = Pegawai::join('jabatan', 'pegawai.jabatan_id', '=', 'jabatan.id')
                                ->select('jabatan.nama_jabatan', DB::raw('count(pegawai.id) as total'))
                                ->groupBy('jabatan.nama_jabatan')
                                ->pluck('total', 'nama_jabatan')
                                ->all();
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new SummarySheet($this->summaryStats);
        $sheets[] = new PenilaianDetailSheet($this->dataPenilaian);
        $sheets[] = new DistribusiKategoriSheet($this->distribusiKategori, $this->kategoriEnum);
        $sheets[] = new PegawaiByStatusSheet($this->pegawaiByStatus);
        $sheets[] = new PegawaiByJabatanSheet($this->pegawaiByJabatan);
        return $sheets;
    }
}

// --- Individual Sheets ---

class SummarySheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;
    public function __construct($data)
    { 
        $this->data = collect(array_map(function($key, $value) {
            return [$key, $value];
        }, array_keys($data), array_values($data)));
    }
    public function collection() { return $this->data; }
    public function title(): string { return 'Ringkasan Laporan'; }
    public function headings(): array { return ['Item', 'Nilai']; }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:B1')->getFont()->setBold(true);
                $sheet->getStyle('A1:B'.($this->data->count()+1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}

class PenilaianDetailSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $penilaianSKP;
    public function __construct($penilaianSKP) { $this->penilaianSKP = $penilaianSKP; }
    public function title(): string { return 'Detail Penilaian SKP'; }
    public function headings(): array {
        return ['#', 'Nama Pegawai', 'NIP', 'Jabatan', 'Periode', 'Nilai Akhir', 'Kategori Nilai', 'Status Penilaian'];
    }
    public function collection()
    {
        return $this->penilaianSKP->map(function ($item, $index) {
            return [
                $index + 1,
                $item->pegawai->user->name ?? 'N/A',
                $item->pegawai->user->nip ?? 'N/A',
                $item->pegawai->jabatan->nama_jabatan ?? 'N/A',
                $item->periode->nama_periode ?? 'N/A',
                number_format($item->nilai_akhir, 2),
                $item->kategori_nilai ?? 'N/A',
                ucfirst($item->status ?? 'N/A'),
            ];
        });
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:H1')->getFont()->setBold(true);
                $sheet->getStyle('A1:H'.($this->penilaianSKP->count()+1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}

// Helper sheet class for simple key-value data
class SimpleDataSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $sheetTitle;
    protected $headerRow;

    public function __construct($data, string $sheetTitle, array $headerRow)
    {
        $this->data = collect(array_map(function($key, $value) {
            return [$key, $value];
        }, array_keys($data), array_values($data)));
        $this->sheetTitle = $sheetTitle;
        $this->headerRow = $headerRow;
    }
    public function collection() { return $this->data; }
    public function title(): string { return $this->sheetTitle; }
    public function headings(): array { return $this->headerRow; }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:B1')->getFont()->setBold(true);
                $sheet->getStyle('A1:B'.($this->data->count()+1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}

class DistribusiKategoriSheet extends SimpleDataSheet
{
    public function __construct($data, $kategoriEnum) // $kategoriEnum is not strictly needed here if data is pre-filled
    { parent::__construct($data, 'Distribusi Kategori', ['Kategori Penilaian', 'Jumlah']); }
}

class PegawaiByStatusSheet extends SimpleDataSheet
{
    public function __construct($data)
    { parent::__construct($data, 'Pegawai per Status', ['Status Kepegawaian', 'Jumlah Pegawai']); }
}

class PegawaiByJabatanSheet extends SimpleDataSheet
{
    public function __construct($data)
    { parent::__construct($data, 'Pegawai per Jabatan', ['Nama Jabatan', 'Jumlah Pegawai']); }
} 