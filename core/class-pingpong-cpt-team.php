<?php
/**
 * Creates the Team custom post type.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_CPT_Team
 *
 * Creates the Team custom post type.
 *
 * @since {{VERSION}}
 */
class PingPong_CPT_Team extends RBM_CPT {

	public $post_type = 'team';
	public $icon = 'groups';
	public $post_args;
	public $supports = array( 'title', 'editor', 'thumbnail' );

	private $user_options = array();

	/**
	 * PingPong_CPT_Team constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->label_singular = __( 'Team', 'pingpong' );
		$this->label_plural   = __( 'Teams', 'pingpong' );

		parent::__construct();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds the custom post type meta boxes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_meta_boxes() {

		$users = get_users( array(
			'role' => 'player',
		) );

		if ( $users && ! is_wp_error( $users ) ) {

			foreach ( $users as $user ) {

				$this->user_options[ $user->ID ] = $user->data->display_name;
			}
		}

		add_meta_box(
			'team-players',
			__( 'Team Players', 'pingpong' ),
			array( $this, 'mb_team_players' ),
			$this->post_type
		);

		add_meta_box(
			'team-scores',
			__( 'Team Scores', 'pingpong' ),
			array( $this, 'mb_team_scores' ),
			$this->post_type
		);
	}

	/**
	 * Metabox for team players.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_team_players() {

		rbm_do_field_select( 'players', false, false, array(
			'options'  => $this->user_options,
			'multiple' => true,
			'input_class'   => 'rbm-select2',
			'input_atts'  => array(
				'data-maximum-selection-length' => 2,
			),
		) );
	}

	/**
	 * Metabox for team scores.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function mb_team_scores() {

		$team_scores = get_post_meta( get_the_ID(), 'pingpong_scores', true );

		$games = array();
		foreach ( $team_scores as $match_ID => $game_score ) {

			if ( ! ( $match_title = get_the_title( $match_ID ) ) ) {

				continue;
			}

			$games[ $match_title ] = $game_score;
		}

		$games_total = array_sum( $games );
		?>

		<span class="pingpong-won-games-total">
			<?php echo __( 'Games won:', 'pingpong' ) . " $games_total"; ?>
		</span>

		<a href="#" data-expand-games>
			<?php _e( 'View Matches', 'pingpong' ); ?>
		</a>

		<table id="pingpong-won-games" style="display: none;">
			<thead>
			<tr>
				<th class="pingpong-won-games-title">
					<?php _e( 'Match Name', 'pingpong' ); ?>
				</th>

				<th class="pingpong-won-games-score">
					<?php _e( 'Score', 'pingpong' ); ?>
				</th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ( $games as $game_title => $game_score ) : ?>
				<tr>
					<td class="pingpong-won-games-title">
						<?php echo $game_title; ?>
					</td>

					<td class="pingpong-won-games-score">
						<?php echo $game_score; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}