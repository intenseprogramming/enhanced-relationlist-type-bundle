import React from 'react';
import BaseAttributeComponent from './base-component';

export default class Checkbox extends BaseAttributeComponent {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value,
        };

        this.onChange = this.onChange.bind(this);
    }

    onChange(event) {
        this.setState({ value: event.target.checked ? 1 : 0 });
    }

    render() {
        return (
            <label>
                <input className="ez-data-source__input" type="checkbox" checked={this.state.value} value="1" onChange={this.onChange} />
            </label>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributes.boolean', Checkbox);
