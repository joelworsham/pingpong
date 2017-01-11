<?php
/**
 * The list table for the Gradebook.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class PingPong_Rankings_Table
 *
 * The list table for the Gradebook.
 *
 * @since {{VERSION}}
 */
class PingPong_Rankings_Table extends WP_List_Table {

	/**
	 * Number of items to show per page.
	 *
	 * @since {{VERSION}}
	 *
	 * @var int
	 */
	public $per_page = 30;

	/**
	 * PingPong_Rankings_Table constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		parent::__construct( array(
			'singular' => __( 'Player', 'pingpong' ),
			'plural'   => __( 'Players', 'pingpong' ),
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since {{VERSION}}
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'player' => __( 'Player', 'pingpong' ),
			'games'  => __( 'Games Won', 'pingpong' ),
		);

		return $columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since {{VERSION}}
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {

		return 'player';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since {{VERSION}}
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {

		return $item[ $column_name ];
	}

	/**
	 *
	 * Get a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @since {{VERSION}}
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {

		$classes = parent::get_table_classes();

		$classes[] = 'pingpong-player-rankings';

		return $classes;
	}

	/**
	 * Retrieve the current page number
	 *
	 * @since {{VERSION}}
	 *
	 * @return int Current page number
	 */
	public function get_paged() {

		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @since {{VERSION}}
	 *
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_search() {

		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Performs the query to get the data, in this case users.
	 *
	 * @since {{VERSION}}
	 */
	public function query() {

		$user_args = array(
			'number'   => $this->per_page,
			'offset'   => $this->per_page * ( $this->get_paged() - 1 ),
			'order'    => isset( $_GET['order'] ) ? $_GET['order'] : 'dsc',
			'orderby'  => 'meta_value',
			'meta_key' => 'pingpong_games',
		);

		if ( $search = $this->get_search() ) {

			$user_args['search'] = $search;
		}

		$users = get_users( $user_args );

		$data = array();

		foreach ( $users as $user ) {

			$data[] = array(
				'player' => $user->display_name,
				'games'  => get_user_meta( $user->ID, 'pingpong_games', true ),
			);
		}

		return $data;
	}

	/**
	 * Retrieve count of total users with keys
	 *
	 * @since {{VERSION}}
	 */
	public function total_items() {

		if ( ! ( $users = get_users() ) ) {

			return false;
		}

		return count( $users );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since {{VERSION}}
	 */
	public function no_items() {

		_e( 'No players to show.', 'pingpong' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since {{VERSION}}
	 */
	public function prepare_items() {

		// Get and set columns
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable, 'user' );

		$data = $this->query();

		if ( ! ( $total_items = $this->total_items() ) ) {

			$total_items = count( $data );
		}

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			)
		);
	}
}