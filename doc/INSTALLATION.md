
# Installation

## Install the bundle

Get the bundle using composer

```
composer require intprog/enhanced-relationlist-type-bundle
```

## Enabling the bundle
 
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

Add the bundle to the list of assetic bundles.

```yaml
assetic:
    # ...
    bundles:
        # ...
        - IntProgEnhancedRelationListBundle
        # ...
```

## Add the field type

Create new content type with the field type `Content relations (enhanced)` or add it to an available content type.
