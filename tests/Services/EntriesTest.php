<?php

namespace Kopernikus\TimrReportManager\Services;

use PHPUnit\Framework\TestCase;

class EntriesTest extends TestCase
{
    public function testItParesTheWorkloadOfADay()
    {
        $rootDir = __DIR__ . '/../..';
        $parser = new CsvParser($rootDir);
        $entries = $parser
            ->parse('tests/csv/ensure_total_count/report.csv');
        $getHoursAndMinutes = Entries::getTotalForHumans(collect($entries));
        $getHours = Entries::getTotalHours($entries);
        $this::assertSame('4:37', $getHoursAndMinutes);
        $this::assertSame(4.62, $getHours);
    }
}