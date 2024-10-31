<?php

namespace Kopernikus\TimrReportManager\Services;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Kopernikus\TimrReportManager\Enum\Header;
use League\Csv\Reader;

class CsvParser
{
    private readonly EntriesMerger $merger;

    public function __construct(
        private readonly string $rootDir,
        EntriesMerger $merger = null,
    )
    {
        $this->merger = $merger ?? new EntriesMerger();
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function parse(string $csvFile): Collection
    {
        $fullPath = $csvFile;
        if (!str_starts_with($csvFile, '/')) {
            $fullPath = $this->rootDir . DIRECTORY_SEPARATOR . $csvFile;
        }

        $records = $this->getRecords($fullPath);
        $entries = $this->toDto($records);

        return collect($entries)
            ->sortBy(fn(TimeEntry $entry) => $entry->start)
            ->groupBy(fn(TimeEntry $entry) => $entry->description)
            ->map(fn(Collection $groupedEntries) => $this->merger->mergeEntries($groupedEntries))
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
            $note = iconv('iso-8859-1', 'UTF8', $note);

            if ($note === false) {
                throw new \RuntimeException('could not decode notes from csv file');
            }

            yield new TimeEntry($note, $start, $end);
        }
    }
}
