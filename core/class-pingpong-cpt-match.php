<?php
/**
 * Creates the Match custom post type.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_CPT_Match
 *
 * Creates the Match custom post type.
 *
 * @since {{VERSION}}
 */
class PingPong_CPT_Match extends RBM_CPT {

	public $post_type = 'match';
	public $icon = 'flag';
	public $post_args;
	public $supports = array( 'title', 'editor', 'thumbnail' );

	/**
	 * PingPong_CPT_Match constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->label_singular = __( 'Match', 'pingpong' );
		$this->label_plural   = __( 'Matches', 'pingpong' );

		parent::__construct();

		add_action( 'init', array( $this, 'add_post_statuses' ) );
		add_action( 'admin_footer-post.php', array( $this, 'add_post_statuses_to_dropdown' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_filter( 'the_content', array( $this, 'frontend_output' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'add_publish_sections' ) );
		add_action( 'admin_footer', array( $this, 'scores_modal' ) );
	}

	/**
	 * Adds custom post statuses.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_post_statuses() {

		global $wp_post_statuses;

		register_post_status( 'match_complete', array(
			'label'                     => __( 'Completed', 'pingpong' ),
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'pingpong' ),
			'exclude_from_search'       => get_post_type_object( 'match' )->exclude_from_search,
			'public'                    => get_post_type_object( 'match' )->public,
			'publicly_queryable'        => get_post_type_object( 'match' )->publicly_queryable,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
		) );
	}

	/**
	 * Add custom statuses to dropdown (messy, but all that's available now).
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_post_statuses_to_dropdown() {

		if ( get_post_type() != 'match' ) {

			return;
		}

		$custom_statuses = array(
			'match_complete' => __( 'Completed', 'pingpong' ),
		);
		?>
		<script type="text/javascript">
			(function ($) {

				$(function () {

					var $status_select = $('#post_status'),
						$status_display = $('#post-status-display');

					<?php foreach ( $custom_statuses as $status => $status_label ) : ?>
					$status_select.append('<option value="<?php echo $status; ?>"><?php echo $status_label; ?></option>');

					<?php if ( $status == get_post_status() ) : ?>
					$status_select.find('option[value="<?php echo $status; ?>"]').prop('selected', true);
					$status_display.html('<?php echo $status_label; ?>');
					<?php endif; ?>
					<?php endforeach; ?>
				});
			})(jQuery);
		</script>
		<?php

	}

	/**
	 * Adds the custom post type meta boxes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_meta_boxes() {

		add_meta_box(
			'match-game-settings',
			__( 'Game Settings', 'pingpong' ),
			array( $this, 'mb_game_settings' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-type',
			__( 'Match Type', 'pingpong' ),
			array( $this, 'mb_match_type' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-league',
			__( 'Match League', 'pingpong' ),
			array( $this, 'mb_match_league' ),
			$this->post_type,
			'side'
		);

		add_meta_box(
			'match-settings-singles',
			__( 'Match Settings Singles', 'pingpong' ),
			array( $this, 'mb_match_settings_singles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-doubles',
			__( 'Match Settings Doubles', 'pingpong' ),
			array( $this, 'mb_match_settings_doubles' ),
			$this->post_type
		);

		add_meta_box(
			'match-settings-team',
			__( 'Match Settings Team', 'pingpong' ),
			array( $this, 'mb_match_settings_team' ),
			$this->post_type
		);
	}

	/**
	 * Outputs the metabox for Game Settings
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_game_settings() {

		rbm_do_field_number( 'games', __( 'Number of Games', 'pingpong' ), false, array(
			'default' => 1,
			'min'     => 0,
		) );
	}

	/**
	 * Outputs the metabox for Match Type
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_type() {

		rbm_do_field_radio( 'type', false, false, array(
			'default' => 'singles',
			'options' => array(
				'singles' => __( 'Singles Match', 'pingpong' ),
				'team'    => __( 'Team Match', 'pingpong' ),
				'doubles' => __( 'Doubles Match', 'pingpong' ),
			),
		) );
	}

	/**
	 * Outputs the metabox for the Match League.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_league() {

		$leagues = get_posts( array(
			'post_type'   => 'league',
			'numberposts' => - 1,
		) );

		$league_options = array(
			'' => __( '- No League -', 'pingpong' ),
		);

		if ( $leagues && ! is_wp_error( $leagues ) ) {

			$league_options = array_merge( $league_options, wp_list_pluck( $leagues, 'post_title', 'ID' ) );
		}

		rbm_do_field_select( 'league', false, false, array(
			'options'     => $league_options,
			'input_class' => 'rbm-select2',
		) );
	}

	/**
	 * Singles match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_singles() {

		$users = get_users( array(
			'role' => 'player',
		) );

		$user_options = array();

		if ( $users && ! is_wp_error( $users ) ) {

			foreach ( $users as $user ) {

				$user_options[ $user->ID ] = $user->data->display_name;
			}
		}

		rbm_do_field_select( 'singles_players', __( 'Players', 'pingpong' ), false, array(
			'options'       => $user_options,
			'wrapper_class' => 'player-select',
			'multiple'      => true,
			'input_class'   => 'rbm-select2',
		) );
	}

	/**
	 * Two person team match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_team() {

		$teams = get_posts( array(
			'post_type'   => 'team',
			'numberposts' => - 1,
		) );

		$team_options = array();

		if ( $teams && ! is_wp_error( $teams ) ) {

			$team_options = wp_list_pluck( $teams, 'post_title', 'ID' );
		}

		rbm_do_field_select( 'teams', __( 'Teams', 'pingpong' ), false, array(
			'options'     => $team_options,
			'multiple'    => true,
			'input_class' => 'rbm-select2',
		) );
	}

	/**
	 * Doubles match settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_match_settings_doubles() {

		echo 'Doubles';
	}

	/**
	 * Outputs any misc publishing sections.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_publish_sections() {

		// Add scores
		?>
		<div class="misc-pub-section pingpong-add-scores">
			<button type="button" class="button">
				<?php _e( 'Add Scores', 'pingpong' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Outputs the scores modal.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function scores_modal() {

		?>
		<div id="pingpong-scores-backdrop"></div>
		<div id="pingpong-scores">
			<div class="pingpong-scores-container">
				<h2 class="pingpong-scores-title">
					<?php _e( 'Match Scores', 'pingpong' ); ?>
				</h2>
			</div>

			<div class="pingpong-scores-actions">
				<button type="button" class="pingpong-scores-submit button button-primary button-large" data-scores-submit>
					<?php _e( 'Submit Scores and End Match', 'pingpong' ); ?>
				</button>

				<a href="#" class="pingpong-scores-close" data-scores-close>
					<span class="dashicons dashicons-no"></span>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output frontend display.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param string $content
	 */
	function frontend_output( $content ) {

		if ( ! is_main_query() ) {

			return $content;
		}


		return $content;
	}
}