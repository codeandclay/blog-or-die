<?php
/**
 * @package Blog or Die
 * @version 1.0
 */
/*
Plugin Name: Blog or Die
Plugin URI: www.codeandclay.com
Description: Publish or your blog gets it.
Author: Oliver Denman
Version: 1.0
Author URI: www.codeandclay.com
*/

class CCBlogOrDie {
	public static function run() {
		add_action( 'template_redirect', array( __CLASS__, 'prevent_page_load' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function prevent_page_load() {
		if ( self::is_last_post_older_than( self::time_limit_in_seconds() ) ) {
			self::death_notice();
		}
	}

	private static function is_last_post_older_than( $seconds ) {
		return current_time( 'timestamp' ) - self::time_of_latest_post_in_seconds() > $seconds;
	}

	private static function time_limit_in_seconds() {
		return 10;
	}

	private static function time_of_latest_post_in_seconds() {
		return strtotime(
			get_posts(
				array(
					'orderby'        => 'date',
					'order'          => 'DESC',
					'posts_per_page' => 1,
				)
			)[0]->post_date
		);
	}

	private static function death_notice() {
		die( '<h1>Your blog is dead</h1>' );
	}

	/*
	Back end
	*/

	public static function add_menu() {
		add_options_page( 'Blog or Die Settings', 'Blog or Die', 'edit_pages', 'blog_or_die_settings', array( __CLASS__, 'render_settings_page' ), false, 62 );
	}

	public static function register_settings() {
		register_setting( 'cc_blog_or_die', 'cc_timeframe' );
		register_setting( 'cc_blog_or_die', 'cc_death_notice' );
	}

	public static function timeframes() {
		return array(
			'a day'        => 'day_1',
			'two days'     => 'day_2',
			'three days'   => 'day_3',
			'four days'    => 'day_4',
			'five days'    => 'day_5',
			'six days'     => 'day_6',
			'a week'       => 'week_1',
			'two weeks'    => 'week_2',
			'three weeks'  => 'week_3',
			'month'        => 'month_1',
			'two months'   => 'month_2',
			'three months' => 'month_3',
			'six months'   => 'month_6',
			'a year'       => 'month_12',
		);
	}

	private static function time_args( $str ) {
		if ( ! self::validate_method( $str ) ) {
			trigger_error( 'String cannot be used to call method.' );
			return false;
		}
		return explode( $str );
	}

	public static function render_settings_page() {
		?>
		<div class="wrap">
			<h1>Blog or Die Settings</h1>

			<form action="" action="options.php">
				<?php settings_fields( 'cc_blog_or_die' ); ?>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet cumque, iusto animi consequatur. Consequatur quaerat, quod magnam. Inventore qui voluptas maiores explicabo nesciunt, dolor ipsum, eos eum veniam labore distinctio recusandae sed ex obcaecati perspiciatis nemo cumque in quam. Eligendi.</p>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="interval">Interval between posts</label>
						</th>
						<td>
							I need to ensure I leave no more than
							<select id="duration">
							</select>
							between published posts or my blog gets it.
						</td>
					</tr>
					<tr>
						<th scope="row">
							Text to display when dead
						</th>
						<td>
							<textarea type="text" rows="5" cols="50" class="large-text">I have failed to meet my own expectations and I should be ashamed of myself.</textarea>
							<p class="description">This is the text that will be displayed when your blog is dead.</p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>
		</div>
		<script>
			jQuery(function(){
				var timeframes = <?php echo( json_encode( self::timeframes() ) ); ?>;
				for(var key in timeframes) {
					jQuery("#duration").append(jQuery('<option></option>')
									   .val(timeframes[key])
									   .html(key))
				};
			})
		</script>
		<?php
	}
}

CCBlogOrDie::run();
