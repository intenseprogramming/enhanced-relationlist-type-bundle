
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

## Add required nodejs packages to your installation

```
npm install array-move@^2.1.0 react-sortable-hoc@^1.8.3
```

or

```
yarn add array-move@^2.1.0 react-sortable-hoc@^1.8.3 --save
```

## Build the javascript and style assets

This bundle is no longer using assetic (as of 1.1.x). Assets are build with encore (default since ezplatform 2.5.x).

Have a look at the [official documentation](https://doc.ezplatform.com/en/2.5/releases/updating_ez_platform/).

```
yarn encore prod
```

## Add the field type

Create new content type with the field type `Content relations (enhanced)` or add it to an available content type.
