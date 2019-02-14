import React from 'react';
import BaseAttributeComponent from './base-component';

export default class Integer extends BaseAttributeComponent {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value,
        };

        this.onChange = this.onChange.bind(this);
    }

    onChange(event) {
        const newValue = parseInt(event.target.value);

        if (!isNaN(newValue)) {
            if (this.props.definition.settings.min !== null && this.props.definition.settings.min > newValue) {
                return;
            }
            if (this.props.definition.settings.max !== null && this.props.definition.settings.max < newValue) {
                return;
            }

            this.setState({ value: newValue });
        } else {
            this.setState({ value: null });
        }
    }

    render() {
        return (
            <label>
                <input className="ez-data-source__input form-control" type="number" value={this.state.value} onChange={this.onChange} />
            </label>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributes.integer', Integer);
