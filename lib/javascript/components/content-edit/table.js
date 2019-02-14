import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { SortableContainer } from 'react-sortable-hoc';
import TableHead from './table-head';
import ContentRow from './content-row';
import GroupRow from './group-row';
const PropTypes = require('prop-types');

class TableElement extends Component {
    constructor(props) {
        super(props);

        this.state = {
            udwIsOpen: false,
            addFlyoutOpen: false,
        };

        this.onDocumentClick = this.onDocumentClick.bind(this);
        this.onAddButtonClick = this.onAddButtonClick.bind(this);
        this.openAddFlyout = this.openAddFlyout.bind(this);
        this.closeAddFlyout = this.closeAddFlyout.bind(this);
        this.openUdw = this.openUdw.bind(this);
        this.closeUdw = this.closeUdw.bind(this);
        this.addGroupRow = this.addGroupRow.bind(this);
        this.addRelationRows = this.addRelationRows.bind(this);
        this.canSelectContent = this.canSelectContent.bind(this);
    }

    onDocumentClick(event) {
        if (
            !(
                (this.flyoutContainer && this.flyoutContainer.contains(event.target)) ||
                (this.addButton && this.addButton.contains(event.target))
            )
        ) {
            this.closeAddFlyout();
        }
    }

    onAddButtonClick() {
        if (this.props.settings.groupSettings.extendable) {
            if (!this.state.addFlyoutOpen) {
                this.openAddFlyout();
            }
        } else {
            this.openUdw();
        }
    }

    openAddFlyout() {
        document.addEventListener('mousedown', this.onDocumentClick);

        const state = Object.assign({}, this.state);
        state.addFlyoutOpen = true;
        this.setState(state);
    }

    closeAddFlyout() {
        document.removeEventListener('mousedown', this.onDocumentClick);

        const state = Object.assign({}, this.state);
        state.addFlyoutOpen = false;
        this.setState(state);
    }

    openUdw() {
        const state = Object.assign({}, this.state);

        if (this.state.addFlyoutOpen) {
            document.removeEventListener('mousedown', this.onDocumentClick);
            state.addFlyoutOpen = false;
        }

        state.udwIsOpen = true;
        this.setState(state);
    }

    closeUdw() {
        const state = Object.assign({}, this.state);
        state.udwIsOpen = false;
        this.setState(state);
    }

    addGroupRow() {
        this.closeAddFlyout();
        this.props.addGroupRow();
    }

    addRelationRows(items) {
        const attributeDefinitions = this.props.settings.attributeDefinitions;
        const newRows = [];

        items.map((item) => {
            const attributeStubs = {};

            for (let key in attributeDefinitions) {
                if (attributeDefinitions.hasOwnProperty(key)) {
                    attributeStubs[key] = this.props.attributeModules[attributeDefinitions[key].type].getEmptyValue(
                        attributeDefinitions[key]
                    );
                }
            }

            newRows.push({
                contentId: item.ContentInfo.Content._id,
                attributes: attributeStubs,
            });
        });

        const state = Object.assign({}, this.state);
        state.udwIsOpen = false;
        this.setState(state);

        this.props.addRelationRows(newRows);
    }

    canSelectContent({ item }, callback) {
        if (typeof callback !== 'function') {
            callback = () => {};
        }

        let isDuplicate = false;
        if (!this.props.settings.selectionAllowDuplicates) {
            this.props.items.forEach((row) => {
                if (row.value.contentId === item.ContentInfo.Content._id) {
                    isDuplicate = true;
                }
            });
        }

        const allowedContentTypes = this.props.validation.relationValidator.allowedContentTypes;
        let typeAllowed = true;
        if (allowedContentTypes.length > 0) {
            if (allowedContentTypes.indexOf(item.ContentInfo.Content.ContentTypeInfo.identifier) === -1) {
                typeAllowed = false;
            }
        }

        callback(!isDuplicate && typeAllowed);
    }

    renderUdwPortal() {
        const UniversalDiscoveryModule = this.props.udwModule;

        let selectedItemsLimit = this.props.settings.selectionLimit;
        if (selectedItemsLimit > 0) {
            selectedItemsLimit = selectedItemsLimit - this.props.value.length;
        }
        const startingLocationId = this.props.settings.defaultBrowseLocation;

        if (this.state.udwIsOpen) {
            return ReactDOM.createPortal(
                <UniversalDiscoveryModule
                    onConfirm={this.addRelationRows}
                    onCancel={this.closeUdw}
                    title={window.Translator.trans('udw.title', {}, 'enhancedrelationlist')}
                    multiple={selectedItemsLimit !== 1}
                    selectedItemsLimit={selectedItemsLimit}
                    startingLocationId={startingLocationId}
                    restInfo={this.props.restInfo}
                    canSelectContent={this.canSelectContent}
                />,
                this.props.udwContainer
            );
        } else {
            return '';
        }
    }

    renderAddFlyout() {
        if (this.state.addFlyoutOpen) {
            return (
                <div
                    className="add-flyout"
                    ref={(ref) => {
                        this.flyoutContainer = ref;
                    }}>
                    <div className="button-connector" />
                    <div className="description">
                        {window.Translator.trans('content_edit.add.flyout.description', {}, 'enhancedrelationlist')}
                    </div>
                    <div className="action-button-container">
                        <button type="button" className="add-group-button btn btn-secondary" onClick={this.addGroupRow}>
                            {window.Translator.trans('content_edit.add.flyout.add_group', {}, 'enhancedrelationlist')}
                        </button>
                        <button type="button" className="add-relation-button btn btn-secondary" onClick={this.openUdw}>
                            {window.Translator.trans('content_edit.add.flyout.add_relation', {}, 'enhancedrelationlist')}
                        </button>
                    </div>
                </div>
            );
        } else {
            return '';
        }
    }

    render() {
        return (
            <div>
                <div className="ez-table-header erl-table-header">
                    <div className="ez-table-header__headline">
                        {window.Translator.trans('header.relation', {}, 'enhancedrelationlist')}
                    </div>
                    <div className="add-flyout-container">
                        <button
                            type="button"
                            className="btn btn-primary"
                            onClick={this.onAddButtonClick}
                            ref={(ref) => {
                                this.addButton = ref;
                            }}>
                            <svg className="ez-icon">
                                <use
                                    xmlnsXlink="http://www.w3.org/1999/xlink"
                                    xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#create"
                                />
                            </svg>
                        </button>
                        <button
                            type="button"
                            className="btn btn-danger"
                            disabled={!this.props.removeList.length}
                            onClick={this.props.removeRows}>
                            <svg className="ez-icon">
                                <use
                                    xmlnsXlink="http://www.w3.org/1999/xlink"
                                    xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#trash"
                                />
                            </svg>
                        </button>
                        {this.renderAddFlyout()}
                    </div>
                </div>
                <table className="relation-root-table erl-table table mb-0">
                    <TableHead settings={this.props.settings} languages={this.props.languages} />
                    <tbody>
                        {this.props.items.map((row, index) => {
                            if (typeof row.value.group === 'undefined') {
                                return (
                                    <ContentRow
                                        key={'entry-' + row.originalIndex}
                                        toggleRemove={this.props.toggleRemove}
                                        remove={this.props.removeList.indexOf(row.originalIndex) !== -1}
                                        updateRowAttribute={this.props.updateRowAttribute}
                                        index={index}
                                        rowIndex={index}
                                        value={row.value}
                                        languages={this.props.languages}
                                        attributeModules={this.props.attributeModules}
                                        checked={row.checked}
                                        settings={this.props.settings}
                                        restInfo={this.props.restInfo}
                                    />
                                );
                            } else if (!(typeof row.value.group === 'undefined')) {
                                return (
                                    <GroupRow
                                        key={'group-' + row.originalIndex}
                                        toggleRemove={this.props.toggleRemove}
                                        remove={this.props.removeList.indexOf(row.originalIndex) !== -1}
                                        updateGroupName={this.props.updateGroupName}
                                        index={index}
                                        rowIndex={index}
                                        value={row.value}
                                        languages={this.props.languages}
                                        isSystemGroup={row.isSystemGroup}
                                        checked={row.checked}
                                        settings={this.props.settings}
                                    />
                                );
                            }
                        })}
                    </tbody>
                </table>
                {this.renderUdwPortal()}
            </div>
        );
    }
}

TableElement.propTypes = {
    settings: PropTypes.shape({
        selectionAllowDuplicates: PropTypes.bool.isRequired,
        groupSettings: PropTypes.bool.isRequired,
        defaultBrowseLocation: PropTypes.number.isRequired,
        selectionLimit: PropTypes.number.isRequired,
        relationValidator: PropTypes.shape({
            allowedContentTypes: PropTypes.arrayOf(PropTypes.string).isRequired,
        }).isRequired,
    }).isRequired,
    validation: PropTypes.object.isRequired,
    languages: PropTypes.arrayOf(PropTypes.string).isRequired,
    input: PropTypes.instanceOf(Node),
    cloneContainer: PropTypes.instanceOf(Node),
    udwContainer: PropTypes.instanceOf(Node),
    udwModule: PropTypes.instanceOf(Component),
    attributeModules: PropTypes.arrayOf(PropTypes.instanceOf(Component)).isRequired,
    removeList: PropTypes.arrayOf(PropTypes.number).isRequired,
    items: PropTypes.arrayOf(PropTypes.object).isRequired,
    restInfo: PropTypes.shape({
        token: PropTypes.string.isRequired,
        siteaccess: PropTypes.string.isRequired,
    }).isRequired,
    value: PropTypes.array.isRequired,
    addGroupRow: PropTypes.func.isRequired,
    updateGroupName: PropTypes.func.isRequired,
    toggleRemove: PropTypes.func.isRequired,
    updateRowAttribute: PropTypes.func.isRequired,
    addRelationRows: PropTypes.func.isRequired,
    removeRows: PropTypes.func.isRequired,
};

const SortableList = SortableContainer(TableElement);

export default class Table extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return <SortableList {...this.props} />;
    }
}
