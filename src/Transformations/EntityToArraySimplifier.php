<?php

namespace Consolidation\OutputFormatters\Transformations;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\Xml\DomDataInterface;
use Consolidation\OutputFormatters\StructuredData\Xml\XmlSchema;

/**
 * Simplify a EntityInterface to an array.
 */
class EntityToArraySimplifier implements SimplifyToArrayInterface
{
    public function __construct()
    {
    }

    /**
     * @param ReflectionClass $dataType
     */
    public function canSimplify(\ReflectionClass $dataType)
    {
        return $dataType->implementsInterface('\Drupal\Core\Entity\EntityInterface');
    }

    public function simplifyToArray($structuredData, FormatterOptions $options)
    {
        return $structuredData->toArray();
    }
}
