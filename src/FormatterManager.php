<?php
namespace Consolidation\OutputFormatters;

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

    public function getFormatter($format, $annotationData = [])
    {
        if (is_string($format) && array_key_exists($format, $this->formatters)) {
            $formatter = new $this->formatters[$format];
            if ($formatter instanceof ConfigurationAwareInterface) {
                $formatter->configure($annotationData);
            }
            return $formatter;
        }
    }
}
