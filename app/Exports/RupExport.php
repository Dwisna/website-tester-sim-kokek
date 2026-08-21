<?php

namespace App\Exports;

use App\Models\RupRecord; // ganti sesuai model asli kamu
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class RupExport implements FromQuery, WithHeadings
{
    public function __construct(
        protected ?string $search = null,
        protected ?string $tahun = null,
        protected ?string $range = null,
        protected ?string $startDate = null,
        protected ?string $endDate = null,
    ) {}

    public function query()
    {
        $query = RupRecord::query()
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_pekerjaan', 'like', "%{$search}%")
                        ->orWhere('nama_instansi', 'like', "%{$search}%")
                        ->orWhere('id_rup', 'like', "%{$search}%");
                });
            })
            ->when($this->tahun, fn ($q) => $q->where('tahun_anggaran', $this->tahun));

        // Filter rentang tanggal custom dari date-picker (prioritas di atas 'range')
        if ($this->startDate) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();

            if ($this->endDate) {
                $endDate = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                $query->whereBetween('created_at', [$startDate, $startDate->copy()->endOfDay()]);
            }
        } elseif (in_array($this->range, ['today', 'week', 'month'], true)) {
            // Filter cepat: hari ini / minggu ini / bulan ini
            $now = now();

            if ($this->range === 'today') {
                $query->whereDate('created_at', $now->toDateString());
            } elseif ($this->range === 'week') {
                $query->whereBetween('created_at', [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek(),
                ]);
            } elseif ($this->range === 'month') {
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ]);
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return ['ID', 'ID RUP', 'Nama Pekerjaan', 'Pagu', 'Metode', 'Instansi', 'Tahun', 'Created At'];
    }
}