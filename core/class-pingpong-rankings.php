<?php
/**
 * Creates the rankings pages.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Rankings
 *
 * Creates the rankings pages.
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

		add_filter( 'the_content', array( $this, 'filter_content' ) );
	}

	function filter_content( $content ) {

		global $wp_query;

		if ( get_the_ID() !== pingpong_get_page( 'rankings' ) ) {

			return $content;
		}

		$per_page = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : 10;
		$page     = isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 0 ?
			(int) $wp_query->query_vars['page'] : 1;

		$base_link = get_permalink() . '?' . http_build_query( $_GET );
		$order     = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby   = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'plusminus';

		$rankings = PingPongDB()->get_all_rankings( array(
			'per_page' => $per_page,
			'offset'   => ( $page - 1 ) * $per_page,
			'order'    => $order,
			'orderby'  => $orderby,
		) );

		ob_start();

		include_once PINGPONG_DIR . 'core/views/rankings.php';

		return ob_get_clean();
	}
}