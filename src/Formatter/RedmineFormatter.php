<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Services\Entries;

class RedmineFormatter extends AbstractFormatter
{
    public function format(Collection $entries): void
    {
        $output = $this->output;
        $grouped = $entries->groupBy(fn(TimeEntry $item) => $item->description);
        $output->writeln("\n\nTICKETING");
        $grouped->each(
            function (Collection $collection, $group) use ($output) {
                $output->writeln($group . ":" . Entries::getTotalHours($collection));
            }
        );
    }
}
