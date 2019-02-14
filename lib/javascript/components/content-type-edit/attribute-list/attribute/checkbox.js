import React from 'react';
import BaseAttributeComponent from './base-component';

export default class Checkbox extends BaseAttributeComponent {
    render() {
        return (<i>{window.Translator.trans('erl.attribute.type.no-settings', {}, 'enhancedrelationlist_definition_attribute')}</i>);
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributeDefinitions.boolean', Checkbox);
