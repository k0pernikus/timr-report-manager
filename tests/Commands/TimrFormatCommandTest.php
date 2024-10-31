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

        yield 'odoo' => [
            'format:oddoo',
            file_get_contents(
                $path . 'odoo_expected.txt'
            ),
        ];

        yield 'redmine' => [
            'format:redmine',
            file_get_contents(
                $path . 'redmine_expected.txt'
            ),
        ];
    }

    #[DataProvider('dataProvider')]
    public function testTimrOverviewCommand(string $cmdName, string $expectedResult): void
    {
        $input = $this->createArgvCsvInput($cmdName, 'tests/csv/day_report/day_report.csv');
        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $actual = trim($buffered->fetch());
        $this::assertSame(
            expected: trim($expectedResult),
            actual: $actual,
            message: $cmdName,
        );
    }

    public function testItShowOfficeEntryAndExit(): void
    {
        $input = $this->createArgvCsvInput('format:oddoo', 'tests/csv/enter_and_exit/enter_and_exit.csv');
        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $path = __DIR__ . '/../../tests/csv/enter_and_exit/expected_output.txt';
        $expectedResult = trim(string: file_get_contents($path));

        $actual = trim($buffered->fetch());
        $this::assertSame(
            expected: $expectedResult,
            actual: $actual,
        );
    }

    public function testItIgnoresCaseForTicket(): void
    {
        $input = $this->createArgvCsvInput('format:redmine', 'tests/csv/tags/tickets_containing_upper_and_lower_case.csv');

        $buffered = new BufferedOutput();
        $this->timr->run($input, $buffered);

        $path = __DIR__ . '/../../tests/csv/tags/redmine_report_only_with_one_checkbox_tag.txt';
        $expectedResult = trim(string: file_get_contents($path));

        $actual = trim($buffered->fetch());
        $this::assertSame(
            expected: $expectedResult,
            actual: $actual,
        );
    }
}
