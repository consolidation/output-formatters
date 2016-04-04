<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write($data, $options, OutputInterface $output)
    {
        $output->writeln(implode("\n", $data));
    }
}
