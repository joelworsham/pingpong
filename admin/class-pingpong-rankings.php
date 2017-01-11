<?php
/**
 * Creates the rankings.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Rankings
 *
 * Creates the rankings.
 *
 * @since {{VERSION}}
 */
class PingPong_Rankings {

	/**
	 * PingPong_Rankings constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu'));
	}

	/**
	 * Adds menu items.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function admin_menu() {

		add_users_page(
			__( 'Player Rankings', 'pingpong' ),
			__( 'Player Rankings', 'pingpong' ),
			'list_users',
			'pingpong-rankings',
			array( $this, 'rankings_page' )
		);
	}

	/**
	 * Outputs the rankings page.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function rankings_page() {

		require_once PINGPONG_DIR . 'admin/includes/class-pingpong-rankings-table.php';

		$rankings = new PingPong_Rankings_Table();

		$rankings->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Player Rankings', 'pingpong' ); ?>
			</h1>

			<?php $rankings->display(); ?>
		</div>
		<?php
	}
}