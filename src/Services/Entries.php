<?php

namespace Kopernikus\TimrReportManager\Services;

use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;

class Entries
{
    /**
     * @param Collection<int, TimeEntry> $collection
     */
    public static function getTotalForHumans(Collection $collection): string
    {
        return (static::getTotal($collection))->format('%h:%I');
    }

    /**
     * @param Collection<int, TimeEntry> $collection
     */
    public static function getTotal(Collection $collection): CarbonInterval
    {
        return $collection
            ->reduce(fn(CarbonInterval $total, TimeEntry $item) => $total->addMinutes($item->getDuration()), new CarbonInterval())
            ->cascade();
    }

    /**
     * @param Collection<int, TimeEntry> $collection
     */
    public static function getTotalHours(Collection $collection, int $roundToNextFactorOf = 1): float
    {
        $total = static::getTotal($collection);

        if ($roundToNextFactorOf <= 1) {
            return round($total->totalHours, 2);
        }

        return ceil($total->totalHours * $roundToNextFactorOf) / $roundToNextFactorOf;
    }
}
