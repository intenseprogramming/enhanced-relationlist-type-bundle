import React, {Component} from 'react';

export default class TableHeader extends Component {
    render() {
        return (
            <tr>
                <th className="erl-drag-column" />
                <th style={{width: '4%'}} />
                <th style={{width: '24%'}}>Name</th>
                <th style={{width: '14%'}}>Type</th>
                <th style={{width: '44%'}}>Settings</th>
                <th style={{width: '14%'}}>Required</th>
            </tr>
        );
    }
}
