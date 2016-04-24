<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ValidationInterface;
use Consolidation\OutputFormatters\Transformations\TableTransformation;
use Symfony\Component\Console\Output\OutputInterface;

class CsvFormatter implements FormatterInterface, ValidationInterface
{
    public function validate($structuredData)
    {
        // If the provided data was of class RowsOfFields
        // or AssociativeList, it will be converted into
        // a TableTransformation object.
        if (!is_array($structuredData) && (!$structuredData instanceof TableTransformation)) {
            throw new IncompatibleDataException(
                $this,
                $structuredData,
                [
                    new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields'),
                    new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\AssociativeList'),
                    [],
                ]
            );
        }
        return $structuredData;
    }

    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        if ($this->isMultiLine($data)) {
            $this->writeCsvLines($output, $data, $options);
            return;
        }
        $this->writeCsvLine($output, $data, $options);
    }

    protected function isMultiLine($data)
    {
        return
            ($data instanceof TableTransformation) ||
            (
                !empty($data) &&
                is_array($data[0])
            );
    }

    protected function writeCsvLines(OutputInterface $output, $data, $options)
    {
        $options += [
            'include-field-labels' => true,
        ];

        if ($options['include-field-labels'] && ($data instanceof TableTransformation)) {
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
