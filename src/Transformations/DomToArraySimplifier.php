<?php
namespace Consolidation\OutputFormatters\Transformations;

use Consolidation\OutputFormatters\SimplifyToArrayInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\Xml\DomDataInterface;
use Consolidation\OutputFormatters\StructuredData\Xml\XmlSchema;

/**
 * Simplify a DOMDocument to an array.
 */
class DomToArraySimplifier implements SimplifyToArrayInterface
{
    public function __construct()
    {
    }

    public function simplifyToArray($structuredData, FormatterOptions $options)
    {
        if ($structuredData instanceof DomDataInterface) {
            $structuredData = $structuredData->getDomData();
        }
        if ($structuredData instanceof \DOMDocument) {
            // $schema = $options->getXmlSchema();
            $simplified = $this->elementToArray($structuredData);
            $structuredData = array_shift($simplified);
        }
        return $structuredData;
    }

    protected function elementToArray(\DOMNode $element)
    {
        if ($element->nodeType == XML_TEXT_NODE) {
            return $element->nodeValue;
        }
        $attributes = $this->getNodeAttributes($element);
        $children = $this->getNodeChildren($element);

        return array_merge($attributes, $children);
    }

    protected function getNodeAttributes($element)
    {
        if (empty($element->attributes)) {
            return [];
        }
        $attributes = [];
        foreach ($element->attributes as $key => $attribute) {
            $attributes[$key] = $attribute->nodeValue;
        }
        return $attributes;
    }

    protected function getNodeChildren($element)
    {
        if (empty($element->childNodes)) {
            return [];
        }
        $result = $this->getNodeChildrenUnique($element);
        if ($this->hasUniformChildren($result)) {
            $result = $this->simplifyUniformChildren($element->nodeName, $result);
        } else {
            $result = $this->simplifyUniqueChildren($result);
        }
        return $result;
    }

    protected function getNodeChildrenUnique($element)
    {
        $children = [];
        foreach ($element->childNodes as $key => $value) {
            $children[$key] = $this->elementToArray($value);
        }
        if ((count($children) == 1) && (is_string($children[0]))) {
            return [$element->nodeName => $children[0]];
        }
        return $children;
    }

    protected function hasUniformChildren($data)
    {
        $last = false;
        foreach ($data as $key => $value) {
            $name = $this->getNameOfSingleResultElement($key, $value);
            if (!$name) {
                return false;
            }
            if ($last && ($name != $last)) {
                return false;
            }
            $last = $name;
        }
        return true;
    }

    protected function simplifyUniformChildren($parentKey, $data)
    {
        $simplifiedChildren = [];
        foreach ($data as $key => $value) {
            $simplifiedChildren[$parentKey][] = array_shift($value);
        }
        return $simplifiedChildren;
    }

    protected function simplifyUniqueChildren($data)
    {
        $simplifiedChildren = [];
        foreach ($data as $key => $value) {
            if (is_numeric($key) && is_array($value) && (count($value) == 1)) {
                $valueKeys = array_keys($value);
                $key = $valueKeys[0];
                $value = array_shift($value);
            }
            if (array_key_exists($key, $simplifiedChildren)) {
                throw new \Exception("Cannot convert data from a DOM document to an array, because <$key> appears more than once, and is not wrapped in a <{$key}s> element.");
            }
            $simplifiedChildren[$key] = $value;
        }
        return $simplifiedChildren;
    }

    protected function getNameOfSingleResultElement($key, $value)
    {
        if (!is_numeric($key)) {
            return false;
        }
        if (!is_array($value)) {
            return false;
        }
        if (count($value) != 1) {
            return false;
        }
        $valueKeys = array_keys($value);
        return $valueKeys[0];
    }
}
