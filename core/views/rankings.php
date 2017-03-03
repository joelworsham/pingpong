<?php
/**
 * Rankings template.
 *
 * @since {{VERSION}}
 *
 * @var array $rankings
 * @var string $base_link
 * @var string $order
 * @var string $orderby
 */

defined( 'ABSPATH' ) || die();
?>

<h1>
	Rankings
</h1>

<table id="pingpong-player-rankings">
	<thead>
	<tr>
		<th>
			Player
		</th>
		<th>
			<a href="<?php
			echo add_query_arg( array(
				'orderby' => 'plusminus',
				'order'   => $orderby == 'plusminus' && $order == 'DESC' ? 'ASC' : 'DESC',
			), $base_link );
			?>">
				+/-
			</a>
		</th>
		<th>
			<a href="<?php
			echo add_query_arg( array(
				'orderby' => 'games_won',
				'order'   => $orderby == 'games_won' && $order == 'DESC' ? 'ASC' : 'DESC',
			), $base_link );
			?>">
				Games Won
			</a>
		</th>
		<th>
			<a href="<?php
			echo add_query_arg( array(
				'orderby' => 'matches_won',
				'order'   => $orderby == 'matches_won' && $order == 'DESC' ? 'ASC' : 'DESC',
			), $base_link );
			?>">
				Matches Won
			</a>
		</th>
	</tr>
	</thead>

	<tbody>
	<?php foreach ( $rankings as $player ) : ?>
		<?php $user = new WP_User( $player['player_id'] ); ?>
		<tr>
			<td>
				<?php echo $user->display_name; ?>
			</td>
			<td>
				<?php echo $player['plusminus']; ?>
			</td>
			<td>
				<?php echo $player['games_won']; ?>
			</td>
			<td>
				<?php echo $player['matches_won']; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>