import React from 'react';
import BaseAttributeComponent from './base-component';

export default class String extends BaseAttributeComponent {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value,
        };

        this.onChange = this.onChange.bind(this);
    }

    onChange(event) {
        this.setState({ value: event.target.value });
    }

    render() {
        return (
            <label>
                <input className="ez-data-source__input form-control" type="text" value={this.state.value} onChange={this.onChange} />
            </label>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributes.string', String);
