<?php
namespace Consolidation\OutputFormatters\StructuredData;

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
class AssociativeList extends AbstractStructuredList
{
    /**
     * Restructure this data for output by converting it into a table
     * transformation object.
     *
     * @param FormatterOptions $options Options that affect output formatting.
     * @return Consolidation\OutputFormatters\Transformations\TableTransformation
     */
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

    protected function defaultOptions()
    {
        return [
            FormatterOptions::LIST_ORIENTATION => true,
        ] + parent::defaultOptions();
    }
}
