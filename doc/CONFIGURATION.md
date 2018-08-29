
# Configuration

All settings are defined when creating/updating the content type containing the field.

## General configuration

### Based on Relations (multiple)

All settings available in the regular relation list are replicated in this content type and work the same way (if not
adjusted by one of the following settings)

### Duplicate items

Allow and disallow adding a single content item to the relation multiple times. If enable the editor will be able to add
a relation to content even if the item is already added to the list.

### Fixed group positions

Fix the position (order) of groups defined in the field definition. System groups can be freely ordered by an editor if
this setting is not enabled.

### Extendable groups

When trying to add an element to the list, the editor will be prompted if he either likes to add a new group or new
items to the list.

### Allow / disallow ungrouped items

Allows / disallows adding relation items outside of groups (on top of the list). The UI still allows for items to be
placed on top but will not validate/allow the value to be stored.

## Attributes

Attributes are extra information added to a relation.

Currently only `integer`, `string`, `checkbox` and a `selection` are supported types. Multiple attributes can be added 
to be used for each relation item.

They are defined by their identifier, a required state and a set of configuration options (if the type supports any).

## Groups

Fixed groups can be added within the definition edit. Those groups can not be removed by an editor and are called
`system groups` as they are not defined by content.

System groups are controlled by their respective identifier and can be translated within the definition edit.
