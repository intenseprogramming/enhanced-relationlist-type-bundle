import React, {Fragment} from 'react';
import _ from 'lodash';
import BaseAttributeComponent from './base-component';

export default class String extends BaseAttributeComponent {
    static getEmptySettings() {
        return {};
    }

    updateMinValue(event) {
        const state = _.cloneDeep(this.state);
        if (event.target.value === '') {
            state.value.min = undefined;
        } else {
            state.value.min = event.target.value;
        }
        this.setState(state);
    }

    updateMaxValue(event) {
        const state = _.cloneDeep(this.state);
        if (event.target.value === '') {
            state.value.max = undefined;
        } else {
            state.value.max = event.target.value;
        }
        this.setState(state);
    }

    render() {
        return (
            <Fragment>
                <label className="form-control-label mt-0">
                    {window.Translator.trans('erl.attribute.type.string.min-length', {}, 'enhancedrelationlist_definition_attribute')}
                </label>
                <input type="number" className="form-control" value={this.state.value.min} onChange={this.updateMinValue.bind(this)} />
                <label className="form-control-label">
                    {window.Translator.trans('erl.attribute.type.string.max-length', {}, 'enhancedrelationlist_definition_attribute')}
                </label>
                <input type="number" className="form-control" value={this.state.value.max} onChange={this.updateMaxValue.bind(this)} />
            </Fragment>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributeDefinitions.string', String);
