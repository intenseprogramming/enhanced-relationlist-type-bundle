import React, { Component } from 'react';
import { SortableElement } from 'react-sortable-hoc';
import _ from 'lodash';
import DragHandle from './../../shared/drag-handle';
import Utils from './../../utils';
const PropTypes = require('prop-types');

class AttributeElement extends Component {
    renderAttributeSettings() {
        const Attribute = this.props.attributeModules[this.props.value.type];

        if (Attribute) {
            return (
                <Attribute
                    value={this.props.value.settings || {}}
                    language={this.props.language}
                    updateAttributeSettings={this.updateAttributeSettings.bind(this)}
                />
            );
        }

        return (<i>{window.Translator.trans('erl.attribute.type.no-settings', {}, 'enhancedrelationlist_definition_attribute')}</i>);
    }

    updateAttributeSettings(newValue) {
        const value = _.cloneDeep(this.props.value);
        value.settings = newValue;

        this.props.updateAttribute(this.props.identifier, value);
    }

    updateName(event) {
        const value = _.cloneDeep(this.props.value);
        value.names[this.props.language] = event.target.value ? event.target.value : undefined;

        this.props.updateAttribute(this.props.identifier, value);
    }

    toggleRequired() {
        const value = _.cloneDeep(this.props.value);
        value.required = !value.required;

        this.props.updateAttribute(this.props.identifier, value);
    }

    render() {
        return (
            <tr>
                <DragHandle enabled/>
                <td style={{width: '4%'}}>
                    <label>
                        <input
                            type="checkbox"
                            className="remove-attribute"
                            onChange={() => {this.props.toggleDeleteMark(this.props.identifier)}}
                            checked={this.props.markedForDelete}
                        />
                    </label>
                </td>
                <td style={{width: '24%'}}>
                    <label style={{width: '100%'}} className="active">
                        <input
                            type="text"
                            className="form-control"
                            value={Utils.translate([this.props.language], this.props.value.names, '')}
                            onChange={this.updateName.bind(this)}
                        />
                    </label>
                    <span className="ml-1">
                        ({this.props.identifier})
                    </span>
                </td>
                <td style={{width: '14%'}}>
                    {window.Translator.trans(this.props.value.type + '.name', {}, 'enhancedrelationlist_definition_attribute')}
                </td>
                <td style={{width: '44%'}}>{this.renderAttributeSettings()}</td>
                <td style={{width: '14%'}}>
                    <label className="ez-data-source__label is-checked" style={{width: '100%'}}>
                        <input type="checkbox" checked={this.props.value.required} onChange={this.toggleRequired.bind(this)} />
                    </label>
                </td>
            </tr>
        );
    }
}

const SortableItem = SortableElement(AttributeElement);

export default class ContentRow extends Component {
    render() {
        return <SortableItem {...this.props} />;
    }
}
