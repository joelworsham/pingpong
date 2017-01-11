var PingPong_Admin_MatchCPTs
(function ($) {
    'use strict';

    var data = {}, l10n = {};

    if (typeof PingPong_Admin != 'undefined') {

        data = PingPong_Admin;
        l10n = typeof PingPong_Admin.l10n != 'undefined' ? PingPong_Admin.l10n : {};
    }

    var api = PingPong_Admin_MatchCPTs = {

        /**
         * Games table.
         *
         * @since {{VERSION}}
         */
        $games: null,

        /**
         * Initializes the object.
         *
         * @since {{VERSION}}
         */
        init: function () {

            api.get_elements();
            api.setup_handlers();
        },

        /**
         * Gets all jQuery elements needed.
         *
         * @since {{VERSION}}
         */
        get_elements: function () {

            api.$games = $('#pingpong-won-games');
        },

        /**
         * Sets up handlers on events.
         *
         * @since {{VERSION}}
         */
        setup_handlers: function () {

            $(document).on('click', '[data-expand-games]', api.toggle_games);
        },

        /**
         * Hides or shows games.
         *
         * @since {{VERSION}}
         *
         * @param e
         */
        toggle_games: function (e) {

            var hide = api.$games.is(':visible');

            e.preventDefault();

            if (hide) {

                $(this).html(l10n['view_matches']);
                api.hide_games();

            } else {

                $(this).html(l10n['hide_matches']);
                api.expand_games();
            }
        },

        /**
         * Shows games.
         *
         * @since {{VERSION}}
         */
        expand_games: function (e) {

            api.$games.show();
        },


        /**
         * Hides games.
         *
         * @since {{VERSION}}
         */
        hide_games: function (e) {

            api.$games.hide();
        }
    }

    $(api.init);
})(jQuery)