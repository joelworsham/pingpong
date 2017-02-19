<?php
/**
 * Match shortcode template: intro.
 *
 * @since {{VERSION}}
 *
 * @var int $game
 * @var array $settings
 */

defined( 'ABSPATH' ) || die();
?>

<input type="hidden" name="page" value="<?php echo $game < (int) $settings['total_games'] ? 'play' : 'end'; ?>"/>
<input type="hidden" name="game" value="<?php echo $game + 1; ?>"/>

<h2>Game <?php echo $game; ?></h2>

<h3>Scores</h3>

<div class="match-player-score">
	<div class="match-player-score-name">
		<?php echo esc_attr( $settings['player1']['name'] ); ?>
	</div>

	<div class="match-player-score-value">
		<select name="match_player_1_score">
			<?php for ( $i = 0; $i <= 30; $i ++ ) : ?>
				<option value="<?php echo $i; ?>">
					<?php echo $i; ?>
				</option>
			<?php endfor; ?>
		</select>
	</div>
</div>

<div class="match-player-score">
	<div class="match-player-score-name">
		<?php echo esc_attr( $settings['player2']['name'] ); ?>
	</div>

	<div class="match-player-score-value">
		<select name="match_player_2_score">
			<?php for ( $i = 0; $i <= 30; $i ++ ) : ?>
				<option value="<?php echo $i; ?>">
					<?php echo $i; ?>
				</option>
			<?php endfor; ?>
		</select>
	</div>
</div>

<button type="submit" class="button">
	<?php echo $game === (int) $settings['total_games'] ? 'End Match' : 'Submit Game'; ?>
</button>