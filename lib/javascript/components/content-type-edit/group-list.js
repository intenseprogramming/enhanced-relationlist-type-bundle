import React, { Component, Fragment } from 'react';
import _ from 'lodash';
import arrayMove from 'array-move';
import PropTypes from 'prop-types';
import Table from './group-list/table';

const headStyle = {
    borderRadius: '0.25rem 0.25rem 0 0',
    padding: '0.25rem 0.75rem 0.25rem',
    lineHeight: '1.5',
};

export default class GroupList extends Component {
    constructor(props) {
        super(props);

        let internalIndex = 0;
        const value = [];
        _.forEach(this.props.value, (group, identifier) => {
            value.push({
                internalIndex: internalIndex++,
                identifier: identifier,
                item: group,
                markedForDelete: false,
            })
        });

        this.state = {
            value: value,
            nextIndex: internalIndex,
            language: this.props.languages[0].code,
            newGroupIdentifier: '',
            isNewGroupConflicting: false,
        }
    }

    componentDidUpdate() {
        this.updateInput();
    }

    changeLanguage(event) {
        const state = _.cloneDeep(this.state);
        state.language = event.target.querySelector('option:checked').value;
        this.setState(state);
    }

    changeNewGroupIdentifier(event) {
        const state = _.cloneDeep(this.state);
        state.newGroupIdentifier = event.target.value;

        let conflict = false;
        this.state.value.forEach(item => {
            if (item.identifier === state.newGroupIdentifier) {
                conflict = true;
            }
        });
        state.isNewGroupConflicting = conflict;

        this.setState(state);
    }

    addNewGroup(event) {
        event.preventDefault();
        const state = _.cloneDeep(this.state);
        state.value.push({
            internalIndex: state.nextIndex++,
            identifier: state.newGroupIdentifier,
            item: {},
            markedForDelete: false,
        });
        state.newGroupIdentifier = '';
        this.setState(state);
    }

    updateGroup(identifier, translations) {
        const state = _.cloneDeep(this.state);
        state.value = state.value.map(item => {
            if (item.identifier === identifier) {
                item.item = translations;
            }

            return item;
        });
        this.setState(state);
    }

    removeGroups(event) {
        event.preventDefault();
        const state = _.cloneDeep(this.state);
        state.value = state.value.filter(item => {
            return !item.markedForDelete;
        });
        this.setState(state);
    }

    updateInput() {
        const value = {};
        this.state.value.forEach(attribute => {
            value[attribute.identifier] = attribute.item;
        });
        this.props.input.setAttribute('value', JSON.stringify(value));
    }

    onSortEnd({ oldIndex, newIndex }) {
        if (newIndex !== oldIndex) {
            const state = _.cloneDeep(this.state);
            state.value = arrayMove(state.value, oldIndex, newIndex);

            this.setState(state);
        }
    }

    toggleDeleteMark(identifier) {
        const state = _.cloneDeep(this.state);
        state.value.some(group => {
            if (group.identifier === identifier) {
                group.markedForDelete = !group.markedForDelete;
            }
        });
        this.setState(state);
    }

    render() {
        const that = this;

        const languageSelectStyle = {};
        if (this.props.languages.length <= 1) {
            languageSelectStyle.display = 'none';
        }

        let rowsMarkedForDelete = false;
        this.state.value.some(group => {
            if (group.markedForDelete) {
                rowsMarkedForDelete = true;
                return true;
            }
        });

        return (
            <Fragment>
                <div className="ez-card__header ez-card__header--secondary d-flex justify-content-between" style={headStyle}>
                    <div className="p-2">
                        <select
                            className="form-control mb-0 erl-attribute-head-language-select"
                            style={languageSelectStyle}
                            value={this.state.language}
                            onChange={this.changeLanguage.bind(this)}
                        >
                            {that.props.languages.map(language => {
                                return <option key={language.code} value={language.code}>{language.name}</option>
                            })}
                        </select>
                    </div>
                    <div className="form-inline">
                        <div className="ez-card__field-control mr-2">
                            <input
                                type="text"
                                className={'form-control erl-attribute-new-identifier' + (this.state.isNewGroupConflicting ? ' border-danger' : '')}
                                placeholder={window.Translator.trans('field_definition.attribute_definition.group.identifier.placeholder', {}, 'enhancedrelationlist_definition_group')}
                                value={this.state.newGroupIdentifier}
                                onChange={this.changeNewGroupIdentifier.bind(this)}
                            />
                            &nbsp;
                            <button
                                type="button"
                                className="btn btn-primary btn erl-attribute-add-button"
                                onClick={this.addNewGroup.bind(this)}
                                disabled={this.state.isNewGroupConflicting || !this.state.newGroupIdentifier}
                            >
                                {window.Translator.trans('field_definition.group_difinition.add_group', {}, 'enhancedrelationlist_definition_group')}
                            </button>
                        </div>
                        <button
                            type="button"
                            title="Delete Content field definition"
                            className="btn btn-danger erl-attribute-remove-button"
                            disabled={!rowsMarkedForDelete}
                            onClick={this.removeGroups.bind(this)}
                        >
                            <svg className="ez-icon ez-icon--medium ez-icon--light">
                                <use xmlnsXlink="http://www.w3.org/1999/xlink" xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg?2.10.1#trash" />
                            </svg>
                        </button>
                    </div>
                </div>
                <Table
                    onSortEnd={this.onSortEnd.bind(this)}
                    rowStyle={headStyle}
                    items={this.state.value}
                    language={this.state.language}
                    helperContainer={this.props.cloneContainer}
                    updateGroup={this.updateGroup.bind(this)}
                    toggleDeleteMark={this.toggleDeleteMark.bind(this)}
                    useDragHandle
                    lockAxis="y"
                />
            </Fragment>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.ContentType.GroupList', GroupList);

GroupList.propTypes = {
    input: PropTypes.instanceOf(Element).isRequired,
    value: PropTypes.object.isRequired,
    cloneContainer: PropTypes.instanceOf(Element).isRequired,
    languages: PropTypes.object.isRequired,
};
