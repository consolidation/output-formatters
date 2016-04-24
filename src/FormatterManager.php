<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\Exception\UnknownFormatException;

/**
 * Manage a collection of formatters; return one on request.
 */
class FormatterManager
{
    protected $formatters = [];

    public function __construct()
    {
        $this->formatters = [
            'default' => '\Consolidation\OutputFormatters\Formatters\DefaultFormatter',
            'yaml' => '\Consolidation\OutputFormatters\Formatters\YamlFormatter',
            'json' => '\Consolidation\OutputFormatters\Formatters\JsonFormatter',
            'print-r' => '\Consolidation\OutputFormatters\Formatters\PrintRFormatter',
            'php' => '\Consolidation\OutputFormatters\Formatters\SerializeFormatter',
            'var_export' => '\Consolidation\OutputFormatters\Formatters\VarExportFormatter',
            'list' => '\Consolidation\OutputFormatters\Formatters\ListFormatter',
            'csv' => '\Consolidation\OutputFormatters\Formatters\CsvFormatter',
            'table' => '\Consolidation\OutputFormatters\Formatters\TableFormatter',
        ];

        // Make the empty string an alias for 'default'.
        $this->formatters[''] = $this->formatters['default'];
    }

    /**
     * Format and write output
     *
     * @param OutputInterface $output Output stream to write to
     * @param string $format Data format to output in
     * @param mixed $structuredOutput Data to output
     * @param array $configurationData Configuration information for formatter
     * @param array $options User options
     */
    public function write(OutputInterface $output, $format, $structuredOutput, $configurationData = [], $options = [])
    {
        $formatter = $this->getFormatter((string)$format, $configurationData);
        $structuredOutput = $this->validateAndRestructure($formatter, $structuredOutput, $configurationData, $options);
        $formatter->write($output, $structuredOutput, $options);
    }

    protected function validateAndRestructure(FormatterInterface $formatter, $structuredOutput, $configurationData, $options)
    {
        // Give the formatter a chance to do something with the
        // raw data before it is restructured.
        $overrideRestructure = $this->overrideRestructure($formatter, $structuredOutput, $configurationData, $options);
        if ($overrideRestructure) {
            return $overrideRestructure;
        }

        // Restructure the output data (e.g. select fields to display, etc.).
        $structuredOutput = $this->restructureData($structuredOutput, $configurationData, $options);

        // Make sure that the provided data is in the correct format for the selected formatter.
        $structuredOutput = $this->validateData($formatter, $structuredOutput);

        return $structuredOutput;
    }

    /**
     * Fetch the requested formatter.
     *
     * @param string $format Identifier for requested formatter
     * @param array $configurationData Configuration data for formatter
     * @return FormatterInterface
     */
    public function getFormatter($format, $configurationData = [])
    {
        if (!$this->hasFormatter($format)) {
            throw new UnknownFormatException($format);
        }

        $formatter = new $this->formatters[$format];
        if ($formatter instanceof ConfigureInterface) {
            $formatter->configure($configurationData);
        }
        return $formatter;
    }

    public function hasFormatter($format)
    {
        return array_key_exists($format, $this->formatters);
    }

    /**
     * Determine if the provided data is compatible with the formatter being used.
     *
     * @param FormatterInterface $formatter Formatter being used
     * @param mixed $structuredOutput Data to validate
     * @return mixed
     */
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

    /**
     * Restructure the data as necessary (e.g. to select or reorder fields).
     *
     * @param mixed $structuredOutput
     * @param array $configurationData
     * @param array $options
     * @return mixed
     */
    public function restructureData($structuredOutput, $configurationData, $options)
    {
        if ($structuredOutput instanceof RestructureInterface) {
            return $structuredOutput->restructure($configurationData, $options);
        }
        return $structuredOutput;
    }

    /**
     * Allow the formatter access to the raw structured data prior
     * to restructuring.  For example, the 'list' formatter may wish
     * to display the row keys when provided table output.  If this
     * function returns a result that does not evaluate to 'false',
     * then that result will be used as-is, and restructuring and
     * validation will not occur.
     *
     * @param mixed $structuredOutput
     * @param array $configurationData
     * @param array $options
     * @return mixed
     */
    public function overrideRestructure(FormatterInterface $formatter, $structuredOutput, $configurationData, $options)
    {
        if ($formatter instanceof OverrideRestructureInterface) {
            return $formatter->overrideRestructure($structuredOutput, $configurationData, $options);
        }
    }
}
