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

    function assertFormattedOutputMatches($expected, $format, $data, $annotationData = [], $options = []) {
        $output = new BufferedOutput();
        $formatter = $this->formatterManager->getFormatter($format, $annotationData);
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

    function testSimpleTable()
    {
        $data = [
            [
                'one' => 'a',
                'two' => 'b',
                'three' => 'c',
            ],
            [
                'one' => 'x',
                'two' => 'y',
                'three' => 'z',
            ],
        ];

        $expected = <<<EOT
+-----+-----+-------+
| one | two | three |
+-----+-----+-------+
| a   | b   | c     |
| x   | y   | z     |
+-----+-----+-------+
EOT;

        $this->assertFormattedOutputMatches($expected, 'table', $data);
    }

    function testSimpleTableWithFieldLabels()
    {
        $data = [
            [
                'one' => 'a',
                'two' => 'b',
                'three' => 'c',
            ],
            [
                'one' => 'x',
                'two' => 'y',
                'three' => 'z',
            ],
        ];

        $expected = <<<EOT
+------+----+-----+
| Ichi | Ni | San |
+------+----+-----+
| a    | b  | c   |
| x    | y  | z   |
+------+----+-----+
EOT;

        $expectedWithReorderedFields = <<<EOT
+-----+------+
| San | Ichi |
+-----+------+
| c   | a    |
| z   | x    |
+-----+------+
EOT;

        $annotationData = [
            'field-labels' => ['one' => 'Ichi', 'two' => 'Ni', 'three' => 'San'],
        ];
        $this->assertFormattedOutputMatches($expected, 'table', $data, $annotationData);
        $this->assertFormattedOutputMatches($expectedWithReorderedFields, 'table', $data, $annotationData, ['fields' => ['three', 'one']]);
    }
}
