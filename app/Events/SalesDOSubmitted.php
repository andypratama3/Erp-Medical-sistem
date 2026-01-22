<?php

namespace App\Events;

use App\Models\SalesDO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesDOSubmitted
{
    use Dispatchable, SerializesModels;

    public SalesDO $salesDo;

    public function __construct(SalesDO $salesDo)
    {
        $this->salesDo = $salesDo;
    }
}
