# Change Log

### 2.0.0 - 15 September 2016

Have the default `string` format convert the result into a tab-separated-value table if possible.  Commands may select a single field to emit in this instance with an annotation:

  @single-field-default email

By this means, a given command may by default emit a single value, but also provide more rich output that may be shown by selecting --format=table, --format=yaml or the like.

This change might cause some commands to produce output in situations that previously were not documented as producing output.  Therefore, this change is considered to be not backwards-compatible with previous releases.


### 1.1.0 - 14 September 2016

Add tab-separated-value (tsv) formatter.


### 1.0.0 - 19 May 2016

First stable release.
