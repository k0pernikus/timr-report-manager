<?php

namespace Kopernikus\TimrReportManager\Services;

use Kopernikus\TimrReportManager\Dto\TimeEntry;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    public function testItMergesActivities()
    {
        $rootDir = __DIR__ . '/../..';
        $parser = new CsvParser($rootDir);

        $expected1 = new TimeEntry('TICKET #45', '2024-10-16 14:14', end: '2024-10-16 17:12');
        $expected2 = new TimeEntry('TICKET #45', '2024-10-16 19:30', end: '2024-10-16 22:00');

        /**
         * @var TimeEntry[] $result
         */
        $result = $parser
            ->parse('tests/csv/day_with_same_end_and_start_time.csv')
            ->toArray();

        $first = $result[0];
        $second = $result[1];

        $expectedStart1 = $expected1->start->toAtomString();
        $expectedEnd1 = $expected1->end->toAtomString();

        $actualStat1 = $first->start->toAtomString();
        $actualEnd1 = $first->end->toAtomString();

        $expectedStart2 = $expected2->start->toAtomString();
        $expectedEnd2 = $expected2->end->toAtomString();

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
            message: 'the start date must be the first possible option as the task was started then'
        );
        $this::assertSame(
            expected: $expectedEnd2,
            actual: $actualEnd2,
            message: 'the end date must be the second latest option as there was a break'
        );
    }
}
