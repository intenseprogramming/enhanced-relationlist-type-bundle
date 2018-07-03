(function (global, React, ReactDOM) {
    const SELECTOR_FIELD = '.ez-field-edit--intprogenhancedrelationlist';
    const SELECTOR_BTN_ADD = '.ez-relations__table-action--create';
    const SELECTOR_TABLE = '.relation-root-table';
    const SELECTOR_INPUT = '.relation-root-table input, .relation-root-table textarea, .relation-root-table select';
    const SELECTOR_DRAG_HANDLE = '.erl-relation-item .erl-drag-handle';
    const SELECTOR_INPUT_ROW = '.erl-relation-item';

    // class EzObjectRelationListValidator extends global.eZ.BaseFieldValidator {
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

    [
        ...document.querySelectorAll(SELECTOR_FIELD)
    ].forEach(fieldContainer => {
        let itemIndex = fieldContainer.querySelectorAll(SELECTOR_INPUT_ROW).length - 1;
        let sortHandler = false;

        // const validator = new EzObjectRelationListValidator({
        //     classInvalid: 'is-invalid',
        //     fieldContainer,
        //     eventsMap: [
        //         {
        //             selector: SELECTOR_INPUT,
        //             eventName: 'blur',
        //             callback: 'validateInput',
        //             errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
        //         },
        //         {
        //             isValueValidator: false,
        //             selector: SELECTOR_INPUT,
        //             eventName: EVENT_CUSTOM,
        //             callback: 'validateInput',
        //             errorNodeSelectors: [SELECTOR_LABEL_WRAPPER]
        //         }
        //     ]
        // });
        const jsonValueInput = fieldContainer.querySelector('input.relation-json');
        const udwContainer = document.getElementById('react-udw');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const relationsContainer = fieldContainer.querySelector('.ez-relations__list');
        const addBtn = fieldContainer.querySelector(SELECTOR_BTN_ADD);
        const trashBtn = fieldContainer.querySelector('.ez-relations__table-action--remove');
        const selectedItemsLimit = parseInt(relationsContainer.dataset.limit, 10);
        const startingLocationId = relationsContainer.dataset.defaultLocation !== '0'
            ? parseInt(relationsContainer.dataset.defaultLocation, 10)
            : window.eZ.adminUiConfig.universalDiscoveryWidget.startingLocationId;
        const allowedContentTypes = relationsContainer.dataset.allowedContentTypes.split(',').filter(item => item.length);
        const setDeepValue = (target, path, value) => {
            const keys = path.split('.');
            let object = target;
            for (let i = 0; i < keys.length - 1; i++) {
                let key = keys[i];
                if (key in object) {
                    object = object[key];
                } else {
                    object[key] = {};
                    object = object[key];
                }
            }
            object[keys[keys.length - 1]] = value;
        };
        const updateJson = (item) => {
            if (item !== undefined && item.target !== undefined && !item.target.closest('td').getAttribute('data-value-path')) {
                return;
            }

            const jsonValue = [];

            fieldContainer.querySelectorAll(SELECTOR_INPUT_ROW).forEach(item => {
                let itemValue = {};

                item.querySelectorAll(SELECTOR_INPUT).forEach(inputItem => {
                    let valuePath = inputItem.closest('td').getAttribute('data-value-path');

                    if (!valuePath) {
                        return;
                    }

                    let value = null;

                    switch (inputItem.tagName.toLowerCase()) {
                        case 'select':
                            if (inputItem.multiple) {
                                value = [];
                                let options = inputItem && inputItem.options;
                                for (let i=0, iLen=options.length; i<iLen; i++) {
                                    if (options[i].selected) {
                                        value.push(parseInt(options[i].value));
                                    }
                                }
                                break;
                            }
                            value = inputItem.value;
                            break;
                        case 'input':
                            if (inputItem.type === 'checkbox') {
                                break;
                            }
                            value = inputItem.value;
                            break;
                        default:
                            value = inputItem.value;
                    }

                    setDeepValue(itemValue, valuePath, value);
                });

                jsonValue.push(itemValue);
            });

            jsonValueInput.value = JSON.stringify(jsonValue);
        };
        fieldContainer.querySelectorAll(SELECTOR_INPUT).forEach((item) => {item.addEventListener('change', updateJson);});
        const closeUDW = () => ReactDOM.unmountComponentAtNode(udwContainer);
        const renderRows = (items) => items.forEach((...args) => relationsContainer.insertAdjacentHTML('beforeend', renderRow(...args)));
        const onConfirm = (items) => {
            renderRows(items);
            attachRowEventHandlers();

            closeUDW();
            updateAddBtnState();
            updateJson();
        };
        const canSelectContent = ({item, itemsCount}, callback) => {
            const isAllowedContentType = allowedContentTypes.length ?
                allowedContentTypes.includes(item.ContentInfo.Content.ContentTypeInfo.identifier) :
                true;

            if (!isAllowedContentType) {
                return callback(false);
            }

            // TODO: add setting to enable/disabled duplicate selection.

            const canSelect = !selectedItemsLimit || (fieldContainer.querySelectorAll(SELECTOR_INPUT_ROW).length + itemsCount) < selectedItemsLimit;

            callback(canSelect);
        };
        const openUDW = (event) => {
            event.preventDefault();

            const config = JSON.parse(event.currentTarget.dataset.udwConfig);

            ReactDOM.render(React.createElement(global.eZ.modules.UniversalDiscovery, Object.assign({
                onConfirm,
                onCancel: closeUDW,
                confirmLabel: 'Confirm selection',
                title: 'Select content',
                multiple: selectedItemsLimit !== 1,
                selectedItemsLimit,
                startingLocationId,
                restInfo: { token, siteaccess },
                canSelectContent
            }, config)), udwContainer);
        };
        const renderRow = (item) => {
            itemIndex++;

            let inputFields = '';
            fieldContainer.querySelectorAll('.input-attributes-layout .input-element-wrapper').forEach(item => {
                let valuePath = item.closest('.input-element-wrapper').getAttribute('data-value-path');

                inputFields += `
                    <td data-value-path="${valuePath}">
                        ${item.innerHTML}
                    </td>
                `;
            });

            return `
                <tr class="ez-relations__item erl-relation-item">
                    <td class="erl-drag-column erl-drag-handle">
                        <i class="drag-icon" />
                    </td>
                    <td class="slim"><label class="slim-content"><input type="checkbox"></label></td>
                    <td data-value-path="contentId">
                        ${item.ContentInfo.Content.Name}
                        <input type="hidden" value="${item.ContentInfo.Content._id}">
                    </td>
                    ${inputFields}
                </tr>
            `;
        };
        const updateAddBtnState = () => {
            const methodName = (!selectedItemsLimit || selectedItems.length < selectedItemsLimit) ? 'removeAttribute' : 'setAttribute';

            addBtn[methodName]('disabled', true);
        };
        const updateTrashBtnState = (event) => {
            if (!event.target.hasAttribute('type') || event.target.type !== 'checkbox') {
                return;
            }

            const anySelected = findDeleteCheckboxes().some(item => item.checked === true);
            const methodName = anySelected ? 'removeAttribute' : 'setAttribute';

            trashBtn[methodName]('disabled', true);
        };
        const removeItem = (event) => {
            event.preventDefault();

            const removedItems = [];

            [...relationsContainer.querySelectorAll('input:checked')].forEach(input => {
                removedItems.push(parseInt(input.value, 10));

                input.closest('tr').remove();
            });

            updateJson();
            updateAddBtnState();
        };
        const findDeleteCheckboxes = () => {
            return [...relationsContainer.querySelectorAll('.erl-remove-control[type="checkbox"]')];
        };
        const attachRowEventHandlers = () => {
            fieldContainer.querySelectorAll(SELECTOR_INPUT).forEach((item) => {item.addEventListener('change', updateJson);});

            if (sortHandler) {
                sortHandler.destroy();
            }

            if (fieldContainer.querySelector(SELECTOR_TABLE).querySelectorAll(SELECTOR_DRAG_HANDLE).length) {
                sortHandler = tableDragger(
                    fieldContainer.querySelector(SELECTOR_TABLE),
                    {
                        dragHandler: SELECTOR_DRAG_HANDLE,
                        mode: 'row',
                        onlyBody: true
                    }
                );
                sortHandler.on('drop', updateJson)
            }
        };

        updateAddBtnState();
        attachRowEventHandlers();

        [
            ...fieldContainer.querySelectorAll(SELECTOR_BTN_ADD),
            ...fieldContainer.querySelectorAll('.ez-relations__cta-btn')
        ].forEach(btn => btn.addEventListener('click', openUDW, false));

        trashBtn.addEventListener('click', removeItem, false);
        relationsContainer.addEventListener('change', updateTrashBtnState, false);

        // validator.init();

        // global.eZ.fieldTypeValidators = global.eZ.fieldTypeValidators ?
        //     [...global.eZ.fieldTypeValidators, validator] :
        //     [validator];
    });
})(window, window.React, window.ReactDOM);
