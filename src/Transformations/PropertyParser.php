<?php
namespace Consolidation\OutputFormatters\Transformations;

use Symfony\Component\Yaml\Yaml;

/**
 * Transform a string of properties into a PHP associative array.
 *
 * Input:
 *
 *   one: red
 *   two: white
 *   three: blue
 *
 * Output:
 *
 *   [
 *      'one' => 'red',
 *      'two' => 'white',
 *      'three' => 'blue',
 *   ]
 */
class PropertyParser
{
    public static function parse($data)
    {
        if (is_string($data)) {
            $data = Yaml::parse(trim($data));
        }
        return $data;
    }
}
