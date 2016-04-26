<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * String formatter
 *
 * This formatter is used as the default action when no
 * particular formatter is requested.  It will print the
 * provided data only if it is a string; if any other
 * type is given, then nothing is printed.
 */
class StringFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, FormatterOptions $options)
    {
        if (is_string($data)) {
            $output->writeln($data);
        }
    }
}
