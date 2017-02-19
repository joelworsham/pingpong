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
         * jQuery object for the scores modal.
         *
         * @since {{VERSION}}
         */
        $scores_modal: null,

        /**
         * jQuery object for the games input.
         *
         * @since {{VERSION}}
         */
        $games_input: null,

        /**
         * Scores to be submitted for saving.
         *
         * @since {{VERSION}}
         */
        scores: {
            players: {},
            teams: {},
            matches: [],
        },

        /**
         * Initializes this object.
         *
         * @since {{VERSION}}
         */
        init: function () {

            api.get_elements();
            api.setup_handlers();
            api.initialize_match_type();

            if (data.scores) {

                api.scores = data.scores;
            }
        },

        /**
         * Gets all jQuery elements needed.
         *
         * @since {{VERSION}}
         */
        get_elements: function () {

            api.$scores_modal = $('#pingpong-scores-modal');
            api.$games_input = $('select[name="_rbm_games"]');
            api.$game_type_input = $('input[name="_rbm_type"]');
        },

        /**
         * Sets up handlers on events.
         *
         * @since {{VERSION}}
         */
        setup_handlers: function () {

            $(document).on('click', '#match-type .rbm-field-radio input[type="radio"]', api.match_type_select);
            $(document).on('click', '[data-scores-close]', api.scores_modal_close);
            $(document).on('click', '[data-scores-open]', api.scores_modal_add_scores);
            $(document).on('click', '[data-scores-submit]', api.scores_modal_submit);
            $(document).on('change', '.pingpong-scores-table input[type="number"]', api.scores_update);

            $(document).on('click',
                'body.post-type-match.pingpong-matches-complete .wp-list-table a.row-title,' +
                'body.post-type-match.pingpong-matches-complete .wp-list-table .row-actions .edit a',
                api.scores_modal_preview);
        },

        /**
         * Initializes the active match type.
         *
         * @since {{VERSION}}
         */
        initialize_match_type: function () {

            $('#match-type .rbm-field-radio input[type="radio"]:checked').click();
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
         * Opens the scores modal.
         *
         * @since {{VERSION}}
         */
        scores_modal_add_scores: function () {

            var games = api.$games_input.val(),
                game_type = api.$game_type_input.filter(':checked').val(),
                $table = api.$scores_modal.find('.pingpong-scores-table'),
                $thead_row = $table.find('thead tr'),
                $tbody = $table.find('tbody'),
                $tbody_rows,
                $players_select,
                $teams_select,
                player_IDs, team_IDs, team_names,
                table_data,
                columns = 0, rows = [], footer = [], i;

            $('body').addClass('pingpong-modal-open');

            columns = parseInt(games);

            api.scores_modal_open();

            // Create rows
            switch (game_type) {

                case 'singles':

                    var players = [];

                    $players_select = $('select[name="_rbm_singles_players[]"]');

                    if (player_IDs = $players_select.val()) {

                        for (i = 0; i < player_IDs.length; i++) {

                            players.push({
                                name: $players_select.find('option[value="' + player_IDs[i] + '"]').html().trim(),
                                id: player_IDs[i]
                            });
                        }

                        table_data = api.scores_modal_get_table_data('singles', players)
                    }

                    api.scores_modal_populate_table(columns, table_data.rows, table_data.footer);

                    break;

                case 'doubles':

                    var teams = [];

                    $teams_select = $('select[name="_rbm_doubles_teams[]"]');

                    if (team_IDs = $teams_select.val()) {

                        for (i = 0; i < team_IDs.length; i++) {

                            teams.push({
                                name: $teams_select.find('option[value="' + team_IDs[i] + '"]').html().trim(),
                                id: team_IDs[i]
                            });
                        }
                    }

                    api.scores_modal_get_table_data('doubles', false, teams);

                    api.scores_modal_populate_table(columns, table_data.rows);

                    break;

                case 'team':

                    $teams_select = $('select[name="_rbm_teams[]"]');

                    if (team_IDs = $teams_select.val()) {

                        api.$scores_modal.find('.pingpong-scores-container').addClass('loading');

                        team_names = {};

                        for (i = 0; i < team_IDs.length; i++) {

                            team_names[team_IDs[i]] = $teams_select.find('option[value="' + team_IDs[i] + '"]').html().trim();
                        }

                        $.post(
                            ajaxurl,
                            {
                                action: 'pingpong_get_team_players',
                                team_IDs: team_IDs
                            },
                            function (response) {

                                var teams = response.data;

                                api.$scores_modal.find('.pingpong-scores-container').removeClass('loading');

                                if (typeof response.success == 'undefined' || !response.success) {

                                    return;
                                }

                                table_data = api.scores_modal_get_table_data('team', false, teams);

                                api.scores_modal_populate_table(columns, table_data.rows, table_data.footer);
                            }
                        )
                    }

                    break;
            }
        },

        /**
         * Preview scores.
         *
         * @since {{VERSION}}
         */
        scores_modal_preview: function (e) {

            var match_id = $(this).closest('tr').attr('id').match(/post-(\d+)/)[1];

            e.preventDefault();

            $.post(
                ajaxurl,
                {
                    action: 'pingpong_get_match_scores',
                    match_id: match_id
                },
                function (response) {

                    var table_data;

                    if (typeof response.success == 'undefined' || !response.success) {

                        return;
                    }

                    switch (response.data.type) {

                        case 'singles':

                            table_data = api.scores_modal_get_table_data('singles', response.data.scores.players);

                            break;

                        case 'doubles':
                            break;

                        case 'team':

                            table_data = api.scores_modal_get_table_data('team', false, response.data.scores.teams);

                            break;
                    }

                    api.scores_modal_populate_table(
                        response.data.scores.matches[0].length,
                        table_data.rows,
                        table_data.footer
                    );

                    api.scores_modal_populate_scores(response.data.scores.matches);

                    // Disable because it's only a preview
                    api.$scores_modal.find('input[type="number"]').prop('disabled', true);

                    api.scores_modal_open();
                }
            )
        },

        /**
         * Opens the scores modal.
         *
         * @since {{VERSION}}
         */
        scores_modal_open: function () {

            api.$scores_modal.show();
        },

        /**
         * Gets table data for use in populating it.
         *
         * @since {{VERSION}}
         *
         * @param type
         * @param players
         * @param teams
         */
        scores_modal_get_table_data: function (type, players, teams) {

            var rows = [], footer = [], i;

            switch (type) {

                case 'singles':

                    for (i = 0; i < players.length; i++) {

                        rows.push({
                            label: players[i]['name'],
                            player_id: players[i]['id']
                        });

                        footer.push({
                            label: players[i]['name'],
                            player_id: players[i]['id'],
                        });
                    }

                    break;

                case 'doubles':

                    for (i = 0; i < teams.length; i++) {

                        rows.push({
                            label: teams[i]['name'],
                            team_id: teams[i]['id']
                        });
                    }

                    break;

                case 'team':

                    rows.push({
                        label: '<strong>' + teams[0]['name'] + ':</strong> ' + teams[0]['players'][0]['name'],
                        team_id: teams[0]['id'],
                        player_id: teams[0]['players'][0]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[1]['name'] + ':</strong> ' + teams[1]['players'][0]['name'],
                        team_id: teams[1]['id'],
                        player_id: teams[1]['players'][0]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[0]['name'] + ':</strong> ' + teams[0]['players'][1]['name'],
                        team_id: teams[0]['id'],
                        player_id: teams[0]['players'][1]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[1]['name'] + ':</strong> ' + teams[1]['players'][1]['name'],
                        team_id: teams[1]['id'],
                        player_id: teams[1]['players'][1]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[0]['name'] + ':</strong> ' + teams[0]['players'][0]['name'],
                        team_id: teams[0]['id'],
                        player_id: teams[0]['players'][0]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[1]['name'] + ':</strong> ' + teams[1]['players'][1]['name'],
                        team_id: teams[1]['id'],
                        player_id: teams[1]['players'][1]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[0]['name'] + ':</strong> ' + teams[0]['players'][1]['name'],
                        team_id: teams[0]['id'],
                        player_id: teams[0]['players'][1]['id']
                    });

                    rows.push({
                        label: '<strong>' + teams[1]['name'] + ':</strong> ' + teams[1]['players'][0]['name'],
                        team_id: teams[1]['id'],
                        player_id: teams[1]['players'][0]['id']
                    });

                    footer.push({
                        label: teams[0]['name'],
                        team_id: teams[0]['id'],
                    });

                    footer.push({
                        label: teams[1]['name'],
                        team_id: teams[1]['id'],
                    });

                    footer.push({
                        label: teams[0]['players'][0]['name'],
                        player_id: teams[0]['players'][0]['id'],
                    });

                    footer.push({
                        label: teams[0]['players'][1]['name'],
                        player_id: teams[0]['players'][1]['id'],
                    });

                    footer.push({
                        label: teams[1]['players'][0]['name'],
                        player_id: teams[1]['players'][0]['id'],
                    });

                    footer.push({
                        label: teams[1]['players'][1]['name'],
                        player_id: teams[1]['players'][1]['id'],
                    });

                    break;
            }

            return {
                rows: rows,
                footer: footer
            };
        },

        /**
         * Populates the scores modal table.
         *
         * @since {{VERSION}}
         *
         * @param columns
         * @param rows
         * @param footer
         */
        scores_modal_populate_table: function (columns, rows, footer) {

            var $table = api.$scores_modal.find('.pingpong-scores-table'),
                $tbody = $table.find('tbody'),
                $tfoot = $table.find('tfoot'),
                $thead_row,
                $tbody_rows,
                row,
                player_id, team_id,
                row_i, column_i, foot_i;

            // Create thead
            $table.find('thead').append('<tr />');
            $thead_row = $table.find('thead tr');
            $thead_row.append('<th />');

            for (column_i = 0; column_i < columns; column_i++) {

                $thead_row.append($('<th>' + l10n['game'] + ' ' + (column_i + 1) + '</th>'));
            }

            // Create tbody
            for (row_i = 0; row_i < rows.length; row_i++) {

                player_id = typeof rows[row_i]['player_id'] != 'undefined' ? rows[row_i]['player_id'] : '';
                team_id = typeof rows[row_i]['team_id'] != 'undefined' ? rows[row_i]['team_id'] : '';

                row = '<tr data-player-id="' + player_id + '" data-team-id="' + team_id +
                    '"><td>' + rows[row_i]['label'] + '</td>'

                for (column_i = 0; column_i < columns; column_i++) {

                    row = row + '<td><input type="number" value="0" /></td>';
                }

                row = row + '</tr>'

                $tbody.append($(row));
            }

            // Create tfoot
            if (footer) {

                $tfoot.append($(
                    '<tr class="pingpong-tfoot-title"><td colspan="' + (columns + 1) + '">' + l10n['totals'] + ':</td></tr>'
                ));

                for (foot_i = 0; foot_i < footer.length; foot_i++) {

                    player_id = typeof footer[foot_i]['player_id'] != 'undefined' ? footer[foot_i]['player_id'] : '';
                    team_id = typeof footer[foot_i]['team_id'] != 'undefined' ? footer[foot_i]['team_id'] : '';

                    $tfoot.append($(
                        '<tr>' +
                        '<td>' + footer[foot_i]['label'] + '</td>' +
                        '<td data-player-id="' + player_id + '" data-team-id="' + team_id + '">0</td>' +
                        '<td colspan="' + (columns - 1) + '"></td>' +
                        '</tr>'
                    ));
                }
            }

            // Populate scores
            if (data.scores) {

                api.scores_modal_populate_scores(data.scores.matches);
            }
        },

        /**
         * Populates the table with previously set scores.
         *
         * @since {{VERSION}}
         *
         * @param matches
         */
        scores_modal_populate_scores: function (matches) {

            var i;

            if (matches.length !== api.$scores_modal.find('tbody tr').length / 2) {

                api.scores_modal_error(l10n['cant_load_scores']);
                return;
            }

            api.$scores_modal.find('tbody td:not(:first-of-type) input[type="number"]').each(function () {

                var $row = $(this).closest('tr'),
                    match = Math.floor($row.index() / 2),
                    game = $(this).closest('td').index() - 1,
                    player_index = $row.index() % 2 === 1 ? 1 : 0;

                $(this).val(matches[match][game][player_index]);
            });

            api.scores_update();
        },

        /**
         * Shows an error.
         *
         * @since {{VERSION}}
         *
         * @param message
         */
        scores_modal_error: function (message) {

            api.$scores_modal.find('.pingpong-scores-error').slideDown().find('p').html(message);
        },

        /**
         * Closes the scores modal.
         *
         * @since {{VERSION}}
         */
        scores_modal_close: function () {

            $('body').removeClass('pingpong-modal-open');

            api.$scores_modal.find('.pingpong-scores-error').hide();
            api.$scores_modal.find('thead, tbody, tfoot').html('');
            api.$scores_modal.hide();
        },

        /**
         * Updates all of the scores in the table.
         *
         * @since {{VERSION}}
         */
        scores_update: function () {

            var total_matches = api.$scores_modal.find('tbody tr').length / 2,
                total_games = api.$scores_modal.find('thead th').length - 1,
                match_i, game_i,
                players = {},
                teams = {},
                match = {
                    win: null,
                    scores: [],
                    player_1: {
                        id: 0,
                        team_id: 0,
                        games: 0,
                        $row: null,
                    },
                    player_2: {
                        id: 0,
                        team_id: 0,
                        games: 0,
                        $row: null,
                    },
                },
                game = {
                    player_1: {
                        score: 0,
                        $input: null,
                    },
                    player_2: {
                        score: 0,
                        $input: null,
                    },
                },
                matches = [];

            for (match_i = 0; match_i < total_matches; match_i++) {

                match.player_1.$row = api.$scores_modal.find('tbody tr:eq(' + (match_i * 2) + ')');
                match.player_2.$row = api.$scores_modal.find('tbody tr:eq(' + ((match_i * 2) + 1) + ')');

                match.player_1.id = match.player_1.$row.attr('data-player-id');
                match.player_2.id = match.player_2.$row.attr('data-player-id');

                match.player_1.team_id = match.player_1.$row.attr('data-team-id');
                match.player_2.team_id = match.player_2.$row.attr('data-team-id');

                if (typeof players[match.player_1.id] == 'undefined') {

                    players.length++;
                    players[match.player_1.id] = {
                        games: 0,
                        points: 0
                    };
                }

                if (typeof players[match.player_2.id] == 'undefined') {

                    players.length++;
                    players[match.player_2.id] = {
                        games: 0,
                        points: 0
                    };
                }

                if (typeof teams[match.player_1.team_id] == 'undefined') {

                    teams.length++;
                    teams[match.player_1.team_id] = 0;
                }

                if (typeof teams[match.player_2.team_id] == 'undefined') {

                    teams.length++;
                    teams[match.player_2.team_id] = 0;
                }

                match.player_1.games = 0;
                match.player_2.games = 0;
                match.win = null;
                match.scores = [];

                for (game_i = 0; game_i < total_games; game_i++) {

                    game.player_1.$input = match.player_1.$row.find('td:eq(' + (game_i + 1) + ') input[type="number"]');
                    game.player_2.$input = match.player_2.$row.find('td:eq(' + (game_i + 1) + ') input[type="number"]');

                    // Player wins match by winning a certain number of games
                    if (match.player_1.games >= Math.floor(total_games / 2) + 1) {

                        match.win = 1;
                        game.player_1.$input.prop('disabled', true);
                        game.player_2.$input.prop('disabled', true);

                    } else if (match.player_2.games >= Math.floor(total_games / 2) + 1) {

                        match.win = 2;
                        game.player_1.$input.prop('disabled', true);
                        game.player_2.$input.prop('disabled', true);

                    } else {

                        game.player_1.$input.prop('disabled', false);
                        game.player_2.$input.prop('disabled', false);
                    }

                    game.player_1.score = parseInt(game.player_1.$input.val());
                    game.player_2.score = parseInt(game.player_2.$input.val());

                    // Award a player a game
                    if (game.player_1.score >= 11 && (game.player_1.score - game.player_2.score >= 2)) {

                        match.player_1.games++;

                    } else if (game.player_2.score >= 11 && (game.player_2.score - game.player_1.score >= 2)) {

                        match.player_2.games++;
                    }

                    match.scores.push([
                        {
                            id: match.player_1.id,
                            score: game.player_1.score
                        },
                        {
                            id: match.player_2.id,
                            score: game.player_2.score
                        }
                    ]);

                    //players[match.player_1.id].points = players[match.player_1.id].points + game.player_1.score;
                    //players[match.player_2.id].points = players[match.player_2.id].points + game.player_2.score;
                }

                players[match.player_1.id].games = players[match.player_1.id].games + match.player_1.games;
                players[match.player_2.id].games = players[match.player_2.id].games + match.player_2.games;

                if (match.win) {

                    teams[match['player_' + match.win].team_id] = teams[match['player_' + match.win].team_id] + 1;
                }

                matches.push(match.scores);
            }

            // Update totals
            api.scores.players = players;
            api.scores.teams = teams;
            api.scores.matches = matches;

            console.log(api.scores);

            $.each(players, function (player_ID, player) {

                if (player_ID == 'length' || !player_ID) {

                    return true;
                }

                api.$scores_modal.find('tfoot [data-player-id="' + player_ID + '"]').html(player.games);
            });

            $.each(teams, function (team_ID, team_score) {

                if (team_ID == 'length' || !team_ID) {

                    return true;
                }

                api.$scores_modal.find('tfoot [data-team-id="' + team_ID + '"]').html(team_score);
            });
        },

        /**
         * Saves the scores.
         *
         * @since {{VERSION}}
         */
        scores_modal_submit: function () {

            if (!confirm(l10n['confirm_submit_match'])) {

                return;
            }

            $.post(
                ajaxurl,
                {
                    action: 'pingpong_submit_scores',
                    match_ID: data.match_ID,
                    scores: api.scores
                },
                function (response) {

                    if (typeof response.success == 'undefined' || !response.success) {

                        alert(l10n['could_not_save_match']);
                        return;
                    }

                    //window.location = response.data.redirect;
                }
            );
        }

        ///**
        // * Fires when selecting a player.
        // *
        // * Prevents duplicate player selects within player groups.
        // *
        // * @since {{VERSION}}
        // */
        //player_select: function () {
        //
        //    var player = $(this).val(),
        //        $player_select_active = $(this).closest('.player-select'),
        //        previous_player = $player_select_active.attr('data-active'),
        //        $player_select_group = $(this).closest('.player-select-group'),
        //        $player_selects = $player_select_group.find('.player-select').not($player_select_active);
        //
        //    if (previous_player) {
        //
        //        $player_selects.find('option[value="' + previous_player + '"]').prop('disabled', false);
        //    }
        //
        //    if (player) {
        //
        //        $player_select_active.attr('data-active', player);
        //        $player_selects.find('option[value="' + player + '"]').prop('disabled', true);
        //
        //    } else {
        //
        //        $player_select_active.removeAttr('data-active');
        //    }
        //}
    }

    $(api.init);
})(jQuery)