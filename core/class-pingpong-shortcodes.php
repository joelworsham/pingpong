<?php
/**
 * Manages all plugin shortcodes.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Shortcodes
 *
 * Contains the grade for a given user.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core
 */
class PingPong_Shortcodes {

	/**
	 * All plugin shortcodes.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array
	 */
	public $shortcodes = array();

	/**
	 * PingPong_Shortcodes constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_filter( 'pingpong_shortcodes', array( $this, 'included_shortcodes' ) );
		add_action( 'init', array( $this, 'init_shortcodes' ) );
	}

	/**
	 * Adds included shortcodes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function included_shortcodes( $shortcodes ) {

		require_once PINGPONG_DIR . 'core/shortcodes/class-pingpong-sc-match.php';

		$shortcodes['pingpong_match'] = new PingPong_SC_Match();

		return $shortcodes;
	}

	/**
	 * Initializes all plugin shortcodes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function init_shortcodes() {

		/**
		 * Shortcodes for LearnDash Gradebook.
		 *
		 * @since {{VERSION}}
		 */
		$shortcodes = apply_filters( 'pingpong_shortcodes', array() );

		foreach ( $shortcodes as $id => $shortcode ) {

				$this->shortcodes[ $id ] = $shortcode;
		}
	}
}