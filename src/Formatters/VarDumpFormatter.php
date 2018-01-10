<?php

namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * Var_dump formatter
 *
 * Run provided data through Symfony VarDumper component.
 */
class VarDumpFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, FormatterOptions $options)
    {
        /** @var \Symfony\Component\Console\Output\StreamOutput $output */
        (new CliDumper())->dump(
            (new VarCloner())->cloneVar($data),
            $output->getStream()
        );
    }
}
