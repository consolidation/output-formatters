<?php
namespace Consolidation\OutputFormatters\StructuredData;

interface TableDataInterface
{
    /**
     * Provide formatter with annotation data to use
     * for configuration.
     *
     * @param boolean $includeRowKey Add a field containing the
     *   key from each row.
     *
     * @return array
     */
    public function getTableData($includeRowKey = false);
}
