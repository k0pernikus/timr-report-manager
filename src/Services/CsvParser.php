<?php

namespace Kopernikus\TimrReportManager\Services;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Enum\Header;
use League\Csv\Reader;

class CsvParser
{
    public function __construct(private readonly string $rootDir)
    {
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function parse(string $csvFile): Collection
    {
        $fullPath = $this->rootDir . DIRECTORY_SEPARATOR . $csvFile;

        $records = $this->getRecords($fullPath);
        $entries = $this->toDto($records);

        return collect($entries)
            ->sortBy(fn(TimeEntry $entry) => $entry->start)
            ->groupBy(fn(TimeEntry $entry) => $entry->description)
            ->map(function (Collection $groupedEntries): Collection {
                /** @var Collection<int, TimeEntry> $mergedEntries */
                $mergedEntries = collect();
                $currentMergedEntry = null;

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
                    } else {
                        $mergedEntries->push($currentMergedEntry);
                        $currentMergedEntry = $entry;
                    }
                }

                if ($currentMergedEntry) {
                    $mergedEntries->push($currentMergedEntry);
                }

                return $mergedEntries;
            })
            ->flatten();
    }

    private function getRecords(string $csvFile): \Iterator
    {
        $reader = Reader::createFromPath($csvFile, 'r');
        $reader->setHeaderOffset(0);

        return $reader->getRecords();
    }

    private function toDto(\Iterator $records): \Generator
    {
        foreach ($records as $record) {
            $note = $record[Header::Note->value];
            $start = $record[Header::StartDate->value];
            $end = $record[Header::EndDate->value];

            yield new TimeEntry($note, $start, $end);
        }
    }
}
