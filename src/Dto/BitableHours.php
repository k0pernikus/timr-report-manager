<?php

namespace Kopernikus\TimrReportManager\Dto;

class BitableHours
{
    public function __construct(public float $total, public readonly float $billable, public float $nonBillable)
    {
    }
}
