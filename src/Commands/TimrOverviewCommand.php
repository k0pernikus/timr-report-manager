<?php

declare(strict_types=1);

namespace Kopernikus\TimrReportManager\Commands;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\CsvParser;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TimrOverviewCommand extends Command
{
    public function __construct(private readonly CsvParser $parser, private readonly CarbonImmutable $now = new CarbonImmutable(), ?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Overview delivered and expected hours')
            ->setName('overview')
            ->addOption(
                name: 'csv',
                shortcut: 'c',
                mode: InputOption::VALUE_REQUIRED,
                description: 'expected vs done hours',
            )
            ->addOption(
                name: 'period',
                shortcut: 'p',
                mode: InputOption::VALUE_OPTIONAL,
                default: 'month',
                suggestedValues: ['day', 'week', 'month']
            )
            ->addOption(
                name: 'hours',
                shortcut: 'H',
                mode: InputOption::VALUE_OPTIONAL,
                default: 5,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetHoursPerDay = (int)$input->getOption('hours');
        $period = (string)$input->getOption('period');
        $csvFile = trim((string)$input->getOption('csv'));

        $this
            ->parser
            ->parse($csvFile)
            ->sortBy(fn(TimeEntry $entry) => $entry->start, SORT_DESC)
            ->groupBy(fn(TimeEntry $entry) => $entry->start->format('Y-m'))
            ->map(callback: function (Collection $items, string $group) use ($period, $targetHoursPerDay, $output) {
                if ($items->last() === null) {
                    throw new RuntimeException('Invalid collection');
                }

                $excludeDates = [
                    '2024-10-03',
                    '2024-10-04',
                ];

                [$year, $month] = explode("-", $group);
                $start = Carbon::create((int)$year, (int)$month, 1);

                $latest = $items->last()->end;
                $end = $this->now->isSameMonth($latest) ? $this->now : $latest->end;

                $amountDays = match ($period) {
                    'day' => 1,
                    'week' => 7, // also must used exluded dates
                    default => (CarbonPeriod::create($start, $end))
                        ::filter('isWeekday')
                        ::filter(
                            static function (Carbon $date) use ($excludeDates): bool {
                                return !in_array($date->format('Y-m-d'), $excludeDates);
                            }
                        )
                        ->count(),
                };

                $targetHours = $amountDays * $targetHoursPerDay;

                $deliveredHours = $items->reduce(
                    fn(CarbonInterval $total, TimeEntry $item): CarbonInterval => $total->addMinutes($item->getDuration()),
                    new CarbonInterval()
                );
                $deliveredHours->cascade();

                $allTheHours = $deliveredHours->totalHours;
                $output->writeln("$group: Expected $targetHours / Delivered $allTheHours");

                return [$group, $items];
            });

        return Command::SUCCESS;
    }
}
