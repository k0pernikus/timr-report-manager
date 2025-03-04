<?php

declare(strict_types=1);

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
    abstract public function format(Collection $entries, string $day): void;

    public function setOutput(OutputInterface $output): AbstractFormatter
    {
        $this->output = $output;

        return $this;
    }

    protected function printLine(
        string $msg = '',
        int $indentationLevel = 0,
        string $char = ' ',
        int $amountCharsPerLevel = 4,
    ): void
    {
        if ($indentationLevel <= 0) {
            $this->output->writeln($msg);

            return;
        }

        $amount = $indentationLevel * $amountCharsPerLevel;
        $prefix = str_repeat($char, $amount);

        $this->output->writeln($prefix . $msg);
    }
}
