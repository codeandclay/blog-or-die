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

require_once plugin_dir_path( __FILE__ ) . '/class-blogordiefuzzytimeago.php';

class CCBlogOrDie {
	public static function run() {
		add_action( 'template_redirect', array( __CLASS__, 'prevent_page_load' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_notices', array( __CLASS__, 'display_time_info' ) );
	}

	public static function prevent_page_load() {
		if ( ! get_posts() ) {
			return;
		}
		if ( self::was_last_post_published_after_deadline() ) {
			self::death_notice();
		}
	}

	private static function was_last_post_published_after_deadline() {
		$deadline = strtotime( '+' . get_option( 'cc_interval' ), self::time_of_latest_post_in_seconds() );
		return $deadline - current_time('timestamp') < 0;
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
		if ( get_option( 'cc_death_notice' ) ) {
			die( '<h1>' . get_option( 'cc_death_notice' ) . '</h1>' );
		}
		die( '<h1>This blog is temporarily dead.</h1>' );
	}

	/*
	Back end
	*/

	public static function display_time_info() {
		$fuzzy = new BlogOrDieFuzzyTimeAgo( self::time_of_latest_post_in_seconds() );
		echo '<p>Your last post was published ' . $fuzzy->description() . '.</p>';
	}

	public static function add_menu() {
		add_options_page( 'Blog or Die Settings', 'Blog or Die', 'edit_pages', 'blog_or_die_settings', array( __CLASS__, 'render_settings_page' ), false, 62 );
	}

	public static function register_settings() {
		register_setting( 'cc_blog_or_die', 'cc_interval' );
		register_setting( 'cc_blog_or_die', 'cc_death_notice' );
	}

	public static function intervals() {
		return array(
			'a day'        => '1 days',
			'two days'     => '2 days',
			'three days'   => '3 days',
			'four days'    => '4 days',
			'five days'    => '5 days',
			'six days'     => '6 days',
			'a week'       => '1 weeks',
			'two weeks'    => '2 weeks',
			'three weeks'  => '3 weeks',
			'month'        => '1 months',
			'two months'   => '2 months',
			'three months' => '3 months',
			'six months'   => '6 months',
			'a year'       => '12 months',
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
			'cc_interval', // ID
			'Interval between posts', // Title
			array( __CLASS__, 'interval_view' ), // Callback
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
				?>
				<?php
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public static function interval_view() {
		echo( 'I need to ensure I leave no more than ' );
		echo( '<select id="duration" name="cc_interval">' );
		foreach ( self::intervals() as $key => $value ) {
			$selected_attr = '';
			if ( $value == get_option( 'cc_interval' ) ) {
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
