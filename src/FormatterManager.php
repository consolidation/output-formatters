<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Manage a collection of formatters; return one on request.
 */
class FormatterManager
{
    protected $formatters = [];

    public function __construct()
    {
        $this->formatters = [
            'yaml' => '\Consolidation\OutputFormatters\Formatters\YamlFormatter',
            'json' => '\Consolidation\OutputFormatters\Formatters\JsonFormatter',
            'print-r' => '\Consolidation\OutputFormatters\Formatters\PrintRFormatter',
            'var_export' => '\Consolidation\OutputFormatters\Formatters\VarExportFormatter',
            'list' => '\Consolidation\OutputFormatters\Formatters\ListFormatter',
            'csv' => '\Consolidation\OutputFormatters\Formatters\CsvFormatter',
            'table' => '\Consolidation\OutputFormatters\Formatters\TableFormatter',
        ];
    }

    public function write(OutputInterface $output, $format, $structuredOutput, $annotationData = [], $options = [])
    {
        $formatter = $this->getFormatter($format, $annotationData);

        // Restructure the output data (e.g. select fields to display, etc.).
        $structuredOutput = $this->restructureData($structuredOutput, $annotationData, $options);

        // Make sure that the provided data is in the correct format for the selected formatter.
        $structuredOutput = $this->validateData($formatter, $structuredOutput);

        $formatter->write($output, $structuredOutput, $options);
    }

    public function getFormatter($format, $configurationData = [])
    {
        if (is_string($format) && array_key_exists($format, $this->formatters)) {
            $formatter = new $this->formatters[$format];
            if ($formatter instanceof ConfigureInterface) {
                $formatter->configure($configurationData);
            }
            return $formatter;
        }
    }

    public function validateData(FormatterInterface $formatter, $structuredOutput)
    {
        // If the formatter implements ValidationInterface, then let it
        // test the data and throw or return an error
        if ($formatter instanceof ValidationInterface) {
            return $formatter->validate($structuredOutput);
        }
        // If the formatter does not implement ValidationInterface, then
        // it will never be passed an ArrayObject; we will always give
        // it a simple array.
        if ($structuredOutput instanceof \ArrayObject) {
            return $structuredOutput->getArrayCopy();
        }

        return $structuredOutput;
    }

    public function restructureData($structuredOutput, $configurationData, $options)
    {
        if ($structuredOutput instanceof RestructureInterface) {
            return $structuredOutput->restructure($configurationData, $options);
        }
        return $structuredOutput;
    }
}
