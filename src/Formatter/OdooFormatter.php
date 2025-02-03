<?php

declare(strict_types=1);

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

        $this->printLine('DATE: <comment>' . $day . '</comment>', 0);

        $total = Entries::getTotalForHumans($entries);

        $this->printLine("DAY TOTAL: <info>$total</info> hours", 0);
        $this->printLine();

        $entries->each(function (TimeEntry $e) {
            $diffInMinutes = $e->getDiff($this->previous);

            if ($diffInMinutes > 1) {
                $this->printLine("< {$diffInMinutes} min break >", 2);
            }

            match (trim($e->description)) {
                'enter', 'exit' => $this->printEnteringAndExiting($e),
                default => $this->printTimeSlot($e),
            };

            $this->previous = $e;
        });

        $this->printLine("");
    }

    private function printEnteringAndExiting(TimeEntry $e): void
    {
        $time = $e->start->format('H:i');

        match ($e->description) {
            'enter' => $this->printLine('ENTERED office at: ' . $time, 1),
            'exit' => $this->printLine("EXITED office at: " . $time, 1),
            default => throw new \LogicException('Invalid TimeEntry, must be either enter or exit type'),
        };
    }

    private function printTimeSlot(TimeEntry $e): void
    {
        $time = $this->getTimeSlotMsg($e);
        $duration = $e->getDuration();
        $descriptor = $e->ticket ?? '';
        $explanation = ($e->description === 'UNCATEGORIZED'
            || strtolower($e->description) === $e->ticket) ? '' : $e->description;
        $msg = '';
        if ($descriptor !== '') {
            $msg = "[$descriptor]";
        }

        if ($explanation !== '') {
            $msg .= " $explanation";
        }
        $suffix = trim("$msg ($duration min)");
        $this->printLine("[$time] $suffix", 2);
    }
}
