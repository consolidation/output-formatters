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
        ];
    }

    public function getFormatter($format, $annotationData = [])
    {
        if (array_key_exists($format, $this->formatters)) {
            $formatter = new $this->formatters[$format];
            if ($formatter instanceof ConfigurationAwareInterface) {
                $formatter->configure($annotationData);
            }
            return $formatter;
        }
    }
}
