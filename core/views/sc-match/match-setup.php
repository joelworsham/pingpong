<?php
/**
 * Match shortcode template: intro.
 *
 * @since {{VERSION}}
 *
 * @var array $player_options
 * @var array $team_options
 */

defined( 'ABSPATH' ) || die();
?>

<input type="hidden" name="page" value="play"/>
<input type="hidden" name="game" value="1"/>

<div class="match-setup-field">
	<label for="match_total_games">Number of Games</label>
	<select id="match_total_games" name="match_total_games">
		<option value="3">3</option>
		<option value="5">5</option>
		<option value="7">7</option>
	</select>
</div>

<div class="match-setup-field">
	<label for="match_points">Points</label>
	<select id="match_points" name="match_points">
		<option value="11">11</option>
		<option value="21">21</option>
	</select>
</div>

<div id="match-setup-field-opponent_player" class="match-setup-field">
	<label for="match_opponent_player">Opponent</label>
	<select id="match_opponent_player" name="match_opponent_player" class="pingpong-select2">
		<?php foreach ( $player_options as $value => $text ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>">
				<?php echo esc_attr( $text ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>

<div id="match-setup-field-opponent_team" class="match-setup-field" style="display: none;">
	<label for="match_opponent_team">Opposing Team</label>
	<select id="match_opponent_team" name="match_opponent_team" class="pingpong-select2">
		<?php foreach ( $team_options as $value => $text ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>">
				<?php echo esc_attr( $text ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>

<button type="submit" class="button">
	Start Match
</button>