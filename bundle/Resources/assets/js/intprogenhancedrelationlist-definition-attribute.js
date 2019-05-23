document.addEventListener('DOMContentLoaded', function _onLoad() {
    document.removeEventListener('DOMContentLoaded', _onLoad);

    ((global, React, ReactDOM) => {
        const SELECTOR_CONTAINER = '.intprogenhancedrelationlist-definition-attribute-edit';
        const SELECTOR_REACT_CONTAINER = '.enhanced-relation-attribute-definition-list-react-container';
        const SELECTOR_CLONE_CONTAINER = '.drag-clone-container tbody';

        document.querySelectorAll(SELECTOR_CONTAINER).forEach(fieldContainer => {
            const reactContainer = fieldContainer.querySelector(SELECTOR_REACT_CONTAINER);
            const cloneContainer = fieldContainer.querySelector(SELECTOR_CLONE_CONTAINER);
            const jsonValueInput = fieldContainer.querySelector('input.relation-attributes-json');

            ReactDOM.render(
                React.createElement(
                    global.eZ.IntProgEnhancedRelationList.ContentType.AttributeList,
                    Object.assign(
                        {
                            input: jsonValueInput,
                            value: JSON.parse(jsonValueInput.value),
                            cloneContainer: cloneContainer,
                            languages: JSON.parse(fieldContainer.querySelector('script[data-languages]').innerText),
                            attributeModules: global.eZ.IntProgEnhancedRelationList.modules.attributeDefinitions,
                        }
                    )
                ),
                reactContainer
            );
        });
    })(window, window.React, window.ReactDOM);
});
