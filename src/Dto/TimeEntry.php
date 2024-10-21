<?php

namespace Kopernikus\TimrReportManager\Dto;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

class TimeEntry
{
    public readonly string $description;
    public readonly ?string $ticket;
    public readonly CarbonImmutable $start;
    public readonly CarbonImmutable $end;

    public function __construct(
        string $description,
        string $start,
        string $end
    )
    {
        $description = trim($description);
        $this->description = empty($description) ? 'UNCATEGORIZED' : trim($description);
        $this->ticket = $this->getTicket($description);


        $this->start = Carbon::parse($start)->toImmutable();
        $this->end = Carbon::parse($end)->toImmutable();
    }

    private function getTicket($description): ?string
    {
        if (preg_match("/#(\w+)/", $description, $matches)) {
            return trim('#' . strtolower($matches[1]));
        } else {
            return null;
        }
    }

    public function getDuration(string $unit = 'minutes'): int
    {
        return match ($unit) {
            'minutes' => (int)$this->start->diffInMinutes($this->end),
            default => throw new \LogicException('invalid unit'),
        };
    }

    public function getDiff(?TimeEntry $previous): int
    {
        if ($previous === null || $previous === $this) {
            return 0;
        }

        return $previous->end->diffInMinutes($this->start);
    }
}
