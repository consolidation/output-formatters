<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RestructureInterface;
use Consolidation\OutputFormatters\Transformations\UnstructuredDataTransformation;

/**
 * Represents aribtrary unstructured array data where the
 * data to display in --list format comes from the array keys.
 *
 * Unstructured list data can have variable keys in every rown (unlike
 * RowsOfFields, which expects uniform rows), and the data elements may
 * themselves be deep arrays.
 */
class UnstructuredListData extends AbstractListData implements RestructureInterface
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function restructure(FormatterOptions $options)
    {
        $data = $this->getArrayCopy();
        $defaults = $this->defaultOptions();
        $fields = $this->getFields($options, $defaults);

        return new UnstructuredDataTransformation($data, $this->processFieldAliases($fields));
    }

    protected function processFieldAliases($fields)
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
}
