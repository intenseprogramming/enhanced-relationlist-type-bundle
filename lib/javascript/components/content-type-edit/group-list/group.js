import React, { Component } from 'react';
import { SortableElement } from 'react-sortable-hoc';
import _ from 'lodash';
import DragHandle from './../../shared/drag-handle';
import Utils from '../../utils';
const PropTypes = require('prop-types');

class GroupElement extends Component {
    updateName(event) {
        const value = _.cloneDeep(this.props.value);
        value[this.props.language] = event.target.value ? event.target.value : undefined;

        this.props.updateGroup(this.props.identifier, value);
    }

    render() {
        const that = this;

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
                <td style={{width: '30%'}}>
                    {that.props.identifier}
                </td>
                <td style={{width: '66%'}}>
                    <input
                        type="text"
                        className="form-control"
                        value={Utils.translate([this.props.language], this.props.value, '')}
                        placeholder={this.props.value[Object.keys(this.props.value)[0]]}
                        onChange={this.updateName.bind(this)}
                    />
                </td>
            </tr>
        );
    }
}

GroupElement.propTypes = {
    identifier: PropTypes.string.isRequired,
    language: PropTypes.string.isRequired,
    value: PropTypes.string.isRequired,
    toggleDeleteMark: PropTypes.func.isRequired,
    updateGroup: PropTypes.func.isRequired,
    markedForDelete: PropTypes.bool.isRequired,
};

const SortableItem = SortableElement(GroupElement);

export default class Group extends Component {
    render() {
        const that = this;

        return <SortableItem {...that.props} />;
    }
}
