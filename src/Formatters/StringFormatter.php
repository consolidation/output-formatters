<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ValidationInterface;
use Consolidation\OutputFormatters\OverrideOptionsInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\OutputFormatters\StructuredData\RestructureInterface;

/**
 * String formatter
 *
 * This formatter is used as the default action when no
 * particular formatter is requested.  It will print the
 * provided data only if it is a string; if any other
 * type is given, then nothing is printed.
 */
class StringFormatter implements FormatterInterface, ValidationInterface, OverrideOptionsInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, FormatterOptions $options)
    {
        if (is_string($data)) {
            return $output->writeln($data);
        }
        return $this->reduceToSigleFieldAndWrite($output, $data, $options);
    }

    /**
     * @inheritdoc
     */
    public function overrideOptions($structuredOutput, FormatterOptions $options)
    {
        $defaultField = $options->get(FormatterOptions::DEFAULT_STRING_FIELD, [], '');
        $userFields = $options->get(FormatterOptions::FIELDS);
        $optionsOverride = $options->override([]);
        if (empty($userFields) && !empty($defaultField)) {
            $optionsOverride->setOption(FormatterOptions::FIELDS, $defaultField);
        }
        return $optionsOverride;
    }

    /**
     * If the data provided to a 'string' formatter is a table, then try
     * to emit it as a TSV value.
     *
     * @param OutputInterface $output
     * @param mixed $data
     * @param FormatterOptions $options
     */
    protected function reduceToSigleFieldAndWrite(OutputInterface $output, $data, FormatterOptions $options)
    {
        $alternateFormatter = new TsvFormatter();
        try {
            $data = $alternateFormatter->validate($data);
            $alternateFormatter->write($output, $data, $options);
        } catch (\Exception $e) {
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
