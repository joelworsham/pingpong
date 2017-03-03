<?php
/**
 * Settings page output.
 *
 * @since {{VERSION}}
 *
 * @var array $pages
 */

defined( 'ABSPATH' ) || die();
?>

<div class="wrap">

	<h1 class="page-title">
		<?php _e( 'Ping Pong Settings', 'pingpong' ); ?>
	</h1>

	<form method="post" action="options.php">

		<?php settings_fields( 'pingpong' ); ?>

		<h2>
			<?php _e( 'Pages', 'pingpong' ); ?>
		</h2>

		<table class="form-table">
			<tbody>
			<?php foreach ( $pages as $page => $page_ID ) : ?>

				<tr valign="top">
					<th scope="row">
						<?php echo pingpong_get_page_title( $page ); ?>
					</th>

					<td>
						<?php
						wp_dropdown_pages( array(
							'numberposts'      => - 1,
							'show_option_none' => __( '- Select a Page -', 'pingpong' ),
							'selected'         => $page_ID,
							'name'             => "pingpong_pages[$page]",
						) );
						?>

						<?php if ( ! $page_ID ) : ?>

							<a href="<?php echo admin_url( "options-general.php?page=pingpong&create_page=$page" ); ?>"
							   class="button">
								<?php _e( 'Create Page', 'pingpong' ); ?>
							</a>

						<?php else : ?>

							<a href="<?php echo get_permalink( $page_ID ); ?>"
							   class="button">
								<?php _e( 'View Page', 'pingpong' ); ?>
							</a>

						<?php endif; ?>
					</td>
				</tr>

			<?php endforeach; ?>
			</tbody>
		</table>

		<?php submit_button(); ?>

	</form>

</div>