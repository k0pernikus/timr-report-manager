<?php

namespace Kopernikus\TimrReportManager\Formatter;

use Illuminate\Support\Collection;
use Kopernikus\TimrReportManager\Dto\TimeEntry;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractFormatter
{
    protected OutputInterface $output;

    /**
     * @param Collection<int,TimeEntry> $entries
     */
    abstract public function format(Collection $entries): void;

    public function setOutput(OutputInterface $output): AbstractFormatter
    {
        $this->output = $output;
        return $this;
    }
}
