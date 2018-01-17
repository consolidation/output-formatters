<?php

namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
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
        $dumper = new CliDumper();
        $cloned_data = (new VarCloner())->cloneVar($data);

        if ($output instanceof StreamOutput) {
            // When stream output is used the dumper is smart enough to
            // determine whether or not to apply colors to the dump.
            // @see Symfony\Component\VarDumper\Dumper\CliDumper::supportsColors
            $dumper->dump($cloned_data, $output->getStream());
        } else {
            $output->writeln($dumper->dump($cloned_data, true));
        }
    }
}
