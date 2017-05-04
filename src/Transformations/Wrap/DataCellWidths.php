<?php
namespace Consolidation\OutputFormatters\Transformations\Wrap;

use Symfony\Component\Console\Helper\TableStyle;

/**
 * Calculate the width of data in table cells in preparation for word wrapping.
 */
class DataCellWidths
{
    protected $widths;

    public function __consturct()
    {
        $this->widths = [];
    }

    /**
     * Calculate the longest cell data from any row of each of the cells.
     */
    public function calculateLongestCell($rows)
    {
        $this->widths = [];

        // Examine each row and find the longest line length and longest
        // word in each column.
        foreach ($rows as $rowkey => $row) {
            foreach ($row as $colkey => $cell) {
                $lineLength = strlen($cell);
                if ((!isset($this->widths[$colkey]) || ($this->widths[$colkey] < $lineLength))) {
                    $this->widths[$colkey] = $lineLength;
                }
            }
        }
    }

    /**
     * Calculate the longest word and longest line in the provided data.
     */
    public function calculateLongestWord($rows)
    {
        $this->widths = [];

        // Examine each row and find the longest line length and longest
        // word in each column.
        foreach ($rows as $rowkey => $row) {
            foreach ($row as $colkey => $cell) {
                $longestWordLength = static::longestWordLength($cell);
                if ((!isset($this->widths[$colkey]) || ($this->widths[$colkey] < $longestWordLength))) {
                    $this->widths[$colkey] = $longestWordLength;
                }
            }
        }
    }

    public function paddingSpace(
        $paddingInEachCell,
        $extraPaddingAtEndOfLine = 0,
        $extraPaddingAtBeginningOfLine = 0)
    {
        return ($extraPaddingAtBeginningOfLine + $extraPaddingAtEndOfLine + (count($this->widths) * $paddingInEachCell));
    }

    /**
     * Find all columns that are shorter than the specified threshold width.
     * These are removed from this object, and returned as the result of
     * this method.
     */
    public function removeShortColumns($thresholdWidth)
    {
        $shortColWidths = $this->findShortColumns($thresholdWidth);
        $this->removeColumns(array_keys($shortColWidths));
        return $shortColWidths;
    }

    /**
     * Find all of the columns that are shorter than the specified threshold.
     */
    public function findShortColumns($thresholdWidth)
    {
        $shortColWidths = [];

        foreach ($this->widths as $key => $maxLength) {
            if ($maxLength <= $thresholdWidth) {
                $shortColWidths[$key] = $maxLength;
            }
        }

        return $shortColWidths;
    }

    /**
     * Remove all of the specified columns from this data structure.
     */
    public function removeColumns($columnKeys)
    {
        foreach ($columnKeys as $key) {
            unset($this->widths[$key]);
        }
    }

    /**
     * Return proportional weights
     */
    public function distribute($availableWidth)
    {
        $result = [];
        $totalWidth = $this->totalWidth();
        $lastColumn = $this->lastColumn();
        $widths = $this->widths();
        array_pop($widths);

        foreach ($widths as $key => $width) {
            $result[$key] = round(($width / $totalWidth) * $availableWidth);
        }

        $usedWidth = $this->sumWidth($result);
        $result[$lastColumn] = $availableWidth - $usedWidth;

        return $result;
    }

    public function lastColumn()
    {
        $keys = $this->keys();
        return array_pop($keys);
    }

    /**
     * Return the available keys (column identifiers) from the calculated
     * data set.
     */
    public function keys()
    {
        return array_keys($this->widths);
    }

    /**
     * Return the length of the specified column.
     */
    public function width($key)
    {
        return isset($this->widths[$key]) ? $this->widths[$key] : 0;
    }

    /**
     * Return all of the lengths
     */
    public function widths()
    {
        return $this->widths;
    }

    /**
     * Return the sum of the lengths of the provided widths.
     */
    public function totalWidth()
    {
        return static::sumWidth($this->widths());
    }

    /**
     * Return the sum of the lengths of the provided widths.
     */
    public static function sumWidth($widths)
    {
        return array_reduce(
            $widths,
            function ($carry, $item) {
                return $carry + $item;
            }
        );
    }

    /**
     * Return the length of the longest word in the string.
     * @param string $str
     * @return int
     */
    protected static function longestWordLength($str)
    {
        $words = preg_split('#[ /-]#', $str);
        $lengths = array_map(function ($s) {
            return strlen($s);
        }, $words);
        return max($lengths);
    }
}
