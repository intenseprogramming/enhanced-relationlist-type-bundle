import React, { Component } from 'react';
import Utils from '../utils';
const PropTypes = require('prop-types');

export default class TableHead extends Component {
    renderAttributeHeadings() {
        const attributeDefinitions = this.props.settings.attributeDefinitions;
        const result = [];

        for (let key in attributeDefinitions) {
            if (attributeDefinitions.hasOwnProperty(key)) {
                result.push(<th>{Utils.translate(this.props.languages, attributeDefinitions[key].names, key)}</th>);
            }
        }

        if (result.length === 0) {
            return <th> </th>;
        }

        return result;
    }

    render() {
        return (
            <thead>
                <tr>
                    <th className="erl-drag-column" />
                    <th className="slim" />
                    <th>{window.Translator.trans('table.header.relation', {}, 'enhancedrelationlist')}</th>
                    {this.renderAttributeHeadings()}
                </tr>
            </thead>
        );
    }
}

TableHead.propTypes = {
    languages: PropTypes.array.isRequired,
    settings: PropTypes.shape({
        attributeDefinitions: PropTypes.object.isRequired,
    }).isRequired,
};
