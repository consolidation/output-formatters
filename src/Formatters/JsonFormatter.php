<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JsonFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write($data, $options, OutputInterface $output)
    {
        $output->writeln(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
