<?php
namespace Consolidation\OutputFormatters;

interface SimplifyToArrayInterface
{
    /**
     * Convert structured data into a generic array, usable by generic
     * array-based formatters.  This interface may be implemented by
     * formatters; for example, the XmlFormatter provides simplification
     * services for \DomDocument objects.
     *
     * @param mixed $structuredOutput The data to simplify to an array.
     *
     * @return array
     */
    public function simplifyToArray($structuredOutput, FormatterOptions $options);
}
