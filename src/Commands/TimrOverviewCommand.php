<?php

namespace Kopernikus\TimrReportManager\Commands;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\CsvParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TimrOverviewCommand extends Command
{
    public function __construct(private readonly CsvParser $parser, ?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Overview delivered and expected hours')
            ->setName('overview')
            ->addOption(
                'csv',
                'c',
                InputOption::VALUE_REQUIRED,
                'expected vs done hours',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetHoursPerDay = 5;

        $csvFile = trim($input->getOption('csv'));
        $this
            ->parser
            ->parse($csvFile)
            ->sortBy(fn(TimeEntry $entry) => $entry->start, SORT_DESC)
            ->groupBy(fn(TimeEntry $entry) => $entry->start->format('Y-m'))
            ->map(function (Collection $items, string $group) use ($targetHoursPerDay, $output) {
                $excludeDates = [
                    '2024-10-03',
                    '2024-10-04',
                ];

                [$year, $month] = explode("-", $group);
                $start = Carbon::create($year, $month, 1);
                //$end = $start->copy()->endOfMonth();
                $end = Carbon::now();

                $amountDays = (CarbonPeriod::create($start, $end))
                    ->filter('isWeekday')
                    ->filter(
                        function (Carbon $date) use ($excludeDates) {
                            return !in_array($date->format('Y-m-d'), $excludeDates);
                        })
                    ->count();

                $targetHours = $amountDays * $targetHoursPerDay;
                /**
                 * @var $deliveredHours
                 */
                $deliveredHours = $items->reduce(
                    fn(CarbonInterval $total, TimeEntry $item): CarbonInterval => $total->addMinutes($item->getDuration()), new CarbonInterval()
                );
                $deliveredHours->cascade();
                $allTheHours = $deliveredHours->totalHours;
                $output->writeln("$group: Expected $targetHours / Delivered $allTheHours");

                return [$group, $items];
            });

        return Command::SUCCESS;
    }


}