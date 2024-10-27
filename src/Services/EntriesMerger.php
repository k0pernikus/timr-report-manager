<?php

namespace Kopernikus\TimrReportManager\Services;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;

class EntriesMerger
{
    /**
     * @param Collection<int, TimeEntry> $entries
     *
     * @return Collection<int,TimeEntry>
     */
    public function mergeEntries(Collection $entries): Collection
    {
        $current = $entries->first();
        if (!$current instanceof TimeEntry) {
            throw new \LogicException('invalid object');
        }

        /** @var Collection<int, TimeEntry> $resultStream */
        $resultStream = collect();
        foreach ($entries as $entry) {
            if ($entry->start->lessThanOrEqualTo($current->end)) {
                $current = $this->mergeIntoOne($current, $entry);

                continue;
            }

            $resultStream->push($current);
            $current = $entry;
        }

        if ($current) {
            $resultStream->push($current);
        }

        return $resultStream;
    }

    private function mergeIntoOne(TimeEntry $currentMergedEntry, TimeEntry $entry): TimeEntry
    {
        return new TimeEntry(
            description: $currentMergedEntry->description,
            start: $currentMergedEntry->start,
            end: $entry->end->isAfter($currentMergedEntry->end) ? $entry->end : $currentMergedEntry->end
        );
    }
}
