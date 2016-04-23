<?php
namespace Consolidation\OutputFormatters\Transformations;

class TableTransformation extends \ArrayObject
{
    protected $headers;

    public function __construct($data, $fieldLabels)
    {
        $this->headers = array_values($fieldLabels);
        $rows = static::transformRows($data, $fieldLabels);
        if (empty($this->headers) && !empty($rows)) {
            $this->headers = array_combine(array_keys($rows[0]), array_keys($rows[0]));
        }
        parent::__construct($rows);
    }

    protected static function transformRows($data, $fieldLabels)
    {
        $rows = [];
        foreach ($data as $row) {
            $rows[] = static::transformRow($row, $fieldLabels);
        }
        return $rows;
    }

    protected static function transformRow($row, $fieldLabels)
    {
        $result = [];
        foreach ($fieldLabels as $key => $label) {
            $result[$key] = array_key_exists($key, $row) ? $row[$key] : '';
        }
        return $result;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getData()
    {
        return $this->getArrayCopy();
    }
}
