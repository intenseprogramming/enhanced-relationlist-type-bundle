import React from 'react';
const PropTypes = require('prop-types');

export default class BaseAttributeComponent extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value,
        }
    }

    static getEmptySettings() {
        return null;
    }

    componentDidUpdate(nextProps, nextState) {
        if (this.state && nextState && this.state.value !== nextState.value) {
            this.props.updateAttributeSettings(this.state.value);
        }
    }
}

BaseAttributeComponent.propTypes = {
    updateAttributeSettings: PropTypes.func.isRequired,
    rowIndex: PropTypes.number.isRequired,
    identifier: PropTypes.string.isRequired,
    value: PropTypes.any.isRequired,
    language: PropTypes.string.isRequired,
};
