<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class OdooFormatter extends AbstractFormatter
{
    /**
     * @param Collection<int,TimeEntry> $entries
     */
    public function format(Collection $entries, string $day): void
    {
        $this->printLn('DATE: <comment>' . $day . '</comment>', 0);
        $grouped = $entries->groupBy(fn(TimeEntry $item) => $item->description);

        $total = Entries::getTotalForHumans($entries);

        $this->printLn("DAY TOTAL: <info>$total</info> hours", 0);
        $this->printLn();

        $grouped->each(
            function (Collection $collection, $group) {
                match (trim($group)) {
                    'enter', 'exit' => $this->printEnteringAndExiting($collection, $group),
                    default => $this->printTimeSlot($collection, $group),
                };
            }
        );

        $this->printLn("");
    }

    /**
     * @param Collection<int,TimeEntry> $c
     */
    private function printEnteringAndExiting(Collection $c, string $group): void
    {
        $first = $c->first(); // fixme: handle multiple comings and goings
        $time = $first->start->format('H:i');

        $msg = match ($group) {
            'enter' => 'ENTERED office at: ' . $time,
            'exit' => 'EXITED office at: ' . $time,
            default => throw new \LogicException('Invalid TimeEntry, must be either enter or exit type'),
        };

        $this->printLn($msg, 1);
    }

    private function getTimeSlotMsg(TimeEntry $e): string
    {
        $start = $e->start->format('H:i');
        $end = $e->end->format('H:i');

        return $start . ' - ' . $end;
    }

    /**
     * @param Collection<int,TimeEntry> $collection
     */
    private function printTimeSlot(Collection $collection, string $group)
    {
        $sorted = $collection
            ->sortBy(fn(TimeEntry $entry) => $entry->start->valueOf(), SORT_ASC);

        $times = $sorted->map(function (TimeEntry $entry) {
            return $this->getTimeSlotMsg($entry);
        })->toArray();


        foreach ($times as $time) {
            $this->printLn($time, 1);
        }
        $this->printLn("$group (" . Entries::getTotalForHumans($collection) . ")", 2);
    }
}
