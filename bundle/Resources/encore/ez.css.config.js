const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry(
        'intprog-enhanced-relation-list-css',
        [path.resolve(__dirname, '../assets/scss/intprogenhancedrelationlist.scss')]
    )
};
