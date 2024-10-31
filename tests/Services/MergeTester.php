<?php

namespace Kopernikus\TimrReportManager\Services;

use Kopernikus\TimrReportManager\Dto\TimeEntry;
use PHPUnit\Framework\TestCase;

class MergeTester extends TestCase
{
    /**
     * @var TimeEntry[] $timeEntries
     */
    private array $timeEntries = [];
    private EntriesMerger $entriesMerger;

    public function testItMerges(): void
    {
        $result = $this->entriesMerger->mergeEntries(collect($this->timeEntries));
        $entry = $result->first();
        $this->assertEquals(
            expected: new TimeEntry(
                description: 'TICKET #45',
                start: '2024-10-16T16:11:00.000000Z',
                end: '2024-10-16T17:12:00.000000Z'
            ),
            actual: $entry
        );

        $this->assertCount(1, $result);
    }

    protected function setUp(): void
    {
        $this->entriesMerger = new EntriesMerger();

        $this->timeEntries = [
            new TimeEntry(
                description: "TICKET #45",
                start: "2024-10-16T16:11:00.000000Z",
                end: "2024-10-16T17:12:00.000000Z",
            ),
            new TimeEntry(
                description: "TICKET #45",
                start: "2024-10-16T16:11:00.000000Z",
                end: "2024-10-16T17:12:00.000000Z",
            ),
            new TimeEntry(
                description: "TICKET #45",
                start: "2024-10-16T16:11:00.000000Z",
                end: "2024-10-16T17:12:00.000000Z",
            ),
        ];
    }
}
