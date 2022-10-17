<?php

namespace Consolidation\OutputFormatters\Tests;

/**
 * Test Util that strips extra whitespace from
 * the lines of a multi-line string.
 */
trait OutputNormalizerTrait
{
    /**
     * @param $string
     * @return string
     */
    protected function filterForTrailingWhitespace($string)
    {
        $lines = explode(PHP_EOL, $string);
        return trim(join("\n", array_map(function ($incoming) {
            return trim($incoming, "\n\t ");
        }, $lines)), "\n ");
    }
}
