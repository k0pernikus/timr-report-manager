<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class RedmineFormatter extends AbstractFormatter
{
    public function __construct(private readonly int $roundUpToNearestFactor = 4)
    {
    }

    public function format(Collection $entries, string $day): void
    {
        $this->printLine();
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


        $this->printLine("Date: <info>$day</info>");
        $this->printLine("Total hours tracked:<info>{$result['total']}</info> hours");
        $this->printLine("\u{1F4A9}Non billable hours:<info>{$result['nonBillable']}</info> hours ($nonBillablePercentage%)");
        $this->printLine("\u{efc7} Billable hours:<info>{$result['billable']}</info> hours ($billablePercentage%)");
        $this->printLine('');

        if ($this->roundUpToNearestFactor >= 1) {
            $this->printLine("Rounded up to the nearest value of 1/{$this->roundUpToNearestFactor}");
        }


        $billable
            ->groupBy(fn(TimeEntry $item) => $item->ticket)
            ->each(fn(Collection $c, string $ticket) => $this->printLine("\u{F17A9} " . $ticket . ": <comment>" . Entries::getTotalHours($c, $this->roundUpToNearestFactor) . '</comment> h', 0));

        $this->printLine('');
    }
}
