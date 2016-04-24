<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\RestructureInterface;
use Consolidation\OutputFormatters\Transformations\PropertyParser;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Transformations\TableTransformation;

/**
 * Holds an array where each element of the array is one
 * key : value pair.  The keys must be unique, as is typically
 * the case for associative arrays.
 */
class AssociativeList extends RowsOfFields
{
    protected $data;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function restructure($configurationData, $options)
    {
        $data = [$this->getArrayCopy()];
        $tableTransformer = $this->createTableTransformation($data, $configurationData, $options);
        $tableTransformer->setLayout(TableTransformation::LIST_LAYOUT);
        return $tableTransformer;
    }
}
