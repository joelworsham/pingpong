<?php
/**
 * Shortcode class framework
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Shortcode
 *
 * Contains the grade for a given user.
 *
 * @since {{VERSION}}
 *
 * @package PingPong
 * @subpackage PingPong/core
 */
abstract class PingPong_Shortcode {

	/**
	 * The shortcode ID (tag).
	 *
	 * @since {{VERSION}}
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The shortcode attributes.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array|bool
	 */
	public $atts;

	/**
	 * PingPong_Shortcode constructor.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $id
	 * @param array|bool $atts
	 */
	function __construct( $id, $atts = false ) {

		$this->id = $id;
		$this->atts = $atts;

		add_shortcode( $this->id, array( $this, 'shortcode' ) );
	}

	/**
	 * Sets up the default attributes.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public function default_atts( $atts) {

		if ( $this->atts ) {
			$this->atts = shortcode_atts( $this->atts, $atts, $this->id );
		}

		return $this->atts;
	}

	/**
	 * Outputs the shortcode.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return mixed The shortcode output.
	 */
	public function shortcode( $atts = array(), $content = '' ) {}
}