<?php
/**
 * Creates the League custom post type.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_CPT_League
 *
 * Creates the League custom post type.
 *
 * @since {{VERSION}}
 */
class PingPong_CPT_League extends RBM_CPT {

	public $post_type = 'league';
	public $icon = 'calendar-alt';
	public $post_args;
	public $supports = array( 'title', 'editor', 'thumbnail' );

	/**
	 * PingPong_CPT_League constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->label_singular = __( 'League', 'pingpong' );
		$this->label_plural   = __( 'Leagues', 'pingpong' );

		parent::__construct();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}
}