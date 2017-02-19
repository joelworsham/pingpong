(function($) {
    'use strict';

    function init() {

        match_type_switch();
    }

    function match_type_switch() {

        var $match_type = $('#match_type');

        if (!$match_type.length) {

            return;
        }

        $match_type.change(function () {

            var type = $(this).val();
            var $opponent = $('#match-setup-field-opponent_player');
            var $team = $('#match-setup-field-opponent_team');

            switch (type) {

                case 'singles':

                    $opponent.show();
                    $team.hide();
                    break;

                case 'doubles':
                case 'team':

                    $team.show();
                    $opponent.hide();
                    break;
            }
        });
    }

    $(init);
})(jQuery);