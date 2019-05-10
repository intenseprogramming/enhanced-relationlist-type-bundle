import React from 'react';
const PropTypes = require('prop-types');

export default class BaseAttributeComponent extends React.Component {
    static getEmptyValue() {
        return null;
    }

    componentDidUpdate(nextProps, nextState) {
        if (typeof this.state === 'object' && typeof nextState === 'object' && this.state.value !== nextState.value) {
            this.props.updateRowAttribute(this.props.rowIndex, this.props.identifier, this.state.value);
        }
    }
}

BaseAttributeComponent.propTypes = {
    updateRowAttribute: PropTypes.func.isRequired,
    rowIndex: PropTypes.number.isRequired,
    identifier: PropTypes.string.isRequired,
};
