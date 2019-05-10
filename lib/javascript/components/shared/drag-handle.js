import React, { Component } from 'react';
import { SortableHandle } from 'react-sortable-hoc';
const PropTypes = require('prop-types');

class DragHandleElement extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <td className="erl-drag-column erl-drag-handle unselectable">
                <i className="drag-icon" />
            </td>
        );
    }
}

const DragHandleItem = SortableHandle(DragHandleElement);

export default class DragHandle extends Component {
    render() {
        if (this.props.enabled) {
            return <DragHandleItem enabled={this.props.enabled} />;
        } else {
            return <td className="erl-no-drag-column"> </td>;
        }
    }
}

DragHandle.propTypes = {
    enabled: PropTypes.bool.isRequired,
};
