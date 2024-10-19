<?php

namespace Kopernikus\TimrReportManager\Services;

use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;

class Entries
{
    /**
     * @param Collection<TimeEntry> $collection
     */
    public static function getTotalForHumans(Collection $collection): string
    {

        $total = static::getTotal($collection);

        return $total->format('%h:%I');

    }

    /**
     * @param Collection<TimeEntry> $collection
     */
    public static function getTotalHours(Collection $collection): float
    {
        $total = static::getTotal($collection);

        return round($total->totalHours, 2);
    }

    /**
     * @param Collection<TimeEntry> $collection
     */
    public static function getTotal(Collection $collection): CarbonInterval
    {
        return $collection
            ->reduce(fn(CarbonInterval $total, TimeEntry $item) => $total->addMinutes($item->getDuration()), new CarbonInterval())
            ->cascade();
    }
}