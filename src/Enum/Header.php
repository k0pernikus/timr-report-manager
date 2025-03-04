<?php

declare(strict_types=1);

namespace Kopernikus\TimrReportManager\Enum;

enum Header: string
{
    case Note = "Notes";
    case StartDate = 'Start';
    case EndDate = 'End';
}
