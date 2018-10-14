<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RestructureInterface;
use Consolidation\OutputFormatters\Transformations\UnstructuredDataListTransformation;

/**
 * FieldProcessor will do various alterations on field sets.
 */
class FieldProcessor
{
    public static function processFieldAliases($fields)
    {
        if (!is_array($fields)) {
            $fields = array_filter(explode(',', $fields));
        }
        $transformed_fields = [];
        foreach ($fields as $field) {
            list($machine_name,$label) = explode(' as ', $field) + [$field, preg_replace('#.*\.#', '', $field)];
            $transformed_fields[$machine_name] = $label;
        }
        return $transformed_fields;
    }

    public static function hasUnstructuredFieldAccess($options)
    {
    }
}
