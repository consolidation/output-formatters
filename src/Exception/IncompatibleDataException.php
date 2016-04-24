<?php
namespace Consolidation\OutputFormatters\Exception;

use Consolidation\OutputFormatters\FormatterInterface;

/**
 * Represents an incompatibility between the output data and selected formatter.
 */
class IncompatibleDataException extends \Exception
{
    public function __construct(FormatterInterface $formatter, $data, $allowedTypes)
    {
        $formatterDescription = get_class($formatter);
        $dataDescription = static::describeDataType($data);
        $allowedTypesDescription = static::describeAllowedTypes($allowedTypes);
        $message = "Data provided to $formatterDescription must be $allowedTypesDescription. Instead, $dataDescription was provided.";
        parent::__construct($message, 1);
    }

    protected static function describeDataType($data)
    {
        if ($data instanceof \ReflectionClass) {
            return 'an instance of ' . $data->getName();
        }
        if (is_object($data)) {
            return 'an instance of ' . get_class($data);
        }
        if (is_array($data)) {
            return 'an array';
        }
        if (is_string($data)) {
            return 'a string';
        }
        return '<' . var_export($data) . '>';
    }

    protected static function describeAllowedTypes($allowedTypes)
    {
        if (is_array($allowedTypes) && !empty($allowedTypes)) {
            if (count($allowedTypes > 1)) {
                return static::describeListOfAllowedTypes($allowedTypes);
            }
            $allowedTypes = $allowedTypes[0];
        }
        return static::describeDataType($allowedTypes);
    }

    protected static function describeListOfAllowedTypes($allowedTypes)
    {
        $descriptions = [];
        foreach ($allowedTypes as $oneAllowedType) {
            $descriptions[] = static::describeDataType($oneAllowedType);
        }
        if (count($descriptions) == 2) {
            return "either {$descriptions[0]} or {$descriptions[1]}";
        }
        $lastDescription = array_pop($descriptions);
        $otherDescriptions = implode(', ', $descriptions);
        return "one of $otherDescriptions or $lastDescription";
    }
}
