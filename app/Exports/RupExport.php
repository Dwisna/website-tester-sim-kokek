<?php

namespace App\Exports;

use App\Models\RupRecord;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RupExport implements FromQuery, WithHeadings
{
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

        // Filter rentang tanggal kalender (Prioritas Utama)
        if (!empty($this->startDate)) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            if (!empty($this->endDate)) {
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } else {
                $query->whereBetween('created_at', [$start, $start->copy()->endOfDay()]);
            }
        } elseif (!empty($this->range) && in_array($this->range, ['today', 'week', 'month'], true)) {
            $now = now();
            if ($this->range === 'today') {
                $query->whereDate('created_at', $now->toDateString());
            } elseif ($this->range === 'week') {
                $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            } elseif ($this->range === 'month') {
                $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            }
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['ID', 'ID RUP', 'Nama Pekerjaan', 'Pagu', 'Metode', 'Instansi', 'Tahun', 'Created At'];
    }
}