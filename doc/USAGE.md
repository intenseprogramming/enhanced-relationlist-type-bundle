# Usage

General purpose and usage of the type.

## Purpose

Adding information to a relation usually takes a separate content item (and presumably content type) to use as storage
between on each relation. No need for such complex (and of course complicated to wrap once head around).

The list can also be used as a glorified regular relation list (by not adding any attributes) and improves on the UX by
using drag'n'drop functionality to sort relations.

Groups can be used to replace multiple relation lists and trim down the content (although this might not be useful in 
every use case)

## Sample use-case

When you take a look at [https://www.intense-programming.com] the menu (top left/right) as well as the footer are
configured using a single content field of this type withing the root landing page.

The grouping functionality is used to add elements to either the `Left side menu`, `Right side menu` or `Footer links`.

Attributes are used to control the dropdown on buttons (`Display children`, `Children count`, `Order by`).
