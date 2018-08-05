
# IntProg Enhanced Relation List Type Bundle

This bundle contains the enhanced relation list field type for eZ Platform version 2.x

The enhanced relation list type stores configurable information extending simple relations. It enables an editor to
enhance the data stored with the relation (grouping and attributes).

Additionally the relations may be grouped (via predefined or on the fly created groups).

The behavior of the relation list is completely controlled by the field definition (settings/validation).

## Installation

#### Install the bundle

Get the bundle using composer

```
composer require ezsystems/ezplatform-drawio-fieldtype
```

#### Enable the bundle in kernel
 
Add the bundle to registerBundles in your Kernel.
 
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new IntProg\EnhancedRelationListBundle\IntProgEnhancedRelationListBundle(),
        // ...
    ];
    
    // ...
}
```

#### Add the field type

Create new content type with the attribute `Content relations (enhanced)` or add it to an available content type.

## Configuration

All settings are defined when editing/creating the content type containing the field.
