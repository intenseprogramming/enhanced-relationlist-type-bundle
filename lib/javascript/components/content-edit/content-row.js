import React, { Component } from 'react';
import { SortableElement } from 'react-sortable-hoc';
import DragHandle from './../shared/drag-handle';
const PropTypes = require('prop-types');

class ContentRowElement extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: null,
            initialized: false,
        };

        this.toggleRemove = this.toggleRemove.bind(this);
        this.onContentRequestFinished = this.onContentRequestFinished.bind(this);
    }

    renderAttributes() {
        const attributeDefinitions = this.props.settings.attributeDefinitions;
        const result = [];
        let Attribute = null;

        for (let key in attributeDefinitions) {
            if (attributeDefinitions.hasOwnProperty(key)) {
                Attribute = this.props.attributeModules[attributeDefinitions[key].type];

                if (Attribute) {
                    result.push(
                        <td>
                            <Attribute
                                key={key}
                                updateRowAttribute={this.props.updateRowAttribute}
                                identifier={key}
                                definition={attributeDefinitions[key]}
                                languages={this.props.languages}
                                rowIndex={this.props.rowIndex}
                                value={this.props.value.attributes[key]}
                            />
                        </td>
                    );
                } else {
                    result.push(<td>{attributeDefinitions[key].type} is unknown</td>);
                }
            }
        }

        if (result.length === 0) {
            return <td> </td>;
        }

        return result;
    }

    onContentRequestFinished({ Content }) {
        this.setState({
            name: Content.Name,
            initialized: true,
        });
    }

    componentDidMount() {
        const request = new Request('/api/ezp/v2/content/objects/' + this.props.value.contentId, {
            method: 'GET',
            headers: {
                Accept: 'application/vnd.ez.api.View+json; version=1.1',
                'Content-Type': 'application/vnd.ez.api.ViewInput+json; version=1.1',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Siteaccess': this.props.restInfo.siteaccess,
                'X-CSRF-Token': this.props.restInfo.token,
            },
            mode: 'same-origin',
            credentials: 'same-origin',
        });
        const errorMessage = window.Translator.trans('table.row.content.load_error', {}, 'enhancedrelationlist');

        fetch(request)
            .then(window.eZ.helpers.request.getJsonFromResponse)
            .then(this.onContentRequestFinished)
            .catch(() => window.eZ.helpers.notification.showErrorNotification(errorMessage));
    }

    toggleRemove() {
        this.props.toggleRemove(this.props.rowIndex);
    }

    render() {
        return (
            <tr className="ez-relations__item erl-relation-item">
                <DragHandle enabled />
                <td className="slim">
                    <label className="slim-content">
                        <input className="erl-remove-control" type="checkbox" checked={this.props.remove} onChange={this.toggleRemove} />
                    </label>
                </td>
                <td>{this.state.initialized ? this.state.name : <em>loading...</em>}</td>
                {this.renderAttributes()}
            </tr>
        );
    }
}

ContentRowElement.propTypes = {
    value: PropTypes.shape({
        contentId: PropTypes.number.isRequired,
        attributes: PropTypes.object.isRequired,
    }).isRequired,
    languages: PropTypes.arrayOf(PropTypes.string).isRequired,
    rowIndex: PropTypes.number.isRequired,
    toggleRemove: PropTypes.func.isRequired,
    updateRowAttribute: PropTypes.func.isRequired,
    remove: PropTypes.bool.isRequired,
    settings: PropTypes.shape({
        attributeDefinitions: PropTypes.object.isRequired,
    }).isRequired,
    attributeModules: PropTypes.arrayOf(Component),
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
};

const SortableItem = SortableElement(ContentRowElement);

export default class ContentRow extends Component {
    render() {
        return <SortableItem {...this.props} />;
    }
}
