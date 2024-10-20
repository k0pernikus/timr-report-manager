<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class RedmineFormatter extends AbstractFormatter
{
    public function format(Collection $entries, string $day): void
    {
        $total = Entries::getTotalHours($entries);

        $this->printLn("Date: <info>$day</info>");
        $this->printLn("Total ticketable hours:<info>$total</info>");

        $grouped = $entries->groupBy(fn(TimeEntry $item) => $item->description);
        $grouped->each(
            function (Collection $collection, $group) {
                match ($group) {
                    'enter', 'exit' => null,
                    default => $this->printLn(
                        $group . ":<comment>" . Entries::getTotalHours($collection) . '</comment>',
                        1
                    ),
                };
            }
        );

        $this->printLn('');
    }
}
