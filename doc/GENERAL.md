# General information

General information to consider when using this field type.

## Attributes

Attributes are stored per relation and are validated on an individual basis.

When adding/removing attributes after content has been created
- the data of the removed attribute will still be stored inside the field but will be rendered inaccessible (new versions of the field won't have the value)
- new attributes will be served with empty data

Rearranging attributes will directly effect the content (order is determined on load by using the field definition).

## Groups

Groups are categorized into `system groups` and `content groups`.
- `System groups`
    - are defined within the field definition
    - are translatable
- `Content groups`
    - are added by an editor on a content basis while editing (if allowed to)
    - are stored per content translation

Note: When removing `system groups`, they will be handled like custom groups and print the identifier as their name.

## Translation

Attribute names as well as `system group` names can be translated from within the field definition edit.

If your setup employs multiple languages, they will be offered in a dropdown above the respective lists. Otherwise the 
dropdown will not be rendered.
