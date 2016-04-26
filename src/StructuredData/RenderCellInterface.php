<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\FormatterOptions;

interface RenderCellInterface
{
    /**
     * Convert the contents of one table cell into a string,
     * so that it may be placed in the table.
     *
     * @param string $key Identifier of the cell being rendered
     * @param mixed $cellData The data to render
     *
     * @return string
     */
    public function renderCell($key, $cellData, FormatterOptions $options);
}
