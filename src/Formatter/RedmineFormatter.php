<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class RedmineFormatter extends AbstractFormatter
{
    public function format(Collection $entries, string $day): void
    {
        $this->printLn();
        [$nonBillable, $billable] = $entries->partition(fn(TimeEntry $e) => $e->ticket === null);

        /**
         * @var array{
         *     total: float,
         *     nonBillable: float,
         *     billable: float
         * } $result
         */
        $result = collect([
            'total' => $entries,
            'nonBillable' => $nonBillable,
            'billable' => $billable,
        ])->mapWithKeys(fn(Collection $c, $key) => [$key => Entries::getTotalHours($c)])
            ->toArray();


        $billablePercentage = round($result['billable'] / $result['total'] * 100, 2);
        $nonBillablePercentage = round(100 - $billablePercentage, 2);

        $this->printLn("Date: <info>$day</info>");
        $this->printLn("Total hours tracked:<info>{$result['total']}</info> hours");
        $this->printLn("\u{1F4A9}Non billable hours:<info>{$result['nonBillable']}</info> hours ($nonBillablePercentage%)");
        $this->printLn("\u{efc7} Billable hours:<info>{$result['billable']}</info> hours ($billablePercentage%)");
        $this->printLn('');
        $billable
            ->groupBy(fn(TimeEntry $item) => $item->ticket)
            ->each(fn(Collection $c, string $ticket) => $this->printLn("\u{F17A9} " . $ticket . ": <comment>" . Entries::getTotalHours($c) . '</comment> hours', 0));

        $this->printLn('');
    }
}
