<?php
namespace Consolidation\OutputFormatters;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\AssociativeList;

class ValidFormatsTests extends \PHPUnit_Framework_TestCase
{
    protected $formatterManager;

    function setup() {
        $this->formatterManager = new FormatterManager();
    }

    function testValidFormats()
    {
        $arrayObjectRef = new \ReflectionClass('\ArrayObject');
        $associativeListRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\AssociativeList');
        $rowsOfFieldsRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields');
        $notADataType = new \ReflectionClass('\Consolidation\OutputFormatters\FormatterManager');

        $jsonFormatter = $this->formatterManager->getFormatter('json');
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, $notADataType);
        $this->assertFalse($isValid);
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, new \ArrayObject());
        $this->assertTrue($isValid);
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, $arrayObjectRef);
        $this->assertTrue($isValid);
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, []);
        $this->assertTrue($isValid);
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, $associativeListRef);
        $this->assertTrue($isValid);
        $isValid = $this->formatterManager->isValidFormat($jsonFormatter, $rowsOfFieldsRef);
        $this->assertTrue($isValid);

        $sectionsFormatter = $this->formatterManager->getFormatter('sections');
        $isValid = $this->formatterManager->isValidFormat($sectionsFormatter, $notADataType);
        $this->assertFalse($isValid);
        $isValid = $this->formatterManager->isValidFormat($sectionsFormatter, []);
        $this->assertFalse($isValid);
        $isValid = $this->formatterManager->isValidFormat($sectionsFormatter, $arrayObjectRef);
        $this->assertFalse($isValid);
        $isValid = $this->formatterManager->isValidFormat($sectionsFormatter, $rowsOfFieldsRef);
        $this->assertTrue($isValid);
        $isValid = $this->formatterManager->isValidFormat($sectionsFormatter, $associativeListRef);
        $this->assertFalse($isValid);

        // Check to see which formats can handle a simple array
        $validFormats = $this->formatterManager->validFormats([]);
        $this->assertEquals('csv,json,list,php,print-r,var_export,xml,yaml', implode(',', $validFormats));

        // Check to see which formats can handle an AssociativeList
        $validFormats = $this->formatterManager->validFormats($associativeListRef);
        $this->assertEquals('csv,json,list,php,print-r,table,var_export,xml,yaml', implode(',', $validFormats));

        // Check to see which formats can handle an RowsOfFields
        $validFormats = $this->formatterManager->validFormats($rowsOfFieldsRef);
        $this->assertEquals('csv,json,list,php,print-r,sections,table,var_export,xml,yaml', implode(',', $validFormats));

        // Test error case: no formatter should handle something that is not a data type.
        $validFormats = $this->formatterManager->validFormats($notADataType);
        $this->assertEquals('', implode(',', $validFormats));
    }
}
