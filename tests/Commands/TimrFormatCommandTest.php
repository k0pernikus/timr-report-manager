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
        $path = __DIR__ . '/../csv/day_report/';

        yield [
            'format:oddoo',
            trim(file_get_contents(
                $path . 'odoo_expected.txt'
            )),
        ];

        yield [
            'format:redmine',
            trim(file_get_contents(
                $path . 'redmine_expected.txt'
            )),
        ];
    }

    #[DataProvider('dataProvider')]
    public function testTimrOverviewCommand(string $cmdName, string $expectedResult)
    {
        $input = $this->createArgvCsvInput($cmdName, 'tests/csv/day_report/day_report.csv');
        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $actual = trim($buffered->fetch());
        $this::assertSame(
            expected: $expectedResult,
            actual: $actual,
        );
    }

    public function testItShowOfficeEntryAndExit()
    {
        $input = $this->createArgvCsvInput('format:oddoo', 'tests/csv/enter_and_exit/enter_and_exit.csv');
        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $path = __DIR__ . '/../../tests/csv/enter_and_exit/expected_output.txt';
        $expectedResult = trim(file_get_contents($path));

        $actual = trim($buffered->fetch());
        $this::assertSame(
            expected: $expectedResult,
            actual: $actual,
        );
    }
}
