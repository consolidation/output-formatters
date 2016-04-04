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
            'yaml' => \Consolidation\OutputFormatters\Formatters\YamlFormatter::class,
            'json' => \Consolidation\OutputFormatters\Formatters\JsonFormatter::class,
            'print-r' => \Consolidation\OutputFormatters\Formatters\PrintRFormatter::class,
            'var_export' => \Consolidation\OutputFormatters\Formatters\VarExportFormatter::class,
            'list' => \Consolidation\OutputFormatters\Formatters\ListFormatter::class,
            'csv' => \Consolidation\OutputFormatters\Formatters\CsvFormatter::class,
            'table' => \Consolidation\OutputFormatters\Formatters\TableFormatter::class,
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
