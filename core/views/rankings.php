<?php
/**
 * Rankings template.
 *
 * @since {{VERSION}}
 *
 * @var array $rankings
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
			+/-
		</th>
		<th>
			Games Won
		</th>
		<th>
			Matches Won
		</th>
	</tr>
	</thead>

	<tbody>
	<?php foreach ( $rankings as $player ) : ?>
		<?php $user = new WP_User( $player['player_id']); ?>
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