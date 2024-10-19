<?php

namespace Kopernikus\TimrReportManager\Commands;

use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Enum\Header;
use Kopernikus\TimrReportManager\Services\CsvParser;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TimrReportCommand extends Command
{
    public const CSV = 'csv';

    /**
     * @param Collection $collection
     * @param OutputInterface $output
     * @return void
     */
    public function printDailyReport(Collection $collection, OutputInterface $output): void
    {
        $grouped = $collection->groupBy(fn(TimeEntry $item) => $item->description);
        $total = $this->getTotalForHumans($collection);
        $hours = $this->getTotalHours($collection);

        $output->writeln('ODOO');
        $output->writeln("Day total as HOURS:MINUTE: <info>$total</info>");
        $output->writeln("Day total as HOURS: <info>$hours</info>");

        $grouped->each(
            function (Collection $collection, $group) use ($output) {
                $sorted = $collection->sortBy(fn(TimeEntry $entry) => $entry->start->valueOf(), SORT_ASC);

                $times = $sorted->map(function (TimeEntry $entry) {
                    $start = $entry->start->format('H:i');
                    $end = $entry->end->format('H:i');

                    return $start . ' - ' . $end;
                })->toArray();
                $output->writeln('');
                $output->writeln("$group (" . $this->getTotalForHumans($collection) . ")");

                foreach ($times as $time) {
                    $output->writeln($time);
                }
            }
        );

        $output->writeln("\n\nTICKETING");
        $grouped->each(
            function (Collection $collection, $group) use ($output) {
                $output->writeln($group . ":" . $this->getTotalHours($collection));
            }
        );
    }

    /**
     * @param Collection $collection
     */
    public function getTotalForHumans(Collection $collection): string
    {
        $total = $this->getTotal($collection);

        return $total->format('%h:%I');
    }

    /**
     * @param Collection<TimeEntry>
     * @return float
     */
    public function getTotalHours(Collection $collection): float
    {
        $total = $this->getTotal($collection);

        return round($total->totalHours, 2);
    }


    public function getTotal(Collection $collection): CarbonInterval
    {
        $result = $collection->reduce(fn(CarbonInterval $total, TimeEntry $item) => $total->addMinutes($item->getDuration()), new CarbonInterval());

        return $result->cascade();
    }


    /**
     * @param string $csvFile
     * @return \Iterator
     * @throws \League\Csv\Exception
     * @throws \League\Csv\UnavailableStream
     */


    protected function configure(): void
    {
        $this
            ->setDescription('Format Timr Reports Manager')
            ->setName('format')
            ->addOption(
                static::CSV,
                'c',
                InputOption::VALUE_REQUIRED,
                'timr csv report file',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvFile = trim($input->getOption(static::CSV));

        $this
            ->parser
            ->parse($csvFile)
            ->sortBy(fn(TimeEntry $entry) => $entry->start, SORT_DESC)
            ->groupBy(fn(TimeEntry $entry) => $entry->start->format('Y-m-d'))
            ->each(
                function ($collection, $day) use ($output) {
                    $output->writeln("Date: $day");
                    $output->writeln('');
                    $this->printDailyReport($collection, $output);
                    $output->writeln(str_repeat('-', 80));
                }
            );

        return Command::SUCCESS;
    }

    public function __construct(private readonly CsvParser $parser, ?string $name = null)
    {
        parent::__construct($name);
    }
}