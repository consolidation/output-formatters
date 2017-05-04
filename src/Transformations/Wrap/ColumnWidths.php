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
    public function __construct()
    {
    }

    /**
     * Given the total amount of available space, and the width of
     * the columns to place, calculate the optimum column widths to use.
     */
    public function calculate($availableWidth, DataCellWidths $dataWidths, DataCellWidths $minimumWidths)
    {
        // First, check to see if all columns will fit at their full widths.
        // If so, do no further calculations. (This may be redundant with
        // the short column width calculation.)
        if ($dataWidths->totalWidth() <= $availableWidth) {
            return $dataWidths->enforceMinimums($minimumWidths);
        }

        // Get the short columns first. If there are none, then distribute all
        // of the available width among the remaining columns.
        $shortColWidths = $this->getShortColumns($availableWidth, $dataWidths, $minimumWidths);
        if ($shortColWidths->isEmpty()) {
            return $this->distributeLongColumns($availableWidth, $dataWidths, $minimumWidths);
        }

        // If some short columns were removed, then account for the length
        // of the removed columns and make a recursive call (since the average
        // width may be higher now, if the removed columns were shorter in
        // length than the previous average).
        $availableWidth -= $shortColWidths->totalWidth();
        $remainingColWidths = $this->calculate($availableWidth, $dataWidths, $minimumWidths);

        return $shortColWidths->combine($remainingColWidths);
    }

    /**
     * Return all of the columns whose longest line length is less than or
     * equal to the average width.
     */
    public function getShortColumns($availableWidth, DataCellWidths $dataWidths, DataCellWidths $minimumWidths)
    {
        $averageWidth = $this->averageWidth($availableWidth, $dataWidths);
        $shortColWidths = $dataWidths->removeShortColumns($averageWidth);
        return $shortColWidths->enforceMinimums($minimumWidths);
    }

    /**
     * Distribute the remainig space among the columns that were not
     * included in the list of "short" columns.
     */
    public function distributeLongColumns($availableWidth, DataCellWidths $dataWidths, DataCellWidths $minimumWidths)
    {
        // Just distribute the remainder without regard to the
        // minimum widths. For now.
        return $dataWidths->distribute($availableWidth);
/*
        // Check to see if there is only one column remaining.
        // If so, give it all of the remaining width.
        $remainingWidths = $dataWidths->widths();
        if (count($remainingWidths) <= 1) {
            return $remainingWidths;
        }

        // Start by giving each column its minimum width
        $result = $minimumWidths;
        $availableWidth -= DataCellWidths::sumWidth($result);


        return $result;
*/
    }

    /**
     * Calculate how much space is available on average for all columns.
     */
    public function averageWidth($availableWidth, DataCellWidths $dataWidths)
    {
        $colkeys = $dataWidths->keys();
        return $availableWidth / count($colkeys);
    }
}
