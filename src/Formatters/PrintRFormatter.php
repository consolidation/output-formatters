<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintRFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write($data, $options, OutputInterface $output)
    {
        $output->writeln(print_r($data, true));
    }
}
