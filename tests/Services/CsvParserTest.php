<?php

namespace Kopernikus\TimrReportManager\Services;

use Kopernikus\TimrReportManager\Assertions\TimeEntryAssertionsTrait;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CsvParserTest extends TestCase
{
    use TimeEntryAssertionsTrait;

    protected CsvParser $csvParser;

    public function testItMergesActivities(): void
    {
        $firstExpectedEntry = new TimeEntry('TICKET #45', '2024-10-16 14:14', end: '2024-10-16 17:12');
        $secondExpectedEntry = new TimeEntry('TICKET #45', '2024-10-16 19:30', end: '2024-10-16 22:00');

        $result = $this->csvParser
            ->parse('tests/csv/day_with_same_end_and_start_time.csv')
            ->toArray();

        $this->assertMultipleEntriesAreMergedIntoTwo($result, $firstExpectedEntry, $secondExpectedEntry);
    }

    public function testItFailsOnNotesHavingMultipleTicketsIds()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Note contains multiple ticket ids: #ticket123, #ticket42; only one is allowed. Please sanitize your records.');

        $result = $this->csvParser->parse('tests/csv/note_contains_more_than_one_ticket_id/report.csv');
    }

    protected function setUp(): void
    {
        $rootDir = __DIR__ . '/../..';
        $this->csvParser = new CsvParser($rootDir);
    }
}
