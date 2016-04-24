<?php
namespace Consolidation\OutputFormatters\Transformations;

/**
 * Reorder the field labels based on the user-selected fields
 * to display.
 */
class ReorderFields
{
    /**
     * Given a simple list of user-supplied field keys or field labels,
     * return a reordered version of the field labels matching the
     * user selection.
     *
     * @param string|array $fields The user-selected fields
     * @param array $fieldLabels An associative array mapping the field
     *   key to the field label
     * @param array $data The data that will be rendered.
     *
     * @return array
     */
    public function reorder($fields, $fieldLabels, $data)
    {
        if (empty($fieldLabels) && !empty($data)) {
            $fieldLabels = array_combine(array_keys($data[0]), array_map('ucfirst', array_keys($data[0])));
        }
        $fields = $this->getSelectedFieldKeys($fields, $fieldLabels);
        if (empty($fields)) {
            return $fieldLabels;
        }
        return $this->reorderFieldLabels($fields, $fieldLabels, $data);
    }

    protected function reorderFieldLabels($fields, $fieldLabels, $data)
    {
        $result = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data[0])) {
                if (array_key_exists($field, $fieldLabels)) {
                    $result[$field] = $fieldLabels[$field];
                }
            }
        }
        return $result;
    }

    protected function getSelectedFieldKeys($fields, $fieldLabels)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }
        $fieldLablesReverseMap = array_combine(array_values($fieldLabels), array_keys($fieldLabels));
        $selectedFields = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $fieldLabels)) {
                $selectedFields[] = $field;
            } elseif (array_key_exists($field, $fieldLablesReverseMap)) {
                $selectedFields[] = $fieldLablesReverseMap[$field];
            }
        }
        return $selectedFields;
    }
}
