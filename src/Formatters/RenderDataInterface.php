<?php
namespace Consolidation\OutputFormatters\Formatters;

interface RenderDataInterface
{
    /**
     * Convert the contents of the output data just before it
     * is to be printed, prior to output but after restructuring
     * and validation.
     *
     * @param mixed $data Restructured, validated data.
     *
     * @return mixed
     */
    public function renderData($originalData, $restructuredData, $configurationData, $options);
}
