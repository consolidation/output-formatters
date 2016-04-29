<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

class FormatterOptionsTests extends \PHPUnit_Framework_TestCase
{
    public function createStringInput($testCommandline)
    {
        $input = new StringInput($testCommandline);
        $optionDefinitions = [
            new InputArgument('unused', InputArgument::REQUIRED),
            new InputOption(FormatterOptions::FORMAT, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::TABLE_STYLE, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::FIELDS, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::INCLUDE_FIELD_LABELS, null, InputOption::VALUE_NONE),
            new InputOption(FormatterOptions::ROW_LABELS, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::FIELD_LABELS, null, InputOption::VALUE_REQUIRED),
            // These probably don't make senes to alter via options
            new InputOption(FormatterOptions::DEFAULT_FORMAT, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::DEFAULT_FIELDS, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::LIST_ORIENTATION, null, InputOption::VALUE_NONE),
        ];
        $definition = new InputDefinition($optionDefinitions);
        $input->bind($definition);
        return $input;
    }

    public function testFormatterOptions() {
        $configurationData = [
            FormatterOptions::DEFAULT_FORMAT => 'table',
            'test' => 'one',
            'try' => 'two',
        ];
        $userOptions = [
            'try' => 'three',
        ];
        $defaults = [
            FormatterOptions::DEFAULT_FORMAT => 'var_export',
            'try' => 'four',
            'default-only' => 'defaulty',
        ];

        // Create a StringInput object and ensure that Symfony Console is working right.
        $input = $this->createStringInput('test --format=yaml --include-field-labels');
        $testValue = $input->getOption(FormatterOptions::INCLUDE_FIELD_LABELS);
        $this->assertTrue($testValue);
        $testValue = $input->getOption(FormatterOptions::FORMAT);
        $this->assertEquals('yaml', $testValue);

        // $options->get() only returns the default parameter is there is
        // no matching key in configuration, userOptions or defaults.
        $options = new FormatterOptions($configurationData, $userOptions);
        $this->assertEquals('', $options->get('default-only'));
        $this->assertEquals('defaulty', $options->get('default-only', $defaults));
        $this->assertEquals('defaulty', $options->get('default-only', $defaults, 'irrelevant'));
        $this->assertEquals('three', $options->get('try'));
        $this->assertEquals('three', $options->get('try', $defaults));
        $this->assertEquals('three', $options->get('try', $defaults, 'irrelevant'));
        $this->assertFalse($options->get('no-such-key'));
        $this->assertFalse($options->get('no-such-key', $defaults));
        $this->assertEquals('last-chance', $options->get('no-such-key', $defaults, 'last-chance'));

        // Change a user option
        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setOption('try', 'changed');
        $this->assertEquals('changed', $options->get('try'));
        $this->assertEquals('changed', $options->get('try', $defaults));
        $this->assertEquals('changed', $options->get('try', $defaults, 'irrelevant'));

        // Configuration has higher priority than defaults
        $options = new FormatterOptions($configurationData, $userOptions);
        $this->assertEquals('table', $options->getFormat());
        $this->assertEquals('table', $options->getFormat($defaults));

        // Override has higher priority than configuration and defaults
        $options = new FormatterOptions($configurationData, $userOptions);
        $newOptions = $options->override([FormatterOptions::DEFAULT_FORMAT => 'json']);
        $this->assertEquals('json', $newOptions->getFormat());
        $this->assertEquals('json', $newOptions->getFormat($defaults));

        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setConfigurationDefault(FormatterOptions::DEFAULT_FORMAT, 'php');
        $this->assertEquals('table', $options->getFormat());

        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setConfigurationData([]);
        $this->assertEquals('', $options->getFormat());

        // It is only possible to override options that appear in '$default'
        // with $input; if there are no defaults, then the --format=yaml
        // option will not be picked up.
        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setInput($input);
        $this->assertEquals('table', $options->get(FormatterOptions::DEFAULT_FORMAT));
        $this->assertEquals('table', $options->get(FormatterOptions::DEFAULT_FORMAT, $defaults, 'irrelevant'));

        // We won't see the default value unless the configuration value is empty.
        $options = new FormatterOptions([], $userOptions);
        $this->assertEquals('var_export', $options->get(FormatterOptions::DEFAULT_FORMAT, $defaults, 'irrelevant'));
    }
}
