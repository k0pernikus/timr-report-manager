<?php

namespace Kopernikus\TimrReportManager\Dto;

use Illuminate\Support\Collection;

class BitableHours
{
    public function __construct(public float $total, public readonly float $billable, public float $nonBillable)
    {
    }
}
