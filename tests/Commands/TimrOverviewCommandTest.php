<?php

namespace Kopernikus\TimrReportManager\Commands;

use Kopernikus\TimrReportManager\Base\TimeCmdTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class TimrOverviewCommandTest extends TimeCmdTestCase
{
    public function testTimrOverviewCommand()
    {
        $input = $this->createArgvCsvInput('overview', 'tests/csv/day_report.csv');
        $buffered = new BufferedOutput();
        $exitCode = $this->timr->run($input, $buffered);

        $this::assertSame(
            expected: '2024-10: Expected 5 / Delivered 5.25',
            actual: $buffered->fetch()
        );
    }
}