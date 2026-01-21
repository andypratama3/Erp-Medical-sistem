<?php

namespace App\Services;

use App\Models\SalesDO;
use Carbon\Carbon;

class DONumberGenerator
{
    public function generate(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $lastDO = SalesDO::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDO ? intval(substr($lastDO->do_number, -4)) + 1 : 1;

        return sprintf('DO/%s/%s/%04d', $year, $month, $sequence);
    }
}
