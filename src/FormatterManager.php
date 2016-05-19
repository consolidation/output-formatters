<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\OutputFormatters\Exception\UnknownFormatException;
use Consolidation\OutputFormatters\Formatters\RenderDataInterface;
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;
use Consolidation\OutputFormatters\Transformations\DomToArraySimplifier;
use Consolidation\OutputFormatters\StructuredData\RestructureInterface;

/**
 * Manage a collection of formatters; return one on request.
 */
class FormatterManager
{
    protected $formatters = [];
    protected $arraySimplifiers = [];

    public function __construct()
    {
        $this->formatters = [
            'string' => '\Consolidation\OutputFormatters\Formatters\StringFormatter',
            'yaml' => '\Consolidation\OutputFormatters\Formatters\YamlFormatter',
            'xml' => '\Consolidation\OutputFormatters\Formatters\XmlFormatter',
            'json' => '\Consolidation\OutputFormatters\Formatters\JsonFormatter',
            'print-r' => '\Consolidation\OutputFormatters\Formatters\PrintRFormatter',
            'php' => '\Consolidation\OutputFormatters\Formatters\SerializeFormatter',
            'var_export' => '\Consolidation\OutputFormatters\Formatters\VarExportFormatter',
            'list' => '\Consolidation\OutputFormatters\Formatters\ListFormatter',
            'csv' => '\Consolidation\OutputFormatters\Formatters\CsvFormatter',
            'table' => '\Consolidation\OutputFormatters\Formatters\TableFormatter',
            'sections' => '\Consolidation\OutputFormatters\Formatters\SectionsFormatter',
        ];

        // Make the empty format an alias for the 'string' formatter.
        $this->addFormatter('', $this->formatters['string']);

        // Add our default array simplifier (DOMDocument to array)
        $this->addSimplifier(new DomToArraySimplifier());
    }

    /**
     * Add a formatter
     *
     * @param string $key the identifier of the formatter to add
     * @param string $formatterClassname the class name of the formatter to add
     * @return FormatterManager
     */
    public function addFormatter($key, $formatterClassname)
    {
        $this->formatters[$key] = $formatterClassname;
        return $this;
    }

    /**
     * Add a simplifier
     *
     * @param SimplifyToArrayInterface $simplifier the array simplifier to add
     * @return FormatterManager
     */
    public function addSimplifier(SimplifyToArrayInterface $simplifier)
    {
        $this->arraySimplifiers[] = $simplifier;
        return $this;
    }

    /**
     * Return the identifiers for all valid data types that have been registered.
     *
     * @param mixed $dataType \ReflectionObject or other description of the produced data type
     * @return array
     */
    public function validFormats($dataType)
    {
        $validFormats = [];
        foreach ($this->formatters as $formatId => $formatterName) {
            $formatter = $this->getFormatter($formatId);
            if ($this->isValidFormat($formatter, $dataType)) {
                $validFormats[] = $formatId;
            }
        }
        sort($validFormats);
        return $validFormats;
    }

    public function isValidFormat(FormatterInterface $formatter, $dataType)
    {
        if (is_array($dataType)) {
            $dataType = new \ReflectionClass('\ArrayObject');
        }
        if (!$dataType instanceof \ReflectionClass) {
            $dataType = new \ReflectionClass($dataType);
        }
        // If the formatter does not implement ValidationInterface, then
        // it is presumed that the formatter only accepts arrays.
        if (!$formatter instanceof ValidationInterface) {
            return $dataType->isSubclassOf('ArrayObject') || ($dataType->getName() == 'ArrayObject');
        }
        $supportedTypes = $formatter->validDataTypes();
        foreach ($supportedTypes as $supportedType) {
            if (($dataType->getName() == $supportedType->getName()) || $dataType->isSubclassOf($supportedType->getName())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Format and write output
     *
     * @param OutputInterface $output Output stream to write to
     * @param string $format Data format to output in
     * @param mixed $structuredOutput Data to output
     * @param FormatterOptions $options Formatting options
     */
    public function write(OutputInterface $output, $format, $structuredOutput, FormatterOptions $options)
    {
        $formatter = $this->getFormatter((string)$format);
        $structuredOutput = $this->validateAndRestructure($formatter, $structuredOutput, $options);
        $formatter->write($output, $structuredOutput, $options);
    }

    protected function validateAndRestructure(FormatterInterface $formatter, $structuredOutput, FormatterOptions $options)
    {
        // Give the formatter a chance to do something with the
        // raw data before it is restructured.
        $overrideRestructure = $this->overrideRestructure($formatter, $structuredOutput, $options);
        if ($overrideRestructure) {
            return $overrideRestructure;
        }

        // Restructure the output data (e.g. select fields to display, etc.).
        $restructuredOutput = $this->restructureData($structuredOutput, $options);

        // Make sure that the provided data is in the correct format for the selected formatter.
        $restructuredOutput = $this->validateData($formatter, $restructuredOutput, $options);

        // Give the original data a chance to re-render the structured
        // output after it has been restructured and validated.
        $restructuredOutput = $this->renderData($formatter, $structuredOutput, $restructuredOutput, $options);

        return $restructuredOutput;
    }

    /**
     * Fetch the requested formatter.
     *
     * @param string $format Identifier for requested formatter
     * @return FormatterInterface
     */
    public function getFormatter($format)
    {
        if (!$this->hasFormatter($format)) {
            throw new UnknownFormatException($format);
        }
        $formatter = new $this->formatters[$format];
        return $formatter;
    }

    /**
     * Test to see if the stipulated format exists
     */
    public function hasFormatter($format)
    {
        return array_key_exists($format, $this->formatters);
    }

    /**
     * Render the data as necessary (e.g. to select or reorder fields).
     *
     * @param FormatterInterface $formatter
     * @param mixed $originalData
     * @param mixed $restructuredData
     * @param FormatterOptions $options Formatting options
     * @return mixed
     */
    public function renderData(FormatterInterface $formatter, $originalData, $restructuredData, FormatterOptions $options)
    {
        if ($formatter instanceof RenderDataInterface) {
            return $formatter->renderData($originalData, $restructuredData, $options);
        }
        return $restructuredData;
    }

    /**
     * Determine if the provided data is compatible with the formatter being used.
     *
     * @param FormatterInterface $formatter Formatter being used
     * @param mixed $structuredOutput Data to validate
     * @return mixed
     */
    public function validateData(FormatterInterface $formatter, $structuredOutput, FormatterOptions $options)
    {
        // If the formatter implements ValidationInterface, then let it
        // test the data and throw or return an error
        if ($formatter instanceof ValidationInterface) {
            return $formatter->validate($structuredOutput);
        }
        // If the formatter does not implement ValidationInterface, then
        // it will never be passed an ArrayObject; we will always give
        // it a simple array.
        $structuredOutput = $this->simplifyToArray($structuredOutput, $options);
        // If we could not simplify to an array, then throw an exception.
        // We will never give a formatter anything other than an array
        // unless it validates that it can accept the data type.
        if (!is_array($structuredOutput)) {
            throw new IncompatibleDataException(
                $formatter,
                $structuredOutput,
                []
            );
        }
        return $structuredOutput;
    }

    protected function simplifyToArray($structuredOutput, FormatterOptions $options)
    {
        // Check to see if any of the simplifiers can convert the given data
        // set to an array.
        foreach ($this->arraySimplifiers as $simplifier) {
            $structuredOutput = $simplifier->simplifyToArray($structuredOutput, $options);
        }
        // Convert \ArrayObjects to a simple array.
        if ($structuredOutput instanceof \ArrayObject) {
            return $structuredOutput->getArrayCopy();
        }
        return $structuredOutput;
    }

    /**
     * Restructure the data as necessary (e.g. to select or reorder fields).
     *
     * @param mixed $structuredOutput
     * @param FormatterOptions $options
     * @return mixed
     */
    public function restructureData($structuredOutput, FormatterOptions $options)
    {
        if ($structuredOutput instanceof RestructureInterface) {
            return $structuredOutput->restructure($options);
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
     * @param FormatterOptions $options
     * @return mixed
     */
    public function overrideRestructure(FormatterInterface $formatter, $structuredOutput, FormatterOptions $options)
    {
        if ($formatter instanceof OverrideRestructureInterface) {
            return $formatter->overrideRestructure($structuredOutput, $options);
        }
    }
}
