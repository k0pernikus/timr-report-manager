<?php

namespace Kopernikus\TimrReportManager\Services;

use Kopernikus\TimrReportManager\Dto\TimeEntry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CsvParserTest extends TestCase
{
    protected CsvParser $csvParser;

    protected function setUp(): void
    {
        $rootDir = __DIR__ . '/../..';
        $this->csvParser = new CsvParser($rootDir);
    }

    public function testItMergesActivities(): void
    {
        $fistWorkSlot = new TimeEntry('TICKET #45', '2024-10-16 14:14', end: '2024-10-16 17:12');
        $secondWorksSlot = new TimeEntry('TICKET #45', '2024-10-16 19:30', end: '2024-10-16 22:00');

        /**
         * @var TimeEntry[] $result
         */
        $result = $this->csvParser
            ->parse('tests/csv/day_with_same_end_and_start_time.csv')
            ->toArray();

        $first = $result[0];
        $second = $result[1];

        $expectedStart1 = $fistWorkSlot->start->toAtomString();
        $expectedEnd1 = $fistWorkSlot->end->toAtomString();

        $actualStat1 = $first->start->toAtomString();
        $actualEnd1 = $first->end->toAtomString();

        $expectedStart2 = $secondWorksSlot->start->toAtomString();
        $expectedEnd2 = $secondWorksSlot->end->toAtomString();

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

        $this::assertCount(2, $result, 'it should only contain two items');
    }

    public function testItFailsOnNotesHavingMultipleTicketsIds()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Note contains multiple ticket ids: #ticket123, #ticket42; only one is allowed. Please sanitize your records.');

        $result = $this->csvParser->parse('tests/csv/note_contains_more_than_one_ticket_id/report.csv');
    }
}
