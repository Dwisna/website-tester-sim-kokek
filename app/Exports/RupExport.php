<?php

namespace App\Exports;

use App\Models\RupRecord; // ganti sesuai model asli kamu
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RupExport implements FromQuery, WithHeadings
{
    public function __construct(protected ?string $search = null, protected ?string $tahun = null) {}

    public function query()
    {
        return RupRecord::query()
            ->when($this->search, fn ($q) => $q->where('nama_pekerjaan', 'like', "%{$this->search}%")
                ->orWhere('nama_instansi', 'like', "%{$this->search}%")
                ->orWhere('id_rup', 'like', "%{$this->search}%"))
            ->when($this->tahun, fn ($q) => $q->where('tahun_anggaran', $this->tahun));
    }

    public function headings(): array
    {
        return ['ID', 'ID RUP', 'Nama Pekerjaan', 'Pagu', 'Metode', 'Instansi', 'Tahun', 'Created At'];
    }
}