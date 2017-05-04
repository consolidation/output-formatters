<?php
namespace Consolidation\OutputFormatters\Transformations;

use Consolidation\OutputFormatters\Transformations\Wrap\ColumnWidths;
use Consolidation\OutputFormatters\Transformations\Wrap\DataCellWidths;
use Symfony\Component\Console\Helper\TableStyle;

class WordWrapper
{
    protected $width;
    protected $minimumWidths;

    // For now, hardcode these to match what the Symfony Table helper does.
    // Note that these might actually need to be adjusted depending on the
    // table style.
    protected $extraPaddingAtBeginningOfLine = 0;
    protected $extraPaddingAtEndOfLine = 0;
    protected $paddingInEachCell = 3;

    public function __construct($width)
    {
        $this->width = $width;
        $this->minimumWidths = new DataCellWidths();
    }

    /**
     * Calculate our padding widths from the specified table style.
     * @param TableStyle $style
     */
    public function setPaddingFromStyle(TableStyle $style)
    {
        $verticalBorderLen = strlen(sprintf($style->getBorderFormat(), $style->getVerticalBorderChar()));
        $paddingLen = strlen($style->getPaddingChar());

        $this->extraPaddingAtBeginningOfLine = 0;
        $this->extraPaddingAtEndOfLine = $verticalBorderLen;
        $this->paddingInEachCell = $verticalBorderLen + $paddingLen + 1;
    }

    /**
     * If columns have minimum widths, then set them here.
     * @param array $minimumWidths
     */
    public function setMinimumWidths($minimumWidths)
    {
        $this->minimumWidths = new DataCellWidths($minimumWidths);
    }

    /**
     * Set the minimum width of just one column
     */
    public function minimumWidth($colkey, $width)
    {
        $this->minimumWidths->setWidth($colkey, $width);
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

        $dataCellWidths = new DataCellWidths();
        $dataCellWidths->calculateLongestCell($rows);

        $availableWidth = $this->width - $dataCellWidths->paddingSpace($this->paddingInEachCell, $this->extraPaddingAtEndOfLine, $this->extraPaddingAtBeginningOfLine);

        $this->minimumWidths->adjustMinimumWidths($availableWidth, $dataCellWidths);

        $columnWidths = new ColumnWidths();
        $auto_widths = $columnWidths->calculate($availableWidth, $dataCellWidths, $this->minimumWidths);

        // Do wordwrap on all cells.
        $newrows = array();
        foreach ($rows as $rowkey => $row) {
            foreach ($row as $colkey => $cell) {
                $newrows[$rowkey][$colkey] = $this->wrapCell($cell, $auto_widths->width($colkey));
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
}
