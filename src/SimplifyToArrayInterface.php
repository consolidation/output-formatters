<?php
namespace Consolidation\OutputFormatters;

interface SimplifyToArrayInterface
{
    /**
     * Convert structured data into a generic array, usable by generic
     * array-based formatters.  Objects that implement this interface may
     * be attached to the FormatterManager, and will be used on any data
     * structure that needs to be simplified into an array.  An array
     * simplifier should take no action other than to return its input data
     * if it cannot simplify the provided data into an array.
     *
     * @param mixed $structuredOutput The data to simplify to an array.
     *
     * @return array
     */
    public function simplifyToArray($structuredOutput, FormatterOptions $options);
}
