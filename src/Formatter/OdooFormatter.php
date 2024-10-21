<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class OdooFormatter extends AbstractFormatter
{
    private ?TimeEntry $previous = null;

    private function getTimeSlotMsg(TimeEntry $e): string
    {
        $start = $e->start->format('H:i');
        $end = $e->end->format('H:i');

        return $start . ' - ' . $end;
    }

    /**
     * @param Collection<int,TimeEntry> $entries
     */
    public function format(Collection $entries, string $day): void
    {
        $this->previous = null;

        $this->printLn('DATE: <comment>' . $day . '</comment>', 0);

        $total = Entries::getTotalForHumans($entries);

        $this->printLn("DAY TOTAL: <info>$total</info> hours", 0);
        $this->printLn();

        $entries->each(function (TimeEntry $e) {
            $diffInMinutes = $e->getDiff($this->previous);

            if ($diffInMinutes > 0) {
                $this->printLn('| ', 1);
                $this->printLn('| ' . "$diffInMinutes min break", 1);
                $this->printLn('| ', 1);
            }


            match (trim($e->description)) {
                'enter', 'exit' => $this->printEnteringAndExiting($e),
                default => $this->printTimeSlot($e),
            };

            $this->previous = $e;
        });

        $this->printLn("");
    }

    /**
     * @param Collection<int,TimeEntry> $c
     */
    private function printEnteringAndExiting(TimeEntry $e): void
    {
        $time = $e->start->format('H:i');

        match ($e->description) {
            'enter' => $this->printLn('ENTERED office at: ' . $time, 1),
            'exit' => $this->printLn("EXITED office at: " . $time, 1),
            default => throw new \LogicException('Invalid TimeEntry, must be either enter or exit type'),
        };
    }

    private function printTimeSlot(TimeEntry $e): void
    {
        $duration = $e->getDuration();
        $time = $this->getTimeSlotMsg($e);
        $this->printLn("$time ($duration min)", 1);
        $descriptor = $e->ticket ?? 'other';
        $explanation = (strtolower(trim($e->description)) === strtolower(trim($e->ticket || ''))) || $e->description === 'UNCATEGORIZED' ? '' : ": $e->description";
        $this->printLn("{$descriptor}{$explanation}", 2);
    }
}
