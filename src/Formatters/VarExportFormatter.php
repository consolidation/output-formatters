<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VarExportFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write($data, $options, OutputInterface $output)
    {
        $output->writeln(var_export($data, true));
    }
}
