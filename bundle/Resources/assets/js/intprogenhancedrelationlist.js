document.addEventListener('DOMContentLoaded', function _onLoad() {
    document.removeEventListener('DOMContentLoaded', _onLoad);

    (function (global, React, ReactDOM) {
        const SELECTOR_CONTAINER = '.enhanced-relation-list-container';
        const SELECTOR_REACT_CONTAINER = '.enhanced-relation-list-react-container';
        const SELECTOR_CLONE_CONTAINER = '.drag-clone-container tbody';

        // class EnhancedRelationListValidator extends global.eZ.BaseFieldValidator {
        //     /**
        //      * Validates the input
        //      *
        //      * @method validateInput
        //      * @param {Event} event
        //      * @returns {Object}
        //      * @memberof EzObjectRelationListValidator
        //      */
        //     validateInput({currentTarget}) {
        //         const isRequired = currentTarget.required;
        //         const isEmpty = !currentTarget.value.length;
        //         const hasCorrectValues = currentTarget.value.split(',').every(id => !isNaN(parseInt(id, 10)));
        //         const fieldContainer = currentTarget.closest(SELECTOR_FIELD);
        //         const label = fieldContainer.querySelector('.ez-field-edit__label').innerHTML;
        //         const result = { isError: false };
        //
        //         if (isRequired && isEmpty) {
        //             result.isError = true;
        //             result.errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', label);
        //         } else if (!isEmpty && !hasCorrectValues) {
        //             result.isError = true;
        //             result.errorMessage = global.eZ.errors.invalidValue.replace('{fieldName}', label);
        //         }
        //
        //         return result;
        //     }
        // }

        document.querySelectorAll(SELECTOR_CONTAINER).forEach(fieldContainer => {
            const udwContainer = document.getElementById('react-udw');
            const reactContainer = fieldContainer.querySelector(SELECTOR_REACT_CONTAINER);
            const cloneContainer = fieldContainer.querySelector(SELECTOR_CLONE_CONTAINER);
            const jsonValueInput = fieldContainer.querySelector('input.relation-json');
            const token = document.querySelector('meta[name="CSRF-Token"]').content;
            const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

            ReactDOM.render(
                React.createElement(
                    global.eZ.IntProgEnhancedRelationList.ContentEdit,
                    Object.assign(
                        {
                            input: jsonValueInput,
                            value: JSON.parse(jsonValueInput.value),
                            cloneContainer: cloneContainer,
                            udwContainer: udwContainer,
                            udwModule: global.eZ.modules.UniversalDiscovery,
                            settings: JSON.parse(fieldContainer.querySelector('script[data-settings]').innerText),
                            validation: JSON.parse(fieldContainer.querySelector('script[data-validation]').innerText),
                            errors: JSON.parse(fieldContainer.querySelector('script[data-errors]').innerText),
                            languages: JSON.parse(fieldContainer.querySelector('script[data-languages]').innerText),
                            attributeModules: global.eZ.IntProgEnhancedRelationList.modules.attributes,
                            restInfo: {token, siteaccess},
                        }
                    )
                ),
                reactContainer
            );

            // validator.init();

            // global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
            //     [...global.eZ.fieldTypeValidators, validator] :
            //     [validator];
        });
    })(window, window.React, window.ReactDOM);
});
