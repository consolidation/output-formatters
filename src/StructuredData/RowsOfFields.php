<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\RestructureInterface;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;
use Consolidation\OutputFormatters\Transformations\PropertyParser;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Transformations\TableTransformation;

/**
 * Holds an array where each element of the array is one row,
 * and each row contains an associative array where the keys
 * are the field names, and the values are the field data.
 *
 * It is presumed that every row contains the same keys.
 */
class RowsOfFields extends \ArrayObject implements RestructureInterface, ListDataInterface
{
    protected $data;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function restructure($configurationData, $options)
    {
        $data = $this->getArrayCopy();
        return $this->createTableTransformation($data, $configurationData, $options);
    }

    public function getListData()
    {
        return array_keys($this->getArrayCopy());
    }

    protected function createTableTransformation($data, $configurationData, $options)
    {
        $options = $this->interpretOptions($configurationData, $options);

        $reorderer = new ReorderFields();
        $fieldLabels = $reorderer->reorder($options['fields'], $options['field-labels'], $data);

        $tableTransformer = new TableTransformation($data, $fieldLabels);

        return $tableTransformer;
    }

    protected function interpretOptions($configurationData, $options)
    {
        $configurationData += $this->defaultOptions();

        $configurationData['field-labels'] = PropertyParser::parse($configurationData['field-labels']);
        $configurationData['default-fields'] = PropertyParser::parse($configurationData['default-fields']);

        return $options + $configurationData;
    }

    protected function defaultOptions()
    {
        return [
            'fields' => [],
            'field-labels' => [],
            'default-fields' => [],
        ];
    }
}
