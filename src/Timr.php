<?php

namespace Kopernikus\TimrReportManager;

use Kopernikus\TimrReportManager\Commands\TimrOverviewCommand;
use Kopernikus\TimrReportManager\Commands\TimrReportCommand;
use Kopernikus\TimrReportManager\Formatter\OdooFormatter;
use Kopernikus\TimrReportManager\Formatter\RedmineFormatter;
use Kopernikus\TimrReportManager\Services\CsvParser;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Timr
{
    private readonly Application $app;

    public function __construct(string $rootDir)
    {
        $parser = new CsvParser($rootDir);
        $cmds = $this->createCmds($parser);
        $app = $this->createApp($cmds);

        $this->app = $app;
    }

    /**
     * @return Command[]
     */
    private function createCmds(CsvParser $parser): array
    {
        return [
            new TimrReportCommand('format:redmine', 'redmine', $parser, new RedmineFormatter()),
            new TimrReportCommand('format:oddoo', 'odoo', $parser, new OdooFormatter()),
            new TimrOverviewCommand($parser),
        ];
    }

    /**
     * @param Command[] $cmds
     */
    private function createApp(array $cmds): Application
    {
        $app = new Application('timr', '1.0.0');
        $app->setAutoExit(false);
        foreach ($cmds as $cmd) {
            $app->add($cmd);
        }

        return $app;
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        return $this->app->run($input, $output);
    }
}
