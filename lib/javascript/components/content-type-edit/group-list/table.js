import React, { Component } from 'react';
import { SortableContainer } from 'react-sortable-hoc';
import TableHead from './table-head';
import Group from './group'
const PropTypes = require('prop-types');

class TableElement extends Component {
    render() {
        return (
            <table className={'table ez-table--list erl-table'}>
                <thead>
                    <TableHead />
                </thead>
                <tbody>
                    {this.props.items.map((item, index) => {
                        return (
                            <Group
                                rowStyle={this.props.rowStyle}
                                key={item.internalIndex}
                                index={index}
                                identifier={item.identifier}
                                value={item.item}
                                markedForDelete={item.markedForDelete}
                                language={this.props.language}
                                updateGroup={this.props.updateGroup}
                                toggleDeleteMark={this.props.toggleDeleteMark}
                            />
                        )
                    })}
                </tbody>
            </table>
        )
    }
}

const SortableList = SortableContainer(TableElement);

export default class Table extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return <SortableList {...this.props} />;
    }
}
