<?php

declare(strict_types=1);

namespace Kopernikus\TimrReportManager\Assertions;

use Kopernikus\TimrReportManager\Dto\TimeEntry;
use PHPUnit\Framework\TestCase;

trait TimeEntryAssertionsTrait
{
    /**
     * @param TimeEntry[] $actualTimeEntries
     */
    public function assertMultipleEntriesAreMergedIntoTwo(array $actualTimeEntries, TimeEntry $firstExpected, TimeEntry $secondExpected): void
    {
        $this::assertCount(2, $actualTimeEntries, 'it should only contain two items');
        [$first, $second] = $actualTimeEntries;

        $expectedStart1 = $firstExpected->start->toAtomString();
        $expectedEnd1 = $firstExpected->end->toAtomString();

        $actualStat1 = $first->start->toAtomString();
        $actualEnd1 = $first->end->toAtomString();

        $expectedStart2 = $secondExpected->start->toAtomString();
        $expectedEnd2 = $secondExpected->end->toAtomString();

        $actualStat2 = $second->start->toAtomString();
        $actualEnd2 = $second->end->toAtomString();

        $this::assertSame(
            expected: $expectedStart1,
            actual: $actualStat1,
            message: 'the start date must be the first possible option as the task was started then'
        );
        $this::assertSame(
            expected: $expectedEnd1,
            actual: $actualEnd1,
            message: 'the end date must be the second latest option as there was a break'
        );

        $this::assertSame(
            expected: $expectedStart2,
            actual: $actualStat2,
            message: 'the start date must be the second possible option as the task was started after a break'
        );
        $this::assertSame(
            expected: $expectedEnd2,
            actual: $actualEnd2,
            message: 'the end date must be the latest option'
        );

        static::assertTimeEntryHasTicketId($first, 45);
        static::assertTimeEntryHasTicketId($second, 45);
    }

    public static function assertTimeEntryHasTicketId(TimeEntry $timeEntry, int $ticketNumber)
    {
        $ticketNumberHashtag = '#' . (string)$ticketNumber;
        $count = substr_count($timeEntry->description, '#');
        TestCase::assertSame(1, $count, 'the time entry must only contain one hashtag for the ticket id');
        TestCase::assertSame($timeEntry->ticket, $ticketNumberHashtag);
    }
}
