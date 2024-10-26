<?php

namespace Kopernikus\TimrReportManager\Services;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;

class EntriesMerger
{
    /**
     * @param Collection<string, Collection<int, TimeEntry>> $groupedEntries
     *
     * @return Collection
     */
    public function mergeEntries(Collection $groupedEntries): Collection
    {
        /** @var Collection<int, TimeEntry> $mergedEntries */
        $mergedEntries = collect();
        $currentMergedEntry = $groupedEntries->first();

        foreach ($groupedEntries as $entry) {
            if (!$currentMergedEntry) {
                $currentMergedEntry = $entry;

                continue;
            }

            if ($entry->start->lessThanOrEqualTo($currentMergedEntry->end)) {
                $currentMergedEntry = new TimeEntry(
                    description: $currentMergedEntry->description,
                    start: $currentMergedEntry->start,
                    end: $entry->end->isAfter($currentMergedEntry->end) ? $entry->end : $currentMergedEntry->end
                );

                continue;
            }

            $mergedEntries->push($currentMergedEntry);
            $currentMergedEntry = $entry;
        }

        if ($currentMergedEntry) {
            $mergedEntries->push($currentMergedEntry);
        }

        return $mergedEntries;
    }
}
