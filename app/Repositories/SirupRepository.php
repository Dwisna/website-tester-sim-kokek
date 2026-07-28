<?php

namespace App\Repositories;

use App\Models\RupRecord;

class SirupRepository
{
    public function summary(): array
    {
        $totalPackages = RupRecord::count();
        $todayNewData = RupRecord::whereDate('created_at', today())->count();
        $latestRecord = RupRecord::orderByRaw('COALESCE(updated_at, created_at) DESC')->first();
        $totalBudget = (int) RupRecord::selectRaw(
            "COALESCE(SUM(CASE WHEN pagu IS NULL OR pagu = '' THEN 0 ELSE CAST(NULLIF(REGEXP_REPLACE(pagu, '[^0-9]', ''), '') AS UNSIGNED) END), 0) AS total_budget"
        )->value('total_budget');

        return [
            'last_update_label' => $latestRecord
                ? $latestRecord->updated_at?->format('d M Y') ?? $latestRecord->created_at?->format('d M Y')
                : now()->format('d M Y'),
            'last_update_time' => $latestRecord
                ? $latestRecord->updated_at?->format('H:i') ?? $latestRecord->created_at?->format('H:i')
                : now()->format('H:i'),
            'data_status' => $totalPackages > 0 ? 'Data Updated' : 'Awaiting Sync',
            'data_status_detail' => $totalPackages > 0 ? 'Connected to dataset' : 'No records detected',
            'total_packages' => $totalPackages,
            'total_budget' => $totalBudget,
            'total_budget_formatted' => 'Rp ' . number_format($totalBudget, 0, ',', '.'),
            'today_new_data' => $todayNewData,
            'system_status' => 'Running Normally',
        ];
    }
}