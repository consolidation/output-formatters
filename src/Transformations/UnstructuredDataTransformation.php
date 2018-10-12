<?php
namespace Consolidation\OutputFormatters\Transformations;

use Dflydev\DotAccessData\Data;
use Consolidation\OutputFormatters\Options\FormatterOptions;

class UnstructuredDataTransformation extends \ArrayObject implements SimplifyToStringInterface
{
    protected $originalData;

    public function __construct($data, $fields)
    {
        $this->originalData = $data;
        $rows = static::transformRows($data, $fields);
        parent::__construct($rows);
    }

    protected static function transformRows($data, $fields)
    {
        $rows = [];
        foreach ($data as $rowid => $row) {
            $rows[$rowid] = static::transformRow($row, $fields);
        }
        return $rows;
    }

    protected static function transformRow($row, $fields)
    {
        if (empty($fields)) {
            return $row;
        }
        $data = new Data($row);
        $result = new Data();
        foreach ($fields as $key => $label) {
            $item = $data->get($key);
            if (isset($item)) {
                if ($label == '.') {
                    if (!is_array($item)) {
                        return $item;
                    }
                    foreach ($item as $key => $value) {
                        $result->set($key, $value);
                    }
                } else {
                    $result->set($label, $data->get($key));
                }
            }
        }
        return $result->export();
    }

    public function simplifyToString(FormatterOptions $options)
    {
        $result = '';
        $iterator = $this->getIterator();
        while ($iterator->valid()) {
            $simplifiedRow = $this->simplifyRow($iterator->current());
            if (isset($simplifiedRow)) {
                $result .= "$simplifiedRow\n";
            }

            $iterator->next();
        }
        return $result;
    }

    protected function simplifyRow($row)
    {
        if (is_string($row)) {
            return $row;
        }
        if ($this->isSimpleArray($row)) {
            return implode("\n", $row);
        }
        // No good way to simplify - just dump a json fragment
        return json_encode($row);
    }

    protected function isSimpleArray($row)
    {
        foreach ($row as $item) {
            if (!is_string($item)) {
                return false;
            }
        }
        return true;
    }
}
