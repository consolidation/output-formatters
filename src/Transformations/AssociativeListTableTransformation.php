<?php
namespace Consolidation\OutputFormatters\Transformations;

class AssociativeListTableTransformation extends TableTransformation
{
    public function getOriginalData()
    {
        $data = $this->getArrayCopy();
        return $data[0];
    }
}
