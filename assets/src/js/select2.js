(function($) {
    'use strict';

    function init() {

        var $select2 = $('select.pingpong-select2');

        if (!$select2.length) {

            return;
        }

        $select2.select2();
    }

    $(init);
})(jQuery);