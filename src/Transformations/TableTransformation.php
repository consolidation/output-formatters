<?php
namespace Consolidation\OutputFormatters\Transformations;

class TableTransformation
{
    protected $headers;
    protected $rows;

    public function __construct($data, $fieldLabels, $options)
    {
        $this->headers = array_values($fieldLabels);
        $this->rows = [];
        foreach ($data as $row) {
            $this->rows[] = $this->transformRow($row, $fieldLabels);
        }
    }

    protected function transformRow($row, $fieldLabels)
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
        return $this->rows;
    }
}
