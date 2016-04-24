# Consolidation\Formatters

Apply transformations to structured data to write output in different formats.

[![Travis CI](https://travis-ci.org/consolidation-org/output-formatters.svg?branch=master)](https://travis-ci.org/consolidation-org/output-formatters) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/consolidation-org/output-formatters/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation-org/output-formatters/?branch=master) [![License](https://poser.pugx.org/consolidation/output-formatters/license)](https://packagist.org/packages/consolidation/output-formatters)

## Component Status

Currently in use in [Robo](https://github.com/Codegyre/Robo).

## Motivation

Formatters are used to allow simple commandline tool commands to be implemented in a manner that is completely independent from the Symfony Console output interfaces.  A command receives its input via its method parameters, and returns its result as structured data (e.g. a php standard object or array).  The structured data is then formatted by a formatter, and the result is printed.

This process is managed by the [Consolidation/AnnotationCommand](https://github.com/consolidation-org/annotation-command) project.

## Example Formatter

Simple formatters are very easy to write.
```
class YamlFormatter implements FormatterInterface
{
    public function write($data, $options, OutputInterface $output)
    {
        $dumper = new Dumper();
        $output->writeln($dumper->dump($data));
    }
}
```
The formatter is passed the set of `$options` that the user provided on the command line. These may optionally be examined to alter the behavior of the formatter, if needed.

## Configuring Formatters

Some formatters take command-specific configuration data; the list of available fields and the default list of fields to display are two common examples.

When configuration data is required, it is provided as annotations on the command method. Formatters that implement ConfigurationAwareInterface will be passed the annotations for the command that requested the formatter.

## Structured Data

Most formatters will operate on any array or ArrayObject data. Some formatters require that specific data types be used. The following data types, all of which are subclasses of ArrayObject, are available for use:

- RowsOfFields: Each row contains an associative array of field:value pairs. It is also assumed that the fields of each row are the same for every row. This format is ideal for displaying in a table, with labels in the top row.
- AssociativeList: Each row contains a field:value pair. Each field is unique. This format is ideal for displaying in a table, with labels in the first column and values in the second common.

Commands that return structured data with fields can be filtered and/or re-ordered by using the --fields option. These structured data types can also be formatted into a more generic type such as yaml or json; however, unstructured data cannot be filtered, re-ordered, or rendered in a table. It is therefore best for a command to use the apporpriate structured data type in place of a php array whenever possible.

## API Usage

It is recommended to use [Consolidation/AnnotationCommand](https://github.com/consolidation-org/annotation-command) to manage commands and formatters.  See the [AnnotationCommand API Usage](https://github.com/consolidation-org/annotation-command#api-usage) for details.

The FormatterManager may also be used directly, if desired:
```
/**
 * @param OutputInterface $output Output stream to write to
 * @param string $format Data format to output in
 * @param mixed $structuredOutput Data to output
 * @param array $annotationData Configuration information for formatter
 * @param array $options User options
 */
function doFormat(
    OutputInterface $output,
    string $format, 
    array $data,
    array $configurationData, 
    array $options) 
{
    $formatterManager = new FormatterManager();
    $formatterManager->write(output, $format, $data, $configurationData, $options);
}
```
## Comparison to Existing Solutions

Formatters have been in use in Drush since version 5. Drush allows formatters to be defined using simple classes, some of which may be configured using metadata. Furthermore, nested formatters are also allowed; for example, a list formatter may be given another formatter to use to format each of its rows. Nested formatters also require nested metadata, causing the code that constructed formatters to become very complicated and unweildy.

Consolidation/OutputFormatters maintains the simplicity of use provided by Drush formatters, but abandons nested metadata configuration in favor of using code in the formatter to configure itself, in order to keep the code simpler.

