<?php

namespace App\Exports;

use App\Models\RupRecord;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RupExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithColumnWidths,
    WithStyles,
    WithEvents
{
    protected int $rowNumber = 0;

    public function __construct(
        protected ?string $search = null,
        protected ?string $tahun = null,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $range = null
    ) {}

    public function query()
    {
        $query = RupRecord::query();

        if (!empty($this->search)) {
            $q = $this->search;

            $query->where(function ($sub) use ($q) {
                $sub->where('nama_pekerjaan', 'like', "%{$q}%")
                    ->orWhere('nama_instansi', 'like', "%{$q}%")
                    ->orWhere('id_rup', 'like', "%{$q}%");
            });
        }

        if (!empty($this->tahun)) {
            $query->where('tahun_anggaran', $this->tahun);
        }

        // Filter rentang tanggal kalender
        if (!empty($this->startDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();

            if (!empty($this->endDate)) {
                $end = Carbon::parse($this->endDate)->endOfDay();

                $query->whereBetween('created_at', [$start, $end]);
            } else {
                $query->whereBetween(
                    'created_at',
                    [$start, $start->copy()->endOfDay()]
                );
            }
        } elseif (
            !empty($this->range)
            && in_array($this->range, ['today', 'week', 'month'], true)
        ) {
            $now = now();

            if ($this->range === 'today') {
                $query->whereDate(
                    'created_at',
                    $now->toDateString()
                );
            } elseif ($this->range === 'week') {
                $query->whereBetween(
                    'created_at',
                    [
                        $now->copy()->startOfWeek(),
                        $now->copy()->endOfWeek()
                    ]
                );
            } elseif ($this->range === 'month') {
                $query->whereBetween(
                    'created_at',
                    [
                        $now->copy()->startOfMonth(),
                        $now->copy()->endOfMonth()
                    ]
                );
            }
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Header mengikuti struktur Excel SIRUP.
     */
    public function headings(): array
    {
        return [
            'No',
            'Paket',
            'Pagu (Rp)',
            'Jenis Pengadaan',
            'Produk Dalam Negeri',
            'Usaha Kecil/Koperasi',
            'Metode',
            'Pemilihan',
            'K/L/PD',
            'Satuan Kerja',
            'Lokasi',
            'ID',
        ];
    }

    /**
     * Header/data dimulai dari baris ke-3.
     *
     * Baris 1 = judul
     * Baris 2 = informasi otomatisasi SIRUP
     * Baris 3 = header
     */
    public function startCell(): string
    {
        return 'A3';
    }

    /**
     * Mapping database -> struktur Excel SIRUP.
     */
    public function map($rup): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $rup->nama_pekerjaan,
            $rup->pagu,
            $rup->nama_jenis_pengadaan,
            $rup->nama_jenis_produk_rup,
            $rup->nama_jenis_usaha,
            $rup->nama_metode_pengadaan,
            $rup->waktu_pemilihan_penyedia,
            $rup->nama_instansi,
            $rup->nama_organisasi,
            $rup->lokasi_pekerjaan,
            $rup->id_rup,
        ];
    }

    /**
     * Lebar kolom dibuat berdasarkan karakteristik data SIRUP.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 7,   // No
            'B' => 55,  // Paket
            'C' => 18,  // Pagu
            'D' => 24,  // Jenis Pengadaan
            'E' => 24,  // Produk Dalam Negeri
            'F' => 24,  // Usaha Kecil/Koperasi
            'G' => 24,  // Metode
            'H' => 20,  // Pemilihan
            'I' => 32,  // K/L/PD
            'J' => 42,  // Satuan Kerja
            'K' => 35,  // Lokasi
            'L' => 14,  // ID
        ];
    }

    /**
     * Style dasar worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            3 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    /**
     * Formatting setelah worksheet selesai dibuat.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                /*
                 * Judul seperti Excel SIRUP.
                 */
                $sheet->mergeCells('A1:L1');

                $sheet->setCellValue(
                    'A1',
                    'RUP - Cari Paket Penyedia'
                );

                $sheet->mergeCells('A2:L2');

                $sheet->setCellValue(
                    'A2',
                    'Data yang terdapat pada berkas ini dipopulasi secara otomatis oleh SiRUP pada tanggal: '
                    . now()->format('j-n-Y G:i:s')
                    . '. Untuk melihat paket yang telah terumumkan dan lebih terbaru silakan kunjungi sirup.inaproc.id'
                );

                /*
                 * Judul.
                 */
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                 * Informasi export.
                 */
                $sheet->getStyle('A2:L2')->applyFromArray([
                    'font' => [
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                /*
                 * Header tabel.
                 */
                $sheet->getStyle('A3:L3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * Data.
                 */
                $highestRow = $sheet->getHighestRow();

                if ($highestRow >= 4) {
                    $sheet->getStyle(
                        'A4:L' . $highestRow
                    )->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    /*
                     * Pagu menggunakan format angka Excel.
                     */
                    $sheet->getStyle(
                        'C4:C' . $highestRow
                    )->getNumberFormat()->setFormatCode(
                        '#,##0'
                    );

                    /*
                     * No dan ID dibuat center.
                     */
                    $sheet->getStyle(
                        'A4:A' . $highestRow
                    )->getAlignment()->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                    $sheet->getStyle(
                        'L4:L' . $highestRow
                    )->getAlignment()->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );
                }

                /*
                 * Tinggi baris.
                 */
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(32);
                $sheet->getRowDimension(3)->setRowHeight(38);

                /*
                 * Freeze header.
                 */
                $sheet->freezePane('A4');

                /*
                 * Filter.
                 */
                if ($highestRow >= 3) {
                    $sheet->setAutoFilter(
                        'A3:L' . $highestRow
                    );
                }

                /*
                 * Print setup agar lebih rapi saat dicetak.
                 */
                $sheet->getPageSetup()->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                );

                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.3);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.3);
            },
        ];
    }
}
