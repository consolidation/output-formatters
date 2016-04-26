<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\RestructureInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;
use Consolidation\OutputFormatters\Transformations\PropertyParser;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Transformations\TableTransformation;

/**
 * Holds an array where each element of the array is one
 * key : value pair.  The keys must be unique, as is typically
 * the case for associative arrays.
 */
class AssociativeList extends RowsOfFields implements ListDataInterface
{
    protected $data;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function restructure(FormatterOptions $options)
    {
        $data = [$this->getArrayCopy()];
        $options->setConfigurationDefault('list-orientation', true);
        $tableTransformer = $this->createTableTransformation($data, $options);
        return $tableTransformer;
    }

    public function getListData()
    {
        return $this->getArrayCopy();
    }
}
