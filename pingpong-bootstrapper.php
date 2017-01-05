<?php
/**
 * Bootstraps the plugin load.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PingPong_Bootstrapper
 *
 * Bootstraps the plugin load.
 *
 * @since {{VERSION}}
 */
class PingPong_Bootstrapper {

	/**
	 * Message to show on load failure.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @var string
	 */
	private $load_failure_message = '';

	/**
	 * PingPong_Bootstrapper constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		if ( version_compare( phpversion(), '5.6' ) === - 1 ) {

			$this->load_failure_message = sprintf(
				__( 'Could not load Ping Pong CPT because your PHP version must be 5.6.0 or higher. Your current version is %s. Please contact your server administrator to upgrade your PHP version.', 'pingpong-cpt' ),
				phpversion()
			);

			add_action( 'admin_notices', array( $this, 'load_failure_notice' ) );

			return;
		}

		if ( version_compare( get_bloginfo( 'version' ), '4.7' ) === - 1 ) {

			$this->load_failure_message = sprintf(
				__( 'Could not load Ping Pong CPT because your WordPress version must be 4.7.0 or higher. Your current version is %s. Please contact your server administrator to upgrade your WordPress version.', 'pingpong-cpt' ),
				get_bloginfo( 'version' )
			);

			add_action( 'admin_notices', array( $this, 'load_failure_notice' ) );

			return;
		}

		if ( ! class_exists( 'RBM_FieldHelpers' ) ) {

			$this->load_failure_message = __( 'Could not load Ping Pong CPT because RBM Field Helpers must be installed and active as a must use plugin on this site. Please contact your system administrator to make sure it is.', 'pingpong-cpt' );

			add_action( 'admin_notices', array( $this, 'load_failure_notice' ) );

			return;
		}

		if ( ! class_exists( 'RBM_CPTS' ) ) {

			$this->load_failure_message = __( 'Could not load Ping Pong CPT because RBM CPTs must be installed and active as a must use plugin on this site. Please contact your system administrator to make sure it is.', 'pingpong-cpt' );

			add_action( 'admin_notices', array( $this, 'load_failure_notice' ) );

			return;
		}

		add_action( 'plugins_loaded', array( $this, 'load' ) );
	}

	/**
	 * Loads the plugin.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function load() {

		PONG();
	}

	/**
	 * Shows a notice for a failure to load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function load_failure_notice() {
		?>
		<div class="notice error">
			<p>
				<?php echo $this->load_failure_message; ?>
			</p>
		</div>
		<?php
	}
}