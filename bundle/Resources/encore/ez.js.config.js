const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry(
        'intprog-enhanced-relation-list-js',
        [
            // base components
            path.resolve(__dirname, '../../../lib/javascript/components/utils.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit/attribute/base-component.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list/attribute/base-component.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/group-list.js'),

            // attributes and definitions.
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit/attribute/checkbox.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit/attribute/integer.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit/attribute/selection.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-edit/attribute/string.js'),

            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list/attribute/checkbox.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list/attribute/integer.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list/attribute/selection.js'),
            path.resolve(__dirname, '../../../lib/javascript/components/content-type-edit/attribute-list/attribute/string.js'),

            // execution
            path.resolve(__dirname, '../assets/js/intprogenhancedrelationlist.js'),
            path.resolve(__dirname, '../assets/js/intprogenhancedrelationlist-definition-attribute.js'),
            path.resolve(__dirname, '../assets/js/intprogenhancedrelationlist-definition-group.js'),
        ]
    )
};
