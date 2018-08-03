(function () {
    const SELECTOR_ROOT_TABLE = '.intprogenhancedrelationlist-definition-attribute-edit .erl-table';

    const SELECTOR_OPTIONS_CONTAINER = '.erl-selection-attribute-edit';
    const SELECTOR_LAST_OPTION = '.input-group:last-child input[data-value-path^="settings.options."]';

    document.querySelectorAll(SELECTOR_ROOT_TABLE).forEach(table => {
        const addOption = (event) => {
            const selectionContainer = event.target.closest(SELECTOR_OPTIONS_CONTAINER);

            const optionTemplate = JSON.parse(selectionContainer.getAttribute('data-option-template'));
            selectionContainer.querySelector(SELECTOR_LAST_OPTION).removeEventListener('change', addOption);

            selectionContainer.insertAdjacentHTML('beforeend', optionTemplate.replace(/%index%/g, ));
            selectionContainer.querySelector(SELECTOR_LAST_OPTION).addEventListener('change', addOption);

            table.dispatchEvent(new Event('change-fields'));
        };

        const removeOption = (event) => {
            event.preventDefault();
            event.target.closest('.input-group').remove();

            table.dispatchEvent(new Event('change-values'));
        };

        const bindOptionEvents = (selectionContainer) => {
            selectionContainer.querySelector(SELECTOR_LAST_OPTION).addEventListener('change', addOption);
            selectionContainer.querySelectorAll('.input-group-text button').forEach(button => {button.addEventListener('click', removeOption)});
        };

        table.addEventListener('erl-attribute-add', () => {
            table.querySelectorAll(SELECTOR_OPTIONS_CONTAINER).forEach(bindOptionEvents);
        });

        table.querySelectorAll(SELECTOR_OPTIONS_CONTAINER).forEach(bindOptionEvents);
    });
})();
