<?php

namespace Kopernikus\TimrReportManager\Base;

use Kopernikus\TimrReportManager\Timr;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;

abstract class TimeCmdTestCase extends TestCase
{
    protected Timr $timr;

    protected function setUp(): void
    {
        // fixme: hardcoded value
        $rootDir = '/home/philipp/github/timr-report-manager';
        $this->timr = new Timr($rootDir);
    }

    public function createArgvCsvInput(string $cmd, string $file): ArgvInput
    {
        $input = new ArgvInput(argv: [
            'test_app', // will be stripped
            $cmd,
            "--csv",
            $file,
        ]);
        return $input;
    }

}