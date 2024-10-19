<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class OdooFormatter extends AbstractFormatter
{
    /**
     * @param Collection<TimeEntry> $entries
     */
    public function format(Collection $entries): void
    {
        $grouped = $entries->groupBy(fn(TimeEntry $item) => $item->description);
        $output = $this->output;

        $total = Entries::getTotalForHumans($entries);
        $hours = Entries::getTotalHours($entries);

        $output->writeln("Day total as HOURS:MINUTE: <info>$total</info>");
        $output->writeln("Day total as HOURS: <info>$hours</info>");

        $grouped->each(
            function (Collection $collection, $group) use ($output) {
                $sorted = $collection->sortBy(fn(TimeEntry $entry) => $entry->start->valueOf(), SORT_ASC);

                $times = $sorted->map(function (TimeEntry $entry) {
                    $start = $entry->start->format('H:i');
                    $end = $entry->end->format('H:i');

                    return $start . ' - ' . $end;
                })->toArray();
                $output->writeln('');
                $output->writeln("$group (" . Entries::getTotalForHumans($collection) . ")");

                foreach ($times as $time) {
                    $output->writeln($time);
                }
            }
        );
    }
}