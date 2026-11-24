// MetCraft Widget Bootstrap - Frontend initialization
(function() {
    'use strict';

    // ReCAPTCHA v3 execution
    function executeRecaptcha(action, callback) {
        if (typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function() {
                grecaptcha.execute(null, { action: action }).then(callback);
            });
        }
    }

    window.MetCraftWidget = {
        executeRecaptcha: executeRecaptcha
    };

})();
