const Utils = {
    translate: (languages, translations, fallback) => {
        let translation = false;

        languages.some((language) => {
            if (translations[language] !== undefined) {
                translation = translations[language];
                return true;
            }
            return false;
        });

        if (translation) {
            return translation;
        }

        return fallback;
    },
};

window.eZ.addConfig('IntProgEnhancedRelationList.Utils', Utils);

export default Utils;
