<?php
/**
 * Match shortcode template: end.
 *
 * @since {{VERSION}}
 *
 * @var array $settings
 * @var array $games
 * @var array $scores
 * @var string $winner
 */

defined( 'ABSPATH' ) || die();
?>

<input type="hidden" name="page" value="intro"/>

<strong>
	<?php echo $settings[ $winner ]['name']; ?> wins!
</strong>

<table class="match-final-scores">
	<thead>
	<tr>
		<th></th>

		<th>
			<?php echo $settings['player1']['name']; ?>
		</th>

		<th>
			<?php echo $settings['player2']['name']; ?>
		</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<th>
			Games Won
		</th>

		<td>
			<?php echo $scores['player1']['games']; ?>
		</td>

		<td>
			<?php echo $scores['player2']['games']; ?>
		</td>
	</tr>

	<tr>
		<th>
			Points Won
		</th>

		<td>
			<?php echo $scores['player1']['points']; ?>
		</td>

		<td>
			<?php echo $scores['player2']['points']; ?>
		</td>
	</tr>
	</tbody>
</table>

<table class="match-final-games">
	<thead>
	<tr>
		<th>
			Game
		</th>

		<th>
			<?php echo $settings['player1']['name']; ?>
		</th>

		<th>
			<?php echo $settings['player2']['name']; ?>
		</th>
	</tr>
	</thead>

	<tbody>
	<?php foreach ( $games as $i => $game ) : ?>
		<tr>
			<td>
				<?php echo $i + 1; ?>
			</td>

			<td>
				<?php echo $game['player1']; ?>
			</td>

			<td>
				<?php echo $game['player2']; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<button type="submit" class="button">
	Finish
</button>