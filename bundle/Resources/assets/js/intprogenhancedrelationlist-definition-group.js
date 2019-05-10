(function (global, React, ReactDOM) {
    const SELECTOR_CONTAINER = '.intprogenhancedrelationlist-definition-group-edit';
    const SELECTOR_REACT_CONTAINER = '.enhanced-relation-group-definition-list-react-container';
    const SELECTOR_CLONE_CONTAINER = '.drag-clone-container tbody';

    document.querySelectorAll(SELECTOR_CONTAINER).forEach(fieldContainer => {
        const reactContainer = fieldContainer.querySelector(SELECTOR_REACT_CONTAINER);
        const cloneContainer = fieldContainer.querySelector(SELECTOR_CLONE_CONTAINER);
        const jsonValueInput = fieldContainer.querySelector('input.relation-groups-json');

        ReactDOM.render(
            React.createElement(
                global.eZ.IntProgEnhancedRelationList.ContentType.GroupList,
                Object.assign(
                    {
                        input: jsonValueInput,
                        value: JSON.parse(jsonValueInput.value),
                        cloneContainer: cloneContainer,
                        languages: JSON.parse(fieldContainer.querySelector('script[data-languages]').innerText),
                    }
                )
            ),
            reactContainer
        );
    });
})(window, window.React, window.ReactDOM);
