<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\FormatterOptions;

/**
 * Holds an array where each element of the array is one row,
 * and each row contains an associative array where the keys
 * are the field names, and the values are the field data.
 *
 * It is presumed that every row contains the same keys.
 */
class RowsOfFields extends AbstractStructuredList
{
    public function restructure(FormatterOptions $options)
    {
        $data = $this->getArrayCopy();
        return $this->createTableTransformation($data, $options);
    }

    public function getListData()
    {
        return array_keys($this->getArrayCopy());
    }
}
