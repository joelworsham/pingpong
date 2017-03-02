<?php
/**
 * Profile template.
 *
 * @since {{VERSION}}
 *
 * @var array $rankings
 * @var WP_User $user
 */

defined( 'ABSPATH' ) || die();
?>

<h1>
	<?php echo $user->display_name; ?>
</h1>

<div class="pingpong-player-plusminus">
	<div class="pingpong-player-plusminus-label">+/1</div>
	<div class="pintpong-player-plusminus-score">
		<?php echo $rankings['plusminus']; ?>
	</div>
</div>

<table id="pingpong-player-rankings">
	<tbody>
	<tr>
		<th>
			Points Won
		</th>
		<td>
			<?php echo $rankings['points_won']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Points Lost
		</th>
		<td>
			<?php echo $rankings['points_lost']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Games Won
		</th>
		<td>
			<?php echo $rankings['games_won']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Games Lost
		</th>
		<td>
			<?php echo $rankings['games_lost']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Matches Won
		</th>
		<td>
			<?php echo $rankings['matches_won']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Matches Lost
		</th>
		<td>
			<?php echo $rankings['matches_lost']; ?>
		</td>
	</tr>
	<tr>
		<th>
			Matches Played
		</th>
		<td>
			<?php echO $rankings['matches_played']; ?>
		</td>
	</tr>
	</tbody>
</table>