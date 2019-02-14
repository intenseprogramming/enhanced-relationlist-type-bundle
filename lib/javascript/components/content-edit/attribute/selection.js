import React from 'react';
import BaseAttributeComponent from './base-component';
import Utils from '../../utils';

export default class Selection extends BaseAttributeComponent {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value !== undefined ? this.props.value : [],
        };

        this.onChange = this.onChange.bind(this);
    }

    static getEmptyValue() {
        return [];
    }

    onChange(event) {
        const newValue = [];

        event.target.querySelectorAll('option').forEach((option) => {
            if (option.selected) {
                newValue.push(parseInt(option.value));
            }
        });

        this.setState({ value: newValue });
    }

    renderOptions() {
        const options = this.props.definition.settings.options;
        const result = [];

        for (let key in options) {
            if (options.hasOwnProperty(key)) {
                result.push(
                    <option key={key} value={key} selected={this.state.value.indexOf(parseInt(key)) !== -1}>
                        {Utils.translate(this.props.languages, options[key], key)}
                    </option>
                );
            }
        }

        return result;
    }

    render() {
        return (
            <select
                className="ez-data-source__input form-control"
                multiple={this.props.definition.settings.multiple}
                onChange={this.onChange}>
                {!this.props.definition.required ? <option>None</option> : ''}
                {this.renderOptions()}
            </select>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributes.selection', Selection);
