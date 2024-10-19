<?php

namespace Kopernikus\TimrReportManager\Commands;

use Kopernikus\TimrReportManager\Base\TimeCmdTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class TimrFormatCommandTest extends TimeCmdTestCase
{
    public function testTimrOverviewCommand()
    {
        $input = $this->createArgvCsvInput('format', 'tests/csv/day_report.csv');
        $buffered = new BufferedOutput();
        $exitCode = $this->timr->run($input, $buffered);

        $actual = $buffered->fetch();
        $actualContentAsJson = json_encode($actual); // use debugger to quickly fetch new assertion
        $expected = $this->whatYouAreExpectingOfMe();

        $this::assertSame(
            expected: $expected,
            actual: $actual,
        );
    }
    public function whatYouAreExpectingOfMe()
    {
        return json_decode('"Date: 2024-10-18\n\nODOO\nDay total as HOURS:MINUTE: 5:15\nDay total as HOURS: 5.25\n\nTicket 123 (2:41)\n09:44 - 10:06\n12:10 - 14:29\n\nPC Adminstration (0:09)\n10:12 - 10:21\n\nCall mit Heino (1:08)\n10:22 - 11:30\n\nTicket 42 (0:24)\n11:30 - 11:54\n\nMeeting Max Mustermann (0:53)\n14:29 - 15:22\n\n\nTICKETING\nTicket 123:2.68\nPC Adminstration:0.15\nCall mit Heino:1.13\nTicket 42:0.4\nMeeting Max Mustermann:0.88\n--------------------------------------------------------------------------------\n"');
    }
}