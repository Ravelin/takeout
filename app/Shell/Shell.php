<?php

namespace App\Shell;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

class Shell
{
    protected $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    public function exec($command, $description = null): Process
    {
        if ($description) {
            $this->output->writeln($this->formatStartMessage($description));
        }

        $process = $this->buildProcess($command);
        $process->run(function ($type, $buffer) {
            if (empty($buffer) || $buffer === PHP_EOL) {
                return;
            }

            if ($type === Process::ERR) {
                return $this->formatErrorMessage($buffer);
            }

            return $this->formatMessage($buffer);
        });

        return $process;
    }

    public function formatStartMessage(string $buffer)
    {
        return rtrim(sprintf("<bg=blue;fg=white> RUN </> <fg=blue>%s</>", $buffer));
    }

    public function formatErrorMessage(string $buffer)
    {
        return rtrim(sprintf("<bg=red;fg=white> ERR </> %s", $buffer));
    }

    public function formatMessage(string $buffer)
    {
        return rtrim(sprintf("<bg=green;fg=white> OUT </> %s", $buffer));
    }

    public function buildProcess($command): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null);
        return $process;
    }
}