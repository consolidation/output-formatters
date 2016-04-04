<?php
namespace Consolidation\OutputFormatters;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class FormattersTests extends \PHPUnit_Framework_TestCase
{
    protected $formatterManager;

    function setup() {
        $this->formatterManager = new FormatterManager();
        //$this->output = new BufferedOutput();
        //$this->output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        //$this->logger = new Logger($this->output);
    }

    function assertFormattedOutputMatches($expected, $format, $data, $options = []) {
        $output = new BufferedOutput();
        $formatter = $this->formatterManager->getFormatter($format);
        $formatter->write($data, $options, $output);
        $actual = trim($output->fetch());
        $this->assertEquals(rtrim($expected), rtrim($actual));
    }

    function testSimpleYaml()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
one: a
two: b
three: c
EOT;

        $this->assertFormattedOutputMatches($expected, 'yaml', $data);
    }

    function testNestedYaml()
    {
        $data = [
            'one' => [
                'i' => ['a', 'b', 'c'],
            ],
            'two' => [
                'ii' => ['q', 'r', 's'],
            ],
            'three' => [
                'iii' => ['t', 'u', 'v'],
            ],
        ];

        $expected = <<<EOT
one:
  i:
    - a
    - b
    - c
two:
  ii:
    - q
    - r
    - s
three:
  iii:
    - t
    - u
    - v
EOT;

        $this->assertFormattedOutputMatches($expected, 'yaml', $data);
    }
}
