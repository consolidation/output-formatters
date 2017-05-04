<?php
namespace Consolidation\OutputFormatters\Transformations\Wrap;

use Symfony\Component\Console\Helper\TableStyle;

/**
 * Calculate column widths for table cells.
 *
 * Influenced by Drush and webmozart/console.
 */
class ColumnWidths
{
    protected $widths;

    public function __construct()
    {
        $this->widths = [];
    }

    /**
     * Given the total amount of available space, and the width of
     * the columns to place, calculate the optimum column widths to use.
     */
    public function calculate($totalWidth, DataCellWidths $dataWidths, $minimumWidths = [])
    {
        // First, check to see if all columns will fit at their full widths.
        // If so, do no further calculations. (This may be redundant with
        // the short column width calculation.)
        $widths = $dataWidths->widths();
        if (DataCellWidths::sumWidth($widths) <= $totalWidth) {
            $widths = $this->enforceMinimums($widths, $minimumWidths);
            return $widths;
        }

        // Get the short columns first. If there are none, then distribute all
        // of the available width among the remaining columns.
        $shortColWidths = $this->getShortColumns($totalWidth, $dataWidths, $minimumWidths);
        if (empty($shortColWidths)) {
            return $this->distributeLongColumns($totalWidth, $dataWidths, $minimumWidths);
        }

        // If some short columns were removed, then account for the length
        // of the removed columns and make a recursive call (since the average
        // width may be higher now, if the removed columns were shorter in
        // length than the previous average).
        $totalWidth -= DataCellWidths::sumWidth($shortColWidths);
        $remainingColWidths = $this->calculate($totalWidth, $dataWidths);

        return array_merge($shortColWidths, $remainingColWidths);
    }

    /**
     * Return all of the columns whose longest line length is less than or
     * equal to the average width.
     */
    public function getShortColumns($totalWidth, DataCellWidths $dataWidths, $minimumWidths)
    {
        $averageWidth = $this->averageWidth($totalWidth, $dataWidths);
        $shortColWidths = $dataWidths->removeShortColumns($averageWidth);
        $shortColWidths = $this->enforceMinimums($shortColWidths, $minimumWidths);
        return $shortColWidths;
    }

    /**
     * Distribute the remainig space among the columns that were not
     * included in the list of "short" columns.
     */
    public function distributeLongColumns($totalWidth, DataCellWidths $dataWidths, $minimumWidths)
    {
        // Just distribute the remainder without regard to the
        // minimum widths. For now.
        return $dataWidths->distribute($totalWidth);
/*
        // Check to see if there is only one column remaining.
        // If so, give it all of the remaining width.
        $remainingWidths = $dataWidths->widths();
        if (count($remainingWidths) <= 1) {
            return $remainingWidths;
        }

        // Start by giving each column its minimum width
        $result = $minimumWidths;
        $totalWidth -= DataCellWidths::sumWidth($result);


        return $result;
*/
    }

    /**
     * Ensure that every item in $widths that has a corresponding entry
     * in $minimumWidths is as least as large as the minimum value held there.
     */
    public function enforceMinimums($widths, $minimumWidths)
    {
        $result = [];
        $minimumWidths += $widths;

        foreach ($widths as $key => $value) {
            $result[$key] = min($value, $minimumWidths[$key]);
        }

        return $result;
    }

    /**
     * Calculate how much space is available on average for all columns.
     */
    public function averageWidth($totalWidth, DataCellWidths $dataWidths)
    {
        $colkeys = $dataWidths->keys();
        return $totalWidth / count($colkeys);
    }
}
