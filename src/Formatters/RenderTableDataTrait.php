<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\StructuredData\RenderCellInterface;

trait RenderTableDataTrait
{
    /**
     * @inheritdoc
     */
    public function renderData($originalData, $restructuredData, $configurationData, $options)
    {
        if ($originalData instanceof RenderCellInterface) {
            return $this->renderEachCell($originalData, $restructuredData, $configurationData, $options);
        }
        return $restructuredData;
    }

    protected function renderEachCell($originalData, $restructuredData, $configurationData, $options)
    {
        foreach ($restructuredData as $id => $row) {
            foreach ($row as $key => $cellData) {
                $restructuredData[$id][$key] = $originalData->renderCell($key, $cellData, $configurationData, $options);
            }
        }
        return $restructuredData;
    }
}
