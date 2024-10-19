<?php

namespace Kopernikus\TimrReportManager;

use Kopernikus\TimrReportManager\Commands\TimrOverviewCommand;
use Kopernikus\TimrReportManager\Commands\TimrReportCommand;
use Kopernikus\TimrReportManager\Formatter\OdooFormatter;
use Kopernikus\TimrReportManager\Formatter\RedmineFormatter;
use Kopernikus\TimrReportManager\Services\CsvParser;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Timr
{
    private readonly Application $app;

    public function __construct(string $rootDir)
    {
        $parser = new CsvParser($rootDir);

        $app = new Application('timr', '1.0.0');
        $app->setAutoExit(false);

        $app->add(new TimrReportCommand('format:oddoo', 'odoo', $parser, new OdooFormatter()));
        $app->add(new TimrReportCommand('format:redmine', 'redmine', $parser, new RedmineFormatter()));
        $app->add(new TimrOverviewCommand($parser));

        $this->app = $app;
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        return $this->app->run($input, $output);
    }
}