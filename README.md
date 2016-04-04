# Consolidation\Formatters

Apply transformations to structured data to write output in different formats.

[![Travis CI](https://travis-ci.org/consolidation-org/formatters.svg?branch=master)](https://travis-ci.org/consolidation-org/formatters) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/consolidation-org/formatters/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation-org/formatters/?branch=master) [![License](https://poser.pugx.org/consolidation/formatters/license)](https://packagist.org/packages/consolidation/formatters)

## Component Status

Under development.

## Motivation

Formatters are used to allow simple commandline tool commands to be implemented in a manner that is completely independent from the Symfony Console output interfaces.  A command receives its input via its method parameters, and returns its result as structured data (e.g. a php standard object or array).  The structured data is then formatted by a formatter, and the result is printed.

This process is managed by the [Consolidation/AnnotationCommand](https://github.com/consolidation-org/annotation-command) project.

## Example Formatter
tbd

## API Usage
tbd

## Comparison to Existing Solutions

Formatters have been in use in Drush since version 5. 
