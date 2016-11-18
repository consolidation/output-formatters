<?php
namespace Consolidation\OutputFormatters\Transformations;

use Symfony\Component\Console\Helper\TableStyle;

class WordWrapper
{
    protected $width;

    // For now, hardcode these to match what the Symfony Table helper does.
    // Note that these might actually need to be adjusted depending on the
    // table style.
    protected $extraPaddingAtBeginningOfLine = 0;
    protected $extraPaddingAtEndOfLine = 0;
    protected $paddingInEachCell = 3;

    public function __construct($width)
    {
        $this->width = $width;
    }

    /**
     * Calculate our padding widths from the specified table style.
     * @param TableStyle $style
     */
    public function setPaddingFromStyle(TableStyle $style)
    {
        $verticalBorderLen = strlen($style->getVerticalBorderChar());
        $paddingLen = strlen($style->getPaddingChar());

        $this->extraPaddingAtBeginningOfLine = 0;
        $this->extraPaddingAtEndOfLine = $verticalBorderLen;
        $this->paddingInEachCell = $verticalBorderLen + $paddingLen + 1;
    }

    /**
     * Wrap the cells in each part of the provided data table
     * @param array $rows
     * @return array
     */
    public function wrap($rows, $widths = [])
    {
        // If the width was not set, then disable wordwrap.
        if (!$this->width) {
            return $rows;
        }

        // Calculate the column widths to use based on the content.
        $auto_widths = $this->columnAutowidth($rows, $widths);

        // Do wordwrap on all cells.
        $newrows = array();
        foreach ($rows as $rowkey => $row) {
            foreach ($row as $colkey => $cell) {
                $newrows[$rowkey][$colkey] = $this->wrapCell($cell, $auto_widths[$colkey]);
            }
        }

        return $newrows;
    }

    /**
     * Wrap one cell.  Guard against modifying non-strings and
     * then call through to wordwrap().
     *
     * @param mixed $cell
     * @param string $cellWidth
     * @return mixed
     */
    protected function wrapCell($cell, $cellWidth)
    {
        if (!is_string($cell)) {
            return $cell;
        }
        return wordwrap($cell, $cellWidth, "\n", true);
    }

    /**
     * Determine the best fit for column widths. Ported from Drush.
     *
     * @param array $rows The rows to use for calculations.
     * @param array $widths Manually specified widths of each column
     *   (in characters) - these will be left as is.
     */
    protected function columnAutowidth($rows, $widths)
    {
        $auto_widths = $widths;

        // First we determine the distribution of row lengths in each column.
        // This is an array of descending character length keys (i.e. starting at
        // the rightmost character column), with the value indicating the number
        // of rows where that character column is present.
        $col_dist = array();
        foreach ($rows as $rowkey => $row) {
            foreach ($row as $col_id => $cell) {
                if (empty($widths[$col_id])) {
                    $length = strlen($cell);
                    if ($length == 0) {
                        $col_dist[$col_id][0] = 0;
                    }
                    while ($length > 0) {
                        if (!isset($col_dist[$col_id][$length])) {
                            $col_dist[$col_id][$length] = 0;
                        }
                        $col_dist[$col_id][$length]++;
                        $length--;
                    }
                }
            }
        }
        foreach ($col_dist as $col_id => $count) {
            // Sort the distribution in decending key order.
            krsort($col_dist[$col_id]);
            // Initially we set all columns to their "ideal" longest width
            // - i.e. the width of their longest column.
            $auto_widths[$col_id] = max(array_keys($col_dist[$col_id]));
        }

        // We determine what width we have available to use, and what width the
        // above "ideal" columns take up.
        $available_width = $this->width - ($this->extraPaddingAtBeginningOfLine + $this->extraPaddingAtEndOfLine + (count($auto_widths) * $this->paddingInEachCell));
        $auto_width_current = array_sum($auto_widths);

        // If we need to reduce a column so that we can fit the space we use this
        // loop to figure out which column will cause the "least wrapping",
        // (relative to the other columns) and reduce the width of that column.
        while ($auto_width_current > $available_width) {
            $count = 0;
            $width = 0;
            foreach ($col_dist as $col_id => $counts) {
                // If we are just starting out, select the first column.
                if ($count == 0 ||
                 // OR: if this column would cause less wrapping than the currently
                 // selected column, then select it.
                 (current($counts) < $count) ||
                 // OR: if this column would cause the same amount of wrapping, but is
                 // longer, then we choose to wrap the longer column (proportionally
                 // less wrapping, and helps avoid triple line wraps).
                 (current($counts) == $count && key($counts) > $width)) {
                    // Select the column number, and record the count and current width
                    // for later comparisons.
                    $column = $col_id;
                    $count = current($counts);
                    $width = key($counts);
                }
            }
            if ($width <= 1) {
                // If we have reached a width of 1 then give up, so wordwrap can still progress.
                break;
            }
            // Reduce the width of the selected column.
            $auto_widths[$column]--;
            // Reduce our overall table width counter.
            $auto_width_current--;
            // Remove the corresponding data from the disctribution, so next time
            // around we use the data for the row to the left.
            unset($col_dist[$column][$width]);
        }
        return $auto_widths;
    }
}
