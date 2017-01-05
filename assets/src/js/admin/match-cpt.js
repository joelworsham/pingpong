var PingPong_Admin_MatchCPTs
(function ($) {
    'use strict';

    var api = PingPong_Admin_MatchCPTs = {

        /**
         * Initializes this object.
         *
         * @since {{VERSION}}
         */
        init: function () {

            api.setup_handlers();
            api.initialize_match_type();
            api.initialize_player_selects();
        },

        /**
         * Sets up handlers on events.
         *
         * @since {{VERSION}}
         */
        setup_handlers: function () {

            $(document).on('click', '#match-type .rbm-field-radio input[type="radio"]', api.match_type_select);
            //$(document).on('change', '.player-select select', api.player_select);
        },

        /**
         * Initializes the active match type.
         *
         * @since {{VERSION}}
         */
        initialize_match_type: function () {

            $(document).find('#match-type .rbm-field-radio input[type="radio"]:checked').click();
        },

        /**
         * Initializes player selects.
         *
         * @since {{VERSION}}
         */
        initialize_player_selects: function () {

            $(document).find('.player-select select').change();
        },

        /**
         * Fires when selecting a match type.
         *
         * @since {{VERSION}}
         */
        match_type_select: function () {

            var type = $(this).val(),
                $mb_active = $('#match-settings-' + type),
                $mb_inactive = $('[id^="match-settings-"]:not(#match-settings-' + type + ')');

            if (!$mb_active.length) {

                return;
            }

            $mb_active.show();
            $mb_inactive.hide();
        },

        /**
         * Fires when selecting a player.
         *
         * Prevents duplicate player selects within player groups.
         *
         * @since {{VERSION}}
         */
        player_select: function () {

            var player = $(this).val(),
                $player_select_active = $(this).closest('.player-select'),
                previous_player = $player_select_active.attr('data-active'),
                $player_select_group = $(this).closest('.player-select-group'),
                $player_selects = $player_select_group.find('.player-select').not($player_select_active);

            if (previous_player) {

                $player_selects.find('option[value="' + previous_player + '"]').prop('disabled', false);
            }

            if (player) {

                $player_select_active.attr('data-active', player);
                $player_selects.find('option[value="' + player + '"]').prop('disabled', true);

            } else {

                $player_select_active.removeAttr('data-active');
            }
        }
    }

    $(api.init);
})(jQuery)