<?php
namespace Consolidation\TestUtils;

use Consolidation\OutputFormatters\StructuredData\AssociativeList;
use Consolidation\OutputFormatters\StructuredData\RenderCellInterface;

class AssociativeListWithCsvCells extends AssociativeList implements RenderCellInterface
{
    public function renderCell($key, $cellData, $configurationData, $options)
    {
        if (is_array($cellData)) {
            return implode(',', $cellData);
        }
        return $cellData;
    }
}
