<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StringFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        if (is_string($data)) {
            $output->writeln($data);
        }
    }
}
