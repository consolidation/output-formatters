<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SerializeFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        $output->writeln(serialize($data));
    }
}
