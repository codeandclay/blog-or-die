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

		add_settings_section(
			'blog_or_die_settings_section', // ID
			'', // Title
			'', // Callback
			'blog_or_die_settings' // Page
		);

		add_settings_field(
			'cc_timeframe', // ID
			'Interval between posts', // Title
			array( __CLASS__, 'timeframe_view' ), // Callback
			'blog_or_die_settings', // Page
			'blog_or_die_settings_section' // Section
		);

		add_settings_field(
			'cc_death_notice', // ID
			'Death notice', // Title
			array( __CLASS__, 'death_notice_view' ), // Callback
			'blog_or_die_settings', // Page
			'blog_or_die_settings_section' // Section
		);

		?>
		<div class="wrap">
			<h1>Blog or Die Settings</h1>

			<form method="POST" action="options.php">
				<?php
					settings_fields( 'cc_blog_or_die' );
					do_settings_sections( 'blog_or_die_settings' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public static function timeframe_view() {
		echo( 'I need to ensure I leave no more than ' );
		echo( '<select id="duration" name="cc_timeframe">' );
		foreach ( self::timeframes() as $key => $value ) {
			$selected_attr = '';
			if ( $value == get_option( 'cc_timeframe' ) ) {
				$selected_attr = 'selected';
			}
			echo( '<option value="' . $value . '" ' . $selected_attr . '>' . $key . '</option>' );
		}
		echo( '</select>' );
		echo( ' between published posts or my blog gets it.' );
	}

	public static function death_notice_view() {
		ob_start();
		$text = 'I have failed to meet my own expectations and I should be ashamed of myself.';
		if ( ! empty( get_option( 'cc_death_notice' ) ) ) {
			$text = get_option( 'cc_death_notice' );
		}
		?>
		<textarea type="text" rows="5" cols="50" name="cc_death_notice" class="large-text"><?php echo( $text ); ?></textarea>
		<p class="description">This is the text that will be displayed when your blog is dead.</p>
		<?php
		echo( ob_get_clean() );
	}
}

CCBlogOrDie::run();
