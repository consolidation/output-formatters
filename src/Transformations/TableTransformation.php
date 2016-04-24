<?php
namespace Consolidation\OutputFormatters\Transformations;

class TableTransformation extends \ArrayObject
{
    protected $headers;
    protected $layout;

    const TABLE_LAYOUT = 'table';
    const LIST_LAYOUT = 'list';

    public function __construct($data, $fieldLabels)
    {
        $this->headers = $fieldLabels;
        $rows = static::transformRows($data, $fieldLabels);
        $this->layout = self::TABLE_LAYOUT;
        parent::__construct($rows);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function isList()
    {
        return $this->layout == self::LIST_LAYOUT;
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

    public function getHeader($key)
    {
        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }
        return $key;
    }

    public function getData($includeRowKey = false)
    {
        $data = $this->getArrayCopy();
        if ($this->isList()) {
            $data = $this->getListData();
        }
        if ($includeRowKey) {
            $data = $this->getRowDataWithKey($data);
        }
        return $data;
    }

    protected function getListData()
    {
        $result = [];
        foreach ($this as $row) {
            foreach ($row as $key => $value) {
                $result[$key][] = $value;
            }
        }
        return $result;
    }

    protected function getRowDataWithKey($data)
    {
        $result = [];
        $i = 0;
        foreach ($data as $key => $row) {
            array_unshift($row, $this->getHeader($key));
            $i++;
            $result[$key] = $row;
        }
        return $result;
    }
}
