<?php

namespace Consolidation\OutputFormatters\Tests;

use Consolidation\OutputFormatters\FormatterManager;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class IncompatibleDataTests extends TestCase
{
    /**
     * @var FormatterManager
     */
    protected $formatterManager;

    /**
     * @setUp
     * @return void
     */
    function setUp(): void
    {
        $this->formatterManager = new FormatterManager();
    }

    /**
     * @param $expected
     * @param $formatter
     * @param $data
     * @return void
     */
    protected function assertIncompatibleDataMessage($expected, $formatter, $data)
    {
        $e = new IncompatibleDataException($formatter, $data, $formatter->validDataTypes());
        $this->assertEquals($expected, $e->getMessage());
    }

    /**
     * @test
     * @return void
     */
    public function testIncompatibleData()
    {
        $tableFormatter = $this->formatterManager->getFormatter('table');

        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\PropertyList. Instead, a string was provided.', $tableFormatter, 'a string');
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\PropertyList. Instead, an instance of Consolidation\OutputFormatters\FormatterManager was provided.', $tableFormatter, $this->formatterManager);
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\PropertyList. Instead, an array was provided.', $tableFormatter, []);
        $this->assertIncompatibleDataMessage('Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\PropertyList. Instead, an instance of Consolidation\OutputFormatters\StructuredData\PropertyList was provided.', $tableFormatter, new PropertyList([]));
    }

    /**
     * @test
     * @return void
     */
    public function testUndescribableData()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage("Undescribable data error: NULL");

        $tableFormatter = $this->formatterManager->getFormatter('table');
        $data = $tableFormatter->validate(null);
        $this->assertEquals('Will throw before comparing.', $data);
    }

    /**
     * @test
     * @return void
     */
    public function testInvalidTableData()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage("Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\PropertyList. Instead, a string was provided.");

        $tableFormatter = $this->formatterManager->getFormatter('table');
        $data = $tableFormatter->validate('bad data type');
        $this->assertEquals('Will throw before comparing.', $data);
    }

    /**
     * @test
     * @return void
     */
    public function testInvalidSectionsData()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage("Data provided to Consolidation\OutputFormatters\Formatters\SectionsFormatter must be an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields. Instead, a string was provided.");

        $tableFormatter = $this->formatterManager->getFormatter('sections');
        $data = $tableFormatter->validate('bad data type');
        $this->assertEquals('Will throw before comparing.', $data);
    }
}
