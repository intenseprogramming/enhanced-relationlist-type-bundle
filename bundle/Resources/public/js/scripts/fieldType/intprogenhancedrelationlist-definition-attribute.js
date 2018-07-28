(function () {
    const SELECTOR_CONTAINER = '.intprogenhancedrelationlist-definition-edit';
    const SELECTOR_STORAGE_INPUT = 'input.erl-json-value';

    const SELECTOR_LANGUAGE_SELECT = '.erl-attribute-head-language-select';

    const SELECTOR_ROOT_TABLE = '.erl-table';
    const SELECTOR_INPUT = 'input[data-value-path], select[data-value-path], textarea[data-value-path]';
    const SELECTOR_ROWS = 'tr[data-value-path]';
    const SELECTOR_BODY = '.erl-table tbody';
    const SELECTOR_DRAG_HANDLE = '.erl-attribute-item .erl-drag-handle';
    const SELECTOR_REMOVE = SELECTOR_ROWS + ' input.remove-attribute';

    const SELECTOR_ADD_CONTAINER = '.erl-attribute-head-input';
    const SELECTOR_ADD_IDENTIFIER = '.erl-attribute-new-identifier';
    const SELECTOR_ADD_TYPE = '.erl-attribute-new-type option:checked';
    const SELECTOR_ADD_BUTTON = '.erl-attribute-add-button';
    const SELECTOR_REMOVE_BUTTON = '.erl-attribute-remove-button';

    const setDeepValue = (target, path, value, multiple) => {
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

        if (multiple) {
            if (typeof object[keys[keys.length - 1]] === 'undefined') {
                object[keys[keys.length - 1]] = []
            }
            object[keys[keys.length - 1]].push(value);
        } else {
            object[keys[keys.length - 1]] = value;
        }
    };

    document.querySelectorAll(SELECTOR_CONTAINER).forEach(definitionContainer => {
        let sortHandler = false;

        const rootTable = definitionContainer.querySelector(SELECTOR_ROOT_TABLE);
        const headContainer = definitionContainer.querySelector(SELECTOR_ADD_CONTAINER);
        const attributeTemplates = JSON.parse(rootTable.getAttribute('data-attribute-template'));

        const updateJson = () => {
            const jsonData = {};

            rootTable.querySelectorAll(SELECTOR_ROWS).forEach(row => {
                row.querySelectorAll(SELECTOR_INPUT).forEach(inputItem => {
                    let nullable = inputItem.hasAttribute('data-erl-nullable');
                    let multiple = inputItem.hasAttribute('data-erl-multiple');
                    let ignoreEmpty = inputItem.hasAttribute('data-erl-ignore-empty');

                    if (row.closest(SELECTOR_ROWS)) {
                        let value = null;

                        switch (inputItem.tagName.toLowerCase()) {
                            case 'select':
                                value = [];
                                inputItem.querySelectorAll('option:checked').forEach(option => {
                                    if (option.hasAttribute('value')) {
                                        value.push(parseInt(option.value));
                                    }
                                });
                                break;
                            case 'input':
                                if (inputItem.type === 'checkbox') {
                                    value = inputItem.checked;
                                } else {
                                    if (!nullable || (nullable && inputItem.value !== '')) {
                                        value = inputItem.value;
                                    }
                                }
                                break;
                            default:
                                if (!nullable || (nullable && inputItem.value !== '')) {
                                    value = inputItem.value;
                                }
                        }

                        if (ignoreEmpty && value === null) {
                            return;
                        }

                        setDeepValue(jsonData, row.getAttribute('data-value-path') + '.' + inputItem.getAttribute('data-value-path'), value, multiple);
                    }
                });
            });

            definitionContainer.querySelector(SELECTOR_STORAGE_INPUT).value = JSON.stringify(jsonData);
        };
        const switchLanguage = () => {
            let languageOption = definitionContainer.querySelector(SELECTOR_LANGUAGE_SELECT).querySelector('option:checked');

            definitionContainer.querySelectorAll('*[data-erl-language-code]').forEach(item => {
                if (item.getAttribute('data-erl-language-code') !== languageOption.value && item.classList.contains('active')) {
                    item.classList.remove('active');
                } else {
                    item.classList.add('active');
                }
            });
        };
        const getInputFields = () => {
            const fields = [];

            definitionContainer.querySelectorAll(SELECTOR_INPUT).forEach(input => {
                if (input.closest(SELECTOR_ROWS)) {
                    fields.push(input);
                }
            });

            return fields;
        };
        const updateRemoveButton = () => {
            const checkedCheckboxes = definitionContainer.querySelectorAll(SELECTOR_REMOVE + ':checked');

            if (checkedCheckboxes.length === 0) {
                definitionContainer.querySelector(SELECTOR_REMOVE_BUTTON).setAttribute('disabled', 'disabled');
            } else {
                definitionContainer.querySelector(SELECTOR_REMOVE_BUTTON).removeAttribute('disabled');
            }
        };

        const addRowEventListeners = () => {
            rootTable.querySelectorAll(SELECTOR_REMOVE).forEach(element => {element.addEventListener('change', updateRemoveButton)});
            [...getInputFields()].forEach(element => {element.addEventListener('change', updateJson)});

            if (sortHandler) {
                sortHandler.destroy();
            }

            if (rootTable.querySelectorAll(SELECTOR_DRAG_HANDLE).length) {
                sortHandler = tableDragger(
                    rootTable,
                    {
                        dragHandler: SELECTOR_DRAG_HANDLE,
                        mode: 'row',
                        onlyBody: true
                    }
                );
                sortHandler.on('drop', updateJson)
            }
        };

        const removeAttribute = () => {
            event.preventDefault();

            definitionContainer.querySelectorAll(SELECTOR_REMOVE + ':checked').forEach(element => {
                element.closest(SELECTOR_ROWS).remove();
            });

            updateJson();
        };
        const addAttribute = (event) => {
            event.preventDefault();

            const identifier = headContainer.querySelector(SELECTOR_ADD_IDENTIFIER).value;
            const type = headContainer.querySelector(SELECTOR_ADD_TYPE).value;
            const markup = `${attributeTemplates[type].replace(/%identifier%/g, identifier)}`;

            definitionContainer.querySelector(SELECTOR_BODY).insertAdjacentHTML('beforeend', markup);

            addRowEventListeners();
            updateJson();
        };
        headContainer.querySelector(SELECTOR_ADD_BUTTON).addEventListener('click', addAttribute);
        addRowEventListeners();
        updateRemoveButton();

        definitionContainer.querySelector(SELECTOR_REMOVE_BUTTON).addEventListener('click', removeAttribute);
        definitionContainer.querySelector(SELECTOR_LANGUAGE_SELECT).addEventListener('change', switchLanguage);
    });
})();
