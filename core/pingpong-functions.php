<?php
/**
 * Helper functions.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Returns the main plugin class.
 *
 * @since {{VERSION}}
 *
 * @return PingPong
 */
function PONG() {

	return PingPong::getInstance();
}