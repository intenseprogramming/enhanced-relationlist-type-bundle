import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import _ from 'lodash';
import arrayMove from 'array-move';
import Table from './attribute-list/table';

const headStyle = {
    borderRadius: '0.25rem 0.25rem 0 0',
    padding: '0.25rem 0.75rem 0.25rem',
    lineHeight: '1.5',
};

export default class AttributeList extends Component {
    constructor(props) {
        super(props);

        let internalIndex = 0;
        const value = [];
        _.forEach(this.props.value, (attribute, identifier) => {
            value.push({
                internalIndex: internalIndex++,
                identifier: identifier,
                item: attribute,
                markedForDelete: false,
            })
        });

        // get first attribute type to set to newAttribute.
        let newType = '';
        for (let identifier in this.props.attributeModules) {
            if (this.props.attributeModules.hasOwnProperty(identifier)) {
                newType = identifier;
                break;
            }
        }

        this.state = {
            value: value,
            nextIndex: internalIndex,
            language: this.props.languages[0].code,
            newAttribute: {
                identifier: '',
                type: newType,
                isConflicting: false,
            }
        }
    }

    componentDidUpdate() {
        this.updateInput();
    }

    changeNewAttributeIdentifier(event) {
        const newValue = event.target.value.trim().toLowerCase().replace(/[^a-z_-]/g, '');

        const state = _.cloneDeep(this.state);
        let conflicting = false;
        state.value.some(attribute => {
            if (attribute.identifier === newValue) {
                conflicting = true;
                return true;
            }
        });
        state.newAttribute.identifier = newValue;
        state.newAttribute.isConflicting = conflicting;
        this.setState(state);
    }

    changeNewAttributeType(event) {
        const state = _.cloneDeep(this.state);
        state.newAttribute.type = event.target.querySelector('option:checked').value;
        this.setState(state);
    }

    addNewAttribute(event) {
        event.preventDefault();
        if (!this.state.newAttribute.identifier || this.state.newAttribute.isConflicting) {
            return;
        }

        const state = _.cloneDeep(this.state);

        let settings = [];
        if (this.props.attributeModules[state.newAttribute.type].getEmptySettings) {
            settings = this.props.attributeModules[state.newAttribute.type].getEmptySettings();
        }

        state.value.push({
            identifier: state.newAttribute.identifier,
            internalIndex: state.nextIndex++,
            item: {
                names: {},
                required: false,
                settings: settings,
                type: state.newAttribute.type,
            },
            markedForDelete: false,
        });

        state.newAttribute.identifier = '';
        this.setState(state);
    }

    changeLanguage(event) {
        const state = _.cloneDeep(this.state);
        state.language = event.target.querySelector('option:checked').value;
        this.setState(state);
    }

    toggleDeleteMark(identifier) {
        const state = _.cloneDeep(this.state);
        state.value.some(attribute => {
            if (attribute.identifier === identifier) {
                attribute.markedForDelete = !attribute.markedForDelete;
            }
        });
        this.setState(state);
    }

    removeAttributes(event) {
        event.preventDefault();

        const state = _.cloneDeep(this.state);
        state.value = state.value.filter(item => {return !item.markedForDelete});

        let conflicting = false;
        state.value.some(attribute => {
            if (attribute.identifier === state.newAttribute.identifier) {
                conflicting = true;
                return true;
            }
        });
        state.newAttribute.isConflicting = conflicting;

        this.setState(state);
    }

    updateAttribute(identifier, newValue) {
        const state = _.cloneDeep(this.state);

        state.value.some(attribute => {
            if (identifier === attribute.identifier) {
                attribute.item = newValue;
                return true;
            }
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

    renderAttributeSelection() {
        const result = [];

        for (let identifier in this.props.attributeModules) {
            if (this.props.attributeModules.hasOwnProperty(identifier)) {
                result.push(
                    <option value={identifier}>
                        {window.Translator.trans(identifier + '.name', {}, 'enhancedrelationlist_definition_attribute')}
                    </option>
                );
            }
        }

        return (
            <select
                className="erl-attribute-selection form-control erl-attribute-new-type"
                value={this.state.newAttribute.type}
                onChange={this.changeNewAttributeType.bind(this)}
            >
                {result}
            </select>
        );
    }

    render() {
        const that = this;
        const languageSelectStyle = {};
        if (this.props.languages.length <= 1) {
            languageSelectStyle.display = 'none';
        }

        let rowsMarkedForDelete = false;
        this.state.value.some(attribute => {
            if (attribute.markedForDelete) {
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
                                className={'form-control erl-attribute-new-identifier' + (this.state.newAttribute.isConflicting ? ' border-danger' : '')}
                                placeholder="Attribute identifier" value={this.state.newAttribute.identifier}
                                onChange={this.changeNewAttributeIdentifier.bind(this)}
                            />
                            &nbsp;
                            {that.renderAttributeSelection()}
                            &nbsp;
                            <button
                                type="button"
                                className="btn btn-primary btn erl-attribute-add-button"
                                onClick={this.addNewAttribute.bind(this)}
                                disabled={this.state.newAttribute.isConflicting || !this.state.newAttribute.identifier}
                            >
                                {window.Translator.trans('field_definition.attribute_difinition.add_attribute', {}, 'enhancedrelationlist_definition_attribute')}
                            </button>
                        </div>
                        <button
                            type="button"
                            title="Delete Content field definition"
                            className="btn btn-danger erl-attribute-remove-button"
                            disabled={!rowsMarkedForDelete}
                            onClick={this.removeAttributes.bind(this)}
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
                    attributeModules={this.props.attributeModules}
                    helperContainer={this.props.cloneContainer}
                    updateAttribute={this.updateAttribute.bind(this)}
                    toggleDeleteMark={this.toggleDeleteMark.bind(this)}
                    useDragHandle
                    lockAxis="y"
                />
            </Fragment>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.ContentType.AttributeList', AttributeList);

AttributeList.propTypes = {
    input: PropTypes.instanceOf(Element).isRequired,
    value: PropTypes.object.isRequired,
    cloneContainer: PropTypes.instanceOf(Element).isRequired,
    languages: PropTypes.object.isRequired,
    attributeModules: PropTypes.array.isRequired,
};
