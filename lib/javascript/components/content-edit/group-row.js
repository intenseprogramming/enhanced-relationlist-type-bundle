import React, { Component } from 'react';
import { SortableElement } from 'react-sortable-hoc';
import DragHandle from './../shared/drag-handle';
import Utils from '../utils';
const PropTypes = require('prop-types');

class GroupRowElement extends Component {
    constructor(props) {
        super(props);

        this.toggleRemove = this.toggleRemove.bind(this);
        this.updateGroupName = this.updateGroupName.bind(this);
    }

    renderExtraCells() {
        const attributeDefinitions = this.props.settings.attributeDefinitions;
        const result = [];

        for (let key in attributeDefinitions) {
            if (attributeDefinitions.hasOwnProperty(key)) {
                result.push(<td> </td>);
            }
        }

        if (result.length === 0) {
            return '';
        }
        result.shift();

        return result;
    }

    renderRemoveInput() {
        if (this.props.isSystemGroup) {
            return '';
        } else {
            return <input className="erl-remove-control" type="checkbox" checked={this.props.remove} onChange={this.toggleRemove} />;
        }
    }

    renderGroupNameColumn() {
        if (this.props.isSystemGroup) {
            const systemGroups = this.props.settings.groupSettings.groups;
            return Utils.translate(this.props.languages, systemGroups[this.props.value.group], this.props.value.group);
        } else {
            return (
                <input
                    type="text"
                    className="ez-data-source__input form-control erl-group-name-input"
                    value={this.props.value.group}
                    onChange={this.updateGroupName}
                />
            );
        }
    }

    toggleRemove() {
        this.props.toggleRemove(this.props.rowIndex);
    }

    updateGroupName(event) {
        this.props.updateGroupName(this.props.rowIndex, event.target.value);
    }

    render() {
        return (
            <tr className="ez-relations__group erl-relation-group">
                <DragHandle enabled={!this.props.settings.groupSettings.positionsFixed || !this.props.isSystemGroup} />
                <td className="slim">
                    <label className="slim-content">{this.renderRemoveInput()}</label>
                </td>
                <td className="unselectable">{window.Translator.trans('table.column.group_name', {}, 'enhancedrelationlist')}</td>
                <td className={this.props.isSystemGroup ? 'unselectable' : ''}>{this.renderGroupNameColumn()}</td>
                {this.renderExtraCells()}
            </tr>
        );
    }
}

GroupRowElement.propTypes = {
    value: PropTypes.shape({
        group: PropTypes.string.isRequired,
    }).isRequired,
    languages: PropTypes.arrayOf(PropTypes.string).isRequired,
    rowIndex: PropTypes.number.isRequired,
    toggleRemove: PropTypes.func.isRequired,
    updateGroupName: PropTypes.func.isRequired,
    remove: PropTypes.bool.isRequired,
    isSystemGroup: PropTypes.bool.isRequired,
    settings: PropTypes.shape({
        attributeDefinitions: PropTypes.object.isRequired,
        groupSettings: PropTypes.shape({
            positionsFixed: PropTypes.bool.isRequired,
        }).isRequired,
    }).isRequired,
};

const SortableItem = SortableElement(GroupRowElement);

export default class GroupRow extends Component {
    render() {
        return <SortableItem {...this.props} />;
    }
}
