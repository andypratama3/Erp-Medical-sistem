<?php

namespace App\Services;

use App\Models\SalesDO;
use Carbon\Carbon;

class DONumberGenerator
{
    /**
     * Generate a unique DO number based on office code and date
     * Format: DO/OFFICE_CODE/YYYY/MM/0001
     * Example: DO/JKT/2024/01/0001
     */
    public function generate(string $officeCode, string $doDate): string
    {
        $date = Carbon::parse($doDate);
        $year = $date->format('Y');
        $month = $date->format('m');

        // Find the last DO for this office in this month
        $lastDO = SalesDO::where('office_id', function ($query) use ($officeCode) {
                $query->select('id')
                    ->from('master_offices')
                    ->where('code', $officeCode);
            })
            ->whereYear('do_date', $year)
            ->whereMonth('do_date', $month)
            ->orderBy('id', 'desc')
            ->first();

        // Get sequence number
        $sequence = 1;
        if ($lastDO && $lastDO->do_code) {
            // Extract sequence from last DO code
            $parts = explode('/', $lastDO->do_code);
            if (count($parts) > 0) {
                $sequence = intval(end($parts)) + 1;
            }
        }

        return sprintf('DO/%s/%s/%s/%04d', $officeCode, $year, $month, $sequence);
    }
}
