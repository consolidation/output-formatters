<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ValidationInterface;
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
class StringFormatter implements FormatterInterface, ValidationInterface
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

    /**
     * Do not return any valid data types -- this formatter will never show up
     * in a list of valid formats.
     */
    public function validDataTypes()
    {
        return [];
    }

    /**
     * Always validate any data, though. This format will never
     * cause an error if it is selected for an incompatible data type; at
     * worse, it simply does not print any data.
     */
    public function validate($structuredData)
    {
        return $structuredData;
    }
}
