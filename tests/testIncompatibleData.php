<?php
namespace Consolidation\OutputFormatters;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\AssociativeList;
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;

class IncompatibleDataTests extends \PHPUnit_Framework_TestCase
{
    protected $formatterManager;

    function setup() {
        $this->formatterManager = new FormatterManager();
    }

    protected function assertIncompatibleDataMessage($expected, $formatter, $data)
    {
        $e = new IncompatibleDataException($formatter, $data, $formatter->validDataTypes());
        $this->assertEquals($expected, $e->getMessage());
    }

    public function testIncompatibleData()
    {
        $tableFormatter = $this->formatterManager->getFormatter('table');

        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, a string was provided.', $tableFormatter, 'a string');
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, an instance of Consolidation\OutputFormatters\FormatterManager was provided.', $tableFormatter, $this->formatterManager);
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, an array was provided.', $tableFormatter, []);
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList was provided.', $tableFormatter, new AssociativeList([]));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Undescribable data error: NULL
     */
    public function testUndescribableData()
    {
        $tableFormatter = $this->formatterManager->getFormatter('table');
        $this->assertIncompatibleDataMessage('Will throw before comparing.', $tableFormatter, null);
    }
}
