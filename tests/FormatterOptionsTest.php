<?php

namespace Consolidation\OutputFormatters\Tests;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FormatterOptionsTests extends TestCase
{
    /**
     * @param $testCommandline
     * @return StringInput
     */
    public function createStringInput($testCommandline)
    {
        $input = new StringInput($testCommandline);
        $optionDefinitions = [
            new InputArgument('unused', InputArgument::REQUIRED),
            new InputOption(FormatterOptions::FORMAT, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::TABLE_STYLE, null, InputOption::VALUE_REQUIRED),
            new InputOption(FormatterOptions::FIELD, null, InputOption::VALUE_REQUIRED),
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

    /**
     * @param FormatterOptions $options
     * @param $defaults
     * @return mixed
     */
    protected function getFormat(FormatterOptions $options, $defaults = [])
    {
        return $options->getFormat($defaults);
        // return $options->get(FormatterOptions::FORMAT, [], $options->get(FormatterOptions::DEFAULT_FORMAT, $defaults, ''));
    }

    /**
     * @test
     * @return void
     */
    public function testFormatterOptions()
    {
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
        $this->assertEquals('table', $this->getFormat($options));
        $this->assertEquals('table', $this->getFormat($options, $defaults));

        // Override has higher priority than configuration and defaults
        $options = new FormatterOptions($configurationData, $userOptions);
        $newOptions = $options->override([FormatterOptions::DEFAULT_FORMAT => 'json']);
        $this->assertEquals('json', $this->getFormat($newOptions));
        $this->assertEquals('json', $this->getFormat($newOptions, $defaults));

        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setConfigurationDefault(FormatterOptions::DEFAULT_FORMAT, 'php');
        $this->assertEquals('table', $this->getFormat($options));

        $options = new FormatterOptions($configurationData, $userOptions);
        $options->setConfigurationData([]);
        $this->assertEquals('', $this->getFormat($options));

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

    public function testFormatterOptionsFields()
    {
        $configurationData = [
            FormatterOptions::DEFAULT_TABLE_FIELDS => 'a,b,c',
            FormatterOptions::DEFAULT_FIELDS => 'a,b,c,d',
            FormatterOptions::FIELD_LABELS => [
                'a' => 'First',
                'b' => 'Second',
                'c' => 'Third',
                'd' => 'Fourth'
            ],
        ];

        $userOptions = [];

        $options = new FormatterOptions($configurationData, $userOptions);

        $this->assertEquals('a,b,c,d', $options->fields());

        $this->assertEquals('a', $this->callProtected($options, 'fieldAlias', ['First']));
        $this->assertEquals('First', $this->callProtected($options, 'fieldAlias', ['a']));

        $this->assertTrue($options->fieldsContain('a'));
        $this->assertTrue($options->fieldsContain('First'));

        $this->assertTrue($options->fieldsContain('c'));
        $this->assertTrue($options->fieldsContain('Third'));

        $this->assertTrue($options->fieldsContain('d'));
        $this->assertTrue($options->fieldsContain('Fourth'));

        $options->setHumanReadable(true);

        $this->assertEquals('a,b,c', $options->fields());

        $this->assertTrue($options->fieldsContain('a'));
        $this->assertTrue($options->fieldsContain('First'));

        $this->assertTrue($options->fieldsContain('c'));
        $this->assertTrue($options->fieldsContain('Third'));

        $this->assertFalse($options->fieldsContain('d'));
        $this->assertFalse($options->fieldsContain('Fourth'));

        $userOptions = [
            FormatterOptions::FIELDS => 'a,b',
        ];

        $options = new FormatterOptions($configurationData, $userOptions);

        $this->assertTrue($options->fieldsContain('a'));
        $this->assertTrue($options->fieldsContain('First'));

        $this->assertFalse($options->fieldsContain('c'));
        $this->assertFalse($options->fieldsContain('Third'));

        $this->assertFalse($options->fieldsContain('d'));
        $this->assertFalse($options->fieldsContain('Fourth'));

        $options->setHumanReadable(true);

        $this->assertTrue($options->fieldsContain('a'));
        $this->assertTrue($options->fieldsContain('First'));

        $this->assertFalse($options->fieldsContain('c'));
        $this->assertFalse($options->fieldsContain('Third'));

        $this->assertFalse($options->fieldsContain('d'));
        $this->assertFalse($options->fieldsContain('Fourth'));


    }

    function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }
}
