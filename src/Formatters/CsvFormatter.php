<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CsvFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        if (empty($data) || !is_array($data)) {
            return;
        }
        if (!is_array($data[0])) {
            $this->writeCsvLine($data, $options, $output);
            return;
        }
        $this->writeCsvLines($data, $options, $output);
    }

    public function writeCsvLines($data, $options, OutputInterface $output)
    {
        foreach ($data as $line) {
            $this->writeCsvLine($line, $options, $output);
        }
    }

    public function writeCsvLine($data, $options, OutputInterface $output)
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
