import React, {Fragment} from 'react';
import BaseAttributeComponent from './base-component';
import Utils from '../../../utils';
import _ from 'lodash';

export default class Selection extends BaseAttributeComponent {
    constructor(props) {
        super(props);

        let nextIndex = 0;

        // ensure options is an object instead of array.
        if (this.state.value.options.forEach) {
            const options = {};
            this.state.value.options.forEach((item, index) => {
                options['' + index] = item;
                nextIndex = index;
            });
            nextIndex++;
            this.state.value.options = options;
        } else {
            for (let index in this.state.value.options) {
                if (this.state.value.options.hasOwnProperty(index) && index >= nextIndex) {
                    nextIndex = parseInt(index) + 1;
                }
            }
        }

        // adding additional option which will be ignored when updated and still empty.
        this.state.value.options['' + (nextIndex++)] = {};

        this.state.nextIndex = nextIndex;
    }

    componentDidUpdate(nextProps, nextState) {
        if (this.state && nextState && this.state.value !== nextState.value) {
            const state = _.cloneDeep(this.state);
            delete state.value.options['' + (state.nextIndex - 1)];

            this.props.updateAttributeSettings(state.value);
        }
    }

    static getEmptySettings() {
        return {
            multiple: false,
            options: {},
        }
    }

    toggleMultiple() {
        const state = _.cloneDeep(this.state);
        state.value.multiple = !state.value.multiple;
        this.setState(state);
    }

    removeOption(event, index) {
        event.preventDefault();

        const state = _.cloneDeep(this.state);
        delete state.value.options[index];
        this.setState(state);
    }

    updateOption(event, index) {
        const state = _.cloneDeep(this.state);
        state.value.options[index][this.props.language] = event.target.value;
        this.setState(state);
    }

    activateOption(event, index) {
        const state = _.cloneDeep(this.state);
        state.value.options[index][this.props.language] = event.target.value;
        state.value.options['' + (state.nextIndex++)] = {};
        this.setState(state);
    }

    renderOptions() {
        const options = [];
        const lastIndex = this.state.nextIndex - 1;

        _.forEach(this.state.value.options, (option, index) => {
            options.push(
                <div key={index} className="input-group" data-index="0">
                    <div className="input-group-prepend">
                        <button className="btn btn-danger" type="button" onClick={event => {this.removeOption(event, index)}} disabled={parseInt(index) === lastIndex}>
                            {window.Translator.trans('erl.attribute.type.selection.options.remove', {}, 'enhancedrelationlist_definition_attribute')}
                        </button>
                    </div>
                    <input
                        type="text"
                        className="form-control"
                        value={Utils.translate([this.props.language], option, '')}
                        onChange={event => {
                            parseInt(index) !== lastIndex ? this.updateOption(event, index) : this.activateOption(event, index)
                        }}
                    />
                </div>
            );
        });

        return options;
    }

    render() {
        return (
            <Fragment>
                <div className="form-check form-check-inline">
                    <label className="checkbox-inline form-check-label">
                        <input className="form-check-input" type="checkbox" checked={this.state.value.multiple} onChange={this.toggleMultiple.bind(this)} />
                        {window.Translator.trans('erl.attribute.type.selection.multiple', {}, 'enhancedrelationlist_definition_attribute')}
                    </label>
                </div>
                <div className="erl-selection-attribute-edit">
                    <label className="form-control-label">
                        {window.Translator.trans('erl.attribute.type.selection.options', {}, 'enhancedrelationlist_definition_attribute')}
                    </label>
                    {this.renderOptions()}
                </div>
            </Fragment>
        );
    }
}

window.eZ.addConfig('IntProgEnhancedRelationList.modules.attributeDefinitions.selection', Selection);
