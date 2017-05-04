<?php
namespace Consolidation\OutputFormatters\Transformations\Wrap;

use Symfony\Component\Console\Helper\TableStyle;

/**
 * Calculate the width of data in table cells in preparation for word wrapping.
 */
class DataCellWidths
{
    protected $widths;

    public function __construct($widths = [])
    {
        $this->widths = $widths;
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
        $extraPaddingAtBeginningOfLine = 0
    ) {

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
        $this->removeColumns($shortColWidths->keys());
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

        return new DataCellWidths($shortColWidths);
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
     * Need to think about the name of this function a bit.
     * Maybe second parameter is just a column count.
     */
    public function adjustMinimumWidths($availableWidth, DataCellWidths $dataCellWidths)
    {
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

        // Take off the last column, and calculate proportional weights
        // for the first N-1 columns.
        array_pop($widths);
        foreach ($widths as $key => $width) {
            $result[$key] = round(($width / $totalWidth) * $availableWidth);
        }

        // Give the last column the rest of the available width
        $usedWidth = $this->sumWidth($result);
        $result[$lastColumn] = $availableWidth - $usedWidth;

        return new DataCellWidths($result);
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
     * Set the length of the specified column.
     */
    public function setWidth($key, $width)
    {
        $this->widths[$key] = $width;
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
     * Return true if there is no data in this object
     */
    public function isEmpty()
    {
        return empty($this->widths);
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
     * Ensure that every item in $widths that has a corresponding entry
     * in $minimumWidths is as least as large as the minimum value held there.
     */
    public function enforceMinimums($minimumWidths)
    {
        $result = [];
        if ($minimumWidths instanceof DataCellWidths) {
            $minimumWidths = $minimumWidths->widths();
        }
        $minimumWidths += $this->widths;

        foreach ($this->widths as $key => $value) {
            $result[$key] = min($value, $minimumWidths[$key]);
        }

        return new DataCellWidths($result);
    }

    /**
     * Combine this set of widths with another set, and return
     * a new set that contains the entries from both.
     */
    public function combine(DataCellWidths $combineWith)
    {
        $combined = array_merge($combineWith->widths(), $this->widths());

        return new DataCellWidths($combined);
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
