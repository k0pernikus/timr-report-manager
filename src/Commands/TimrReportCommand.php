<?php

namespace Kopernikus\TimrReportManager\Commands;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Formatter\AbstractFormatter;
use Kopernikus\TimrReportManager\Services\CsvParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TimrReportCommand extends Command
{
    public const CSV = 'csv';

    public function __construct(
        string $name,
        string $description,
        private readonly CsvParser $parser,
        private readonly AbstractFormatter $formatter
    ) {
        parent::__construct($name);
        $this->setDescription($description);
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                static::CSV,
                'c',
                InputOption::VALUE_REQUIRED,
                'timr csv report file',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvFile = trim((string)$input->getOption(static::CSV));

        $this
            ->parser
            ->parse($csvFile)
            ->sortBy(fn(TimeEntry $entry) => $entry->start, SORT_DESC)
            ->groupBy(fn(TimeEntry $entry) => $entry->start->format('D, Y-m-d'))
            ->each(
                function ($collection, $day) use ($output) {
                    $this->printDailyReport($collection, $output, $day);
                }
            );

        return Command::SUCCESS;
    }

    /**
     * @param Collection<int,TimeEntry> $collection
     */
    public function printDailyReport(Collection $collection, OutputInterface $output, string $day): void
    {
        $this->formatter->setOutput($output);
        $this->formatter->format($collection, $day);
    }
}
