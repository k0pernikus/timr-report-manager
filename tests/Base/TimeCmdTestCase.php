<?php

namespace Kopernikus\TimrReportManager\Base;

use Kopernikus\TimrReportManager\Timr;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;

abstract class TimeCmdTestCase extends TestCase
{
    protected Timr $timr;

    public function createArgvCsvInput(string $cmd, string $file, ?string $period = null): ArgvInput
    {
        $argv = [
            'test_app', // will be stripped
            $cmd,
            "--csv",
            $file,
        ];

        if ($period) {
            $argv[] = "--period";
            $argv[] = $period;
        }

        return new ArgvInput(argv: $argv);
    }

    protected function setUp(): void
    {
        $rootDir = __DIR__ . '/../..';
        $this->timr = new Timr($rootDir);
    }
}
