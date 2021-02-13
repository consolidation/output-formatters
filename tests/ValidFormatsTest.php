<?php
namespace Consolidation\OutputFormatters;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use PHPUnit\Framework\TestCase;

class ValidFormatsTest extends TestCase
{
    protected $formatterManager;

    function setup(): void {
        $this->formatterManager = new FormatterManager();
        $this->formatterManager->addDefaultFormatters();
        $this->formatterManager->addDefaultSimplifiers();
    }

    function testValidFormats()
    {
        $arrayObjectRef = new \ReflectionClass('\ArrayObject');
        $associativeListRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\PropertyList');
        $rowsOfFieldsRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields');
        $unstructuredDataRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\UnstructuredData');
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
        $this->assertEquals('csv,json,list,null,php,print-r,string,tsv,var_dump,var_export,xml,yaml', implode(',', $validFormats));

        // Check to see which formats can handle an PropertyList
        $validFormats = $this->formatterManager->validFormats($associativeListRef);
        $this->assertEquals('csv,json,list,null,php,print-r,string,table,tsv,var_dump,var_export,xml,yaml', implode(',', $validFormats));

        // Check to see which formats can handle an RowsOfFields
        $validFormats = $this->formatterManager->validFormats($rowsOfFieldsRef);
        $this->assertEquals('csv,json,list,null,php,print-r,sections,string,table,tsv,var_dump,var_export,xml,yaml', implode(',', $validFormats));

        // Check to see which formats can handle unstructured data
        $validFormats = $this->formatterManager->validFormats($unstructuredDataRef);
        $this->assertEquals('csv,json,list,null,php,print-r,tsv,var_dump,var_export,xml,yaml', implode(',', $validFormats));

        // TODO: it woud be better if this returned an empty set instead of 'string'.
        $validFormats = $this->formatterManager->validFormats($notADataType);
        $this->assertEquals('null,string', implode(',', $validFormats));
    }

    function testAutomaticOptions()
    {
        $rowsOfFieldsRef = new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields');
        $formatterOptions = new FormatterOptions(
            [
                FormatterOptions::FIELD_LABELS => "name: Name\nphone_number: Phone Number",
            ]
        );
        $inputOptions = $this->formatterManager->automaticOptions($formatterOptions, $rowsOfFieldsRef);
        $this->assertInputOptionDescriptionsEquals("Format the result data. Available formats: csv,json,list,null,php,print-r,sections,string,table,tsv,var_dump,var_export,xml,yaml [Default: 'table']\nAvailable fields: Name (name), Phone Number (phone_number) [Default: '']\nSelect just one field, and force format to *string*. [Default: '']", $inputOptions);
    }

    function assertInputOptionDescriptionsEquals($expected, $inputOptions)
    {
        $descriptions = [];
        foreach ($inputOptions as $inputOption) {
            $descriptions[] = $inputOption->getDescription() . " [Default: '" . $inputOption->getDefault() . "']";
        }
        $this->assertEquals($expected, implode("\n", $descriptions));
    }
}
