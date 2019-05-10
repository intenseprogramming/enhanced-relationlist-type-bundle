import React, { Component } from 'react';
import { SortableContainer } from 'react-sortable-hoc';
import TableHead from './table-head';
import Attribute from './attribute'
import PropTypes from 'prop-types';

class TableElement extends Component {
    render() {
        const that = this;

        return (
            <table className={'table ez-table--list erl-table'}>
                <thead>
                    <TableHead />
                </thead>
                <tbody>
                    {that.props.items.map((item, index) => {
                        return (
                            <Attribute
                                rowStyle={this.props.rowStyle}
                                key={item.internalIndex}
                                index={index}
                                identifier={item.identifier}
                                value={item.item}
                                markedForDelete={item.markedForDelete}
                                language={this.props.language}
                                updateAttribute={this.props.updateAttribute}
                                toggleDeleteMark={this.props.toggleDeleteMark}
                                attributeModules={this.props.attributeModules}
                            />
                        )
                    })}
                </tbody>
            </table>
        )
    }
}

TableElement.propTypes = {
    items: PropTypes.arrayOf(PropTypes.shape({
        internalIndex: PropTypes.number.isRequired,
        identifier: PropTypes.string.isRequired,
        item: PropTypes.object.isRequired,
        markedForDelete: PropTypes.bool.isRequired,
    })).isRequired,
    language: PropTypes.string.isRequired,
    updateAttribute: PropTypes.func.isRequired,
    toggleDeleteMark: PropTypes.func.isRequired,
    attributeModules: PropTypes.arrayOf(Component).isRequired,
    rowStyle: PropTypes.object.isRequired,
};

const SortableList = SortableContainer(TableElement);

export default class Table extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const that = this;

        return <SortableList {...that.props} />;
    }
}
