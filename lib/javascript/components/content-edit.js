import React, { Component } from 'react';
import Table from './content-edit/table';
import arrayMove from 'array-move';
const PropTypes = require('prop-types');

export default class ContentEditBase extends Component {
    constructor(props) {
        super(props);

        this.state = {
            value: this.props.value.map((row, index) => {
                const item = {
                    value: row,
                    originalIndex: index,
                };

                if (row.group !== undefined) {
                    item.isSystemGroup = this.props.settings.groupSettings.groups[row.group] !== undefined;
                }

                return item;
            }),
            index: {
                next: this.props.value.length,
            },
            remove: [],
        };

        this.onSortEnd = this.onSortEnd.bind(this);
        this.addGroupRow = this.addGroupRow.bind(this);
        this.addRelationRows = this.addRelationRows.bind(this);
        this.toggleRemove = this.toggleRemove.bind(this);
        this.removeRows = this.removeRows.bind(this);
        this.updateRowAttribute = this.updateRowAttribute.bind(this);
        this.updateGroupName = this.updateGroupName.bind(this);
    }

    onSortEnd({ oldIndex, newIndex }) {
        if (newIndex === 0 && !this.props.settings.groupSettings.allowUngrouped && this.state.value[oldIndex].value.group === undefined) {
            newIndex = 1;
        }

        if (newIndex !== oldIndex) {
            const state = Object.assign({}, this.state);
            state.value = arrayMove(state.value, oldIndex, newIndex);
            this.setState(state);
        }
    }

    addGroupRow() {
        let index = this.state.index.next;
        const value = this.state.value;

        value.push({
            value: { group: '', isSystemGroup: false },
            originalIndex: index++,
        });

        const state = this.state;
        state.value = value;
        state.index.next = index;
        this.setState(state);
    }

    addRelationRows(rows) {
        let index = this.state.index.next;
        const value = this.state.value;

        rows.map((item) => {
            value.push({ value: item, originalIndex: index++ });
        });

        const state = this.state;
        state.value = value;
        state.index.next = index;
        this.setState(state);
    }

    toggleRemove(rowIndex) {
        const state = this.state;
        if (state.remove.indexOf(rowIndex) !== -1) {
            state.remove.splice(state.remove.indexOf(rowIndex), 1);
        } else {
            state.remove.push(rowIndex);
        }
        this.setState(state);
    }

    removeRows() {
        const state = this.state;
        const value = state.value;
        const rowsToRemove = [];
        value.forEach((row) => {
            if (state.remove.indexOf(row.originalIndex) !== -1) {
                rowsToRemove.push(row);
            }
        });
        rowsToRemove.forEach((row) => {
            value.splice(value.indexOf(row), 1);
        });
        state.remove = [];
        this.setState(state);
    }

    updateRowAttribute(rowIndex, attributeIdentifier, value) {
        const state = this.state;
        state.value[rowIndex].value.attributes[attributeIdentifier] = value;
        this.setState(state);
    }

    updateGroupName(rowIndex, value) {
        const state = this.state;
        state.value[rowIndex].value.group = value;
        this.setState(state);
    }

    componentDidMount() {
        this.updateInput();
    }

    componentDidUpdate() {
        this.updateInput();
    }

    updateInput() {
        this.props.input.setAttribute(
            'value',
            JSON.stringify(
                this.state.value.map((item) => {
                    return item.value;
                })
            )
        );
    }

    render() {
        return (
            <Table
                settings={this.props.settings}
                validation={this.props.validation}
                items={this.state.value}
                languages={this.props.languages}
                onSortEnd={this.onSortEnd}
                addGroupRow={this.addGroupRow}
                addRelationRows={this.addRelationRows}
                updateRowAttribute={this.updateRowAttribute}
                updateGroupName={this.updateGroupName}
                removeList={this.state.remove}
                toggleRemove={this.toggleRemove}
                removeRows={this.removeRows}
                helperContainer={this.props.cloneContainer}
                udwContainer={this.props.udwContainer}
                udwModule={this.props.udwModule}
                attributeModules={this.props.attributeModules}
                restInfo={this.props.restInfo}
                useDragHandle
                lockAxis="y"
            />
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.ContentEdit', ContentEditBase);

ContentEditBase.propTypes = {
    settings: PropTypes.shape({
        groupSettings: PropTypes.shape({
            allowUngrouped: PropTypes.bool.isRequired,
            groups: PropTypes.array.isRequired,
        }).isRequired,
    }).isRequired,
    validation: PropTypes.object.isRequired,
    languages: PropTypes.arrayOf(PropTypes.string).isRequired,
    input: PropTypes.instanceOf(Node),
    cloneContainer: PropTypes.instanceOf(Node),
    udwContainer: PropTypes.instanceOf(Node),
    udwModule: PropTypes.instanceOf(Component),
    attributeModules: PropTypes.arrayOf(PropTypes.instanceOf(Component)).isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    value: PropTypes.array.isRequired,
};
