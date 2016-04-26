<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Input\InputInterface;
use Consolidation\OutputFormatters\Transformations\PropertyParser;

/**
 * FormetterOptions holds information that affects the way a formatter
 * renders its output.
 *
 * There are three places where a formatter might get options from:
 *
 * 1. Configuration associated with the command that produced the output.
 *    This is passed in to FormatterManager::write() along with the data
 *    to format.  It might originally come from annotations on the command,
 *    or it might come from another source.  Examples include the field labels
 *    for a table, or the default list of fields to display.
 *
 * 2. Options specified by the user, e.g. by commandline options.
 *
 * 3. Default values associated with the formatter itself.
 *
 * This class caches configuration from sources (1) and (2), and expects
 * to be provided the defaults, (3), whenever a value is requested.
 */
class FormatterOptions
{
    /** var array */
    protected $configurationData = [];
    /** var array */
    protected $options = [];
    /** var InputInterface */
    protected $input;

    public function __construct($configurationData = [], $options = [])
    {
        $this->configurationData = $configurationData;
        $this->options = $options;
    }

    public function override($configurationData)
    {
        $override = new self();
        $override
            ->setConfigurationData($configurationData + $this->getConfigurationData())
            ->setOptions($this->getOptions());
        return $override;
    }

    public function get($key, $defaults = [], $default = false)
    {
        $value = $this->fetch($key, $defaults, $default);
        return $this->parse($key, $value);
    }

    protected function fetch($key, $defaults = [], $default = false)
    {
        $values = array_merge(
            $defaults,
            $this->getConfigurationData(),
            $this->getOptions(),
            $this->getInputOptions($defaults)
        );
        if (array_key_exists($key, $values)) {
            return $values[$key];
        }
        return $default;
    }

    protected function parse($key, $value)
    {
        $optionFormat = $this->getOptionFormat($key);
        if ($optionFormat) {
            return $this->$optionFormat($value);
        }
        return $value;
    }

    public function parsePropertyList($value)
    {
        return PropertyParser::parse($value);
    }

    protected function getOptionFormat($key)
    {
        $propertyFormats = [
            'row-labels' => 'PropertyList',
            'field-labels' => 'PropertyList',
            'default-fields' => 'PropertyList',
        ];
        if (array_key_exists($key, $propertyFormats)) {
            return "parse{$propertyFormats[$key]}";
        }
        return false;
    }

    public function setConfigurationData($configurationData)
    {
        $this->configurationData = $configurationData;
        return $this;
    }

    public function setConfigurationValue($key, $value)
    {
        $this->configurationData[$key] = $value;
        return $this;
    }

    public function setConfigurationDefault($key, $value)
    {
        if (!array_key_exists($key, $this->configurationData)) {
            return $this->setConfigurationValue($key, $value);
        }
        return $this;
    }

    public function getConfigurationData()
    {
        return $this->configurationData;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    public function getInputOptions($defaults)
    {
        if (!isset($this->input)) {
            return [];
        }
        $options = [];
        foreach ($defaults as $key => $value) {
            if ($this->input->hasOption($key)) {
                $options[$key] = $this->input->getOption($key);
            }
        }
        return $options;
    }
}
