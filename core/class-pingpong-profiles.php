<?php
/**
 * Creates the profile pages.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Profiles
 *
 * Creates the profile pages.
 *
 * @since {{VERSION}}
 */
class PingPong_Profiles {

	/**
	 * PingPong_Profiles constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_filter( 'the_content', array( $this, 'filter_content' ) );
	}

	function rewrite_rules() {

		$page_ID = pingpong_get_page( 'profile' );

		if ( ! $page_ID || ! ( $page = get_post( $page_ID ) ) ) {

			return;
		}

		add_rewrite_rule( "^{$page->post_name}/([^/]*)/?", "index.php?page_id={$page_ID}&profile=\$matches[1]", 'top' );

		add_rewrite_tag( '%profile%', '([^&]+)' );
	}

	function filter_content( $content ) {

		global $wp_query;

		if ( get_the_ID() !== pingpong_get_page( 'profile' ) ||
		     ( ! $wp_query->query_vars['profile'] && ! is_main_query() )
		) {

			return $content;
		}

		if ( ! $wp_query->query_vars['profile'] ) {

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

			} else {

				return 'You must be logged in to view this page.';
			}

		} else {

			$user = get_user_by( 'login', $wp_query->query_vars['profile'] );
		}

		$rankings = PingPongDB()->get_rankings( $user->ID );

		ob_start();

		include_once PINGPONG_DIR . 'core/views/profile.php';

		return ob_get_clean();
	}
}