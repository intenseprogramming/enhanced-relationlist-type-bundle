
# Adding custom relation attributes

Required knowledge:
- 

This bundle uses the same functionality to add the core attributes (`integer`, `boolean`, `string`, `selection`).
With that you will be able to use this bundle as a source of concrete examples for implementation.

## Template blocks and configuration

### Attribute value rendering

Attribute templates are added to the `ezpublish.system.<scope>.enhanced_relation_list.attribute_templates`, similar to
field templates in ezplatform.

#### Template
```twig
{% block <attribute-type-identifier>_relation_attribute %}
    {# the logic for rendering the template #}
{% endblock %}
```

#### Configuration
```yaml
ezpublish:
    system:
        default: # default or any other scope
            enhanced_relation_list:
                attribute_templates:
                    -
                        template: '@ezdesign/path/to/your/attribute-template.html.twig'
                        priority: 0 # optional, default: 0
```

`priority`: Templates are sorted by priority descending (highest will be used first).

### Attribute definition rendering (optional)

Attribute templates are added to the `ezpublish.system.<scope>.enhanced_relation_list.attribute_definition_templates`,
similar to field templates in ezplatform.

_Note: The admin-ui currently does not use this functionality. Hence it being optional._

#### Template
```twig
{% block <attribute-type-identifier>_relation_attribute_definition %}
    {# the logic for rendering the template #}
{% endblock %}
```

#### Configuration

```yaml
ezpublish:
    system:
        default: # default or any other scope
            enhanced_relation_list:
                attribute_definition_templates:
                    -
                        template: '@ezdesign/path/to/your/attribute-definition-template.html.twig'
                        priority: 0 # optional, default: 0
```

`priority`: Templates are sorted by priority descending (highest will be used first).

## React components

React components need to be added to the `encore` configuration (`<bundle>/Resources/encore/ez.config.js`).
Don't forget about using the entrypoint in a template
[extending the admin-ui](https://doc.ezplatform.com/en/latest/guide/extending_ez_platform/#injecting-custom-components).

#### Attribute edit component
```javascript
import React from 'react';

class YourComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value,
        };
    }
    
    static getEmptyValue() {
        return null; // return the empty definition (with potential default value)
    }

    componentDidUpdate(nextProps, nextState) {
        if (typeof this.state === 'object' && typeof nextState === 'object' && this.state.value !== nextState.value) {
            this.props.updateRowAttribute(this.props.rowIndex, this.props.identifier, this.state.value);
        }
    }

    render() {
        return (
            '' // rendering the edit-output.
        );
    }
}

// Add the component to the attribute modules.
window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributes.<attribute-type-identifier>', YourComponent);
```

#### Attribute definition edit component

```javascript
import React from 'react';
import PropTypes from 'prop-types';

class YourComponent extends React.Component {
    static getEmptySettings() {
        return null;
    }

    render() {
        return (
            '' // rendering the definition edit-output.
        );
    }
}

YourComponent.propTypes = {
    updateAttributeSettings: PropTypes.func.isRequired,
    rowIndex: PropTypes.number.isRequired,
    identifier: PropTypes.string.isRequired,
    value: PropTypes.any.isRequired,
    language: PropTypes.string.isRequired,
};

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributeDefinitions.<attribute-type-identifier>', YourComponent);
```

The `updateAttributeSettings`-function is the part that propagates the change of the configuration up to the storage
value.

```javascript
// calling updateAttributeSettings on changing the configuration.
this.props.updateAttributeSettings(this.props.rowIndex, this.props.identifier, '<the new value>');
```

## The attribute value class

The value class is the container for the value after converting it from the storage.

```php
<?php
namespace Your\Bundle\Core\ERL;

use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;

class YourAttributeValue extends RelationAttributeBase
{
    public $value;
    
    public function getTypeIdentifier(){
        return '<attribute-type-identifier>';
    }
}
```

## The attribute value converter

#### Converter class

The converter takes care of conversion to and from storage value (hash), validation and empty-value check/generation.

```php
<?php
namespace Your\Bundle\Services;

use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;
use Your\Bundle\Core\ERL\YourAttributeValue;

class YourAttributeConverter extends RelationAttributeConverter
{
    public function toHash(RelationAttributeBase $attribute)
    {
        // convert the value to a storable hash and return it.
    }
    
    public function fromHash($hash)
    {
        // converts the hash to the value.
    }
    
    public function validate(RelationAttributeBase $attribute, $definition)
    {
        // check the validity of the value.
    }
    
    public function isEmpty(RelationAttributeBase $attribute)
    {
        // check if the value is empty.
    }
    
    public function getEmptyValue()
    {
        // returns an empty value.
    }
}
```

#### Converter service registration

The service needs to be tagged to with `name: int.prog.erl.relation.attribute` and requires the definition of an
`identifier` containing the attribute type identifier.

```yaml
services:
    Your\Bundle\Services\YourAttributeConverter:
        tags:
            - {name: int.prog.erl.relation.attribute, identifier: <attribute-type-identifier>}
```
