<?php

namespace Kopernikus\TimrReportManager\Commands;

use Kopernikus\TimrReportManager\Base\TimeCmdTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class TimrOverviewCommandTest extends TimeCmdTestCase
{
    public function testTimrOverviewCommand(): void
    {
        $this->markTestSkipped('implement me');

        $input = $this->createArgvCsvInput('overview', 'tests/csv/day_report/day_report.csv', period: 'day');
        $buffered = new BufferedOutput();

        $exitCode = $this->timr->run($input, $buffered);
        $actual = $buffered->fetch();

        $path = __DIR__ . '/../csv/day_report/';
        $expected = file_get_contents($path . 'ticketing_overview_expected.txt');

        $this::assertSame(
            expected: $expected,
            actual: trim($buffered->fetch())
        );
    }
}
