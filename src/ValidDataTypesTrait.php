<?php
namespace Consolidation\OutputFormatters;

/**
 * Provides a default implementation of
 */
trait ValidDataTypesTrait
{
    /**
     * Return the list of data types acceptable to this formatter
     */
    public function isValidDataType(\ReflectionClass $dataType)
    {
        return array_reduce(
            $this->validDataTypes(),
            function ($carry, $supportedType) use ($dataType) {
                return
                    $carry ||
                    ($dataType->getName() == $supportedType->getName()) ||
                    ($dataType->isSubclassOf($supportedType->getName()));
            },
            false
        );
    }
}
