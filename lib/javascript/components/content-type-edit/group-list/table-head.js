import React, {Component} from 'react';

export default class TableHeader extends Component {
    render() {
        return (
            <tr>
                <th className="erl-drag-column" />
                <th style={{width: '4%'}} />
                <th style={{width: '30%'}}>{window.Translator.trans('field_definition.group_definition.col.identifier', {}, 'enhancedrelationlist_definition_group')}</th>
                <th style={{width: '66%'}}>{window.Translator.trans('field_definition.group_definition.col.name', {}, 'enhancedrelationlist_definition_group')}</th>
            </tr>
        );
    }
}
