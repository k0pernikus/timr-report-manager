<?php

namespace Kopernikus\TimrReportManager\Commands;

use Generator;
use Kopernikus\TimrReportManager\Base\TimeCmdTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Output\BufferedOutput;

class TimrFormatCommandTest extends TimeCmdTestCase
{
    public static function dataProvider(): Generator
    {
        yield [
            'format:oddoo', '"Date: 2024-10-18\n\nDay total as HOURS:MINUTE: 5:15\nDay total as HOURS: 5.25\n\nTicket 123 (2:41)\n09:44 - 10:06\n12:10 - 14:29\n\nPC Adminstration (0:09)\n10:12 - 10:21\n\nCall mit Heino (1:08)\n10:22 - 11:30\n\nTicket 42 (0:24)\n11:30 - 11:54\n\nMeeting Max Mustermann (0:53)\n14:29 - 15:22\n--------------------------------------------------------------------------------\n"'];

        yield ['format:redmine', '"Date: 2024-10-18\n\n\n\nTICKETING\nTicket 123:2.68\nPC Adminstration:0.15\nCall mit Heino:1.13\nTicket 42:0.4\nMeeting Max Mustermann:0.88\n--------------------------------------------------------------------------------\n"'];
    }

    #[DataProvider('dataProvider')]
    public function testTimrOverviewCommand(string $cmdName, string $expectedResult)
    {
        $input = $this->createArgvCsvInput($cmdName, 'tests/csv/day_report.csv');
        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $actual = $buffered->fetch();
        $this::assertSame(
            expected: json_decode($expectedResult),
            actual: $actual,
        );
    }
}