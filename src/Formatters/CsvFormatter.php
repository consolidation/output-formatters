<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ValidationInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Consolidation\OutputFormatters\Transformations\TableTransformation;
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comma-separated value formatters
 *
 * Display the provided structured data in a comma-separated list. If
 * there are multiple records provided, then they will be printed
 * one per line.  The primary data types accepted are RowsOfFields and
 * AssociativeList. The later behaves exactly like the former, save for
 * the fact that it contains but a single row. This formmatter can also
 * accept a PHP array; this is also interpreted as a single-row of data
 * with no header.
 */
class CsvFormatter implements FormatterInterface, ValidationInterface, RenderDataInterface
{
    use RenderTableDataTrait;

    public function validDataTypes()
    {
        return
            [
                new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields'),
                new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\AssociativeList'),
                new \ReflectionClass('\ArrayObject'),
            ];
    }

    public function validate($structuredData)
    {
        // If the provided data was of class RowsOfFields
        // or AssociativeList, it will be converted into
        // a TableTransformation object.
        if (!is_array($structuredData) && (!$structuredData instanceof TableTransformation)) {
            throw new IncompatibleDataException(
                $this,
                $structuredData,
                $this->validDataTypes()
            );
        }
        // If the data was provided to us as a single array, then
        // convert it to a single row.
        if (is_array($structuredData) && !empty($structuredData)) {
            $firstRow = reset($structuredData);
            if (!is_array($firstRow)) {
                return [$structuredData];
            }
        }
        return $structuredData;
    }

    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, FormatterOptions $options)
    {
        $defaults = [
            'include-field-labels' => true,
        ];

        if ($options->get(FormatterOptions::INCLUDE_FIELD_LABELS, $defaults) && ($data instanceof TableTransformation)) {
            $headers = $data->getHeaders();
            $this->writeCsvLine($output, $headers, $options);
        }

        foreach ($data as $line) {
            $this->writeCsvLine($output, $line, $options);
        }
    }

    protected function writeCsvLine(OutputInterface $output, $data, $options)
    {
        $output->write($this->csvEscape($data));
    }

    protected function csvEscape($data, $delimiter = ',')
    {
        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, $data, $delimiter);
        rewind($buffer);
        $csv = fgets($buffer);
        fclose($buffer);
        return $csv;
    }
}
