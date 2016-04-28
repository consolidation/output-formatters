<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\RestructureInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Transformations\TableTransformation;

/**
 * Holds an array where each element of the array is one row,
 * and each row contains an associative array where the keys
 * are the field names, and the values are the field data.
 *
 * It is presumed that every row contains the same keys.
 */
abstract class AbstractStructuredList extends \ArrayObject implements RestructureInterface, ListDataInterface
{
    protected $data;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public abstract function restructure(FormatterOptions $options);

    public abstract function getListData();

    protected function createTableTransformation($data, $options)
    {
        $defaults = $this->defaultOptions();

        $reorderer = new ReorderFields();
        $fieldLabels = $reorderer->reorder($options->get('fields', $defaults), $options->get('field-labels', $defaults), $data);

        $tableTransformer = new TableTransformation($data, $fieldLabels, $options->get('row-labels', $defaults));
        if ($options->get('list-orientation', $defaults)) {
            $tableTransformer->setLayout(TableTransformation::LIST_LAYOUT);
        }

        return $tableTransformer;
    }

    protected function defaultOptions()
    {
        return [
            'list-orientation' => false,
            'fields' => [],
            'field-labels' => [],
            'row-labels' => [],
            'default-fields' => [],
        ];
    }
}
