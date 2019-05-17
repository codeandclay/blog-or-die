<?php
/**
 * @package Blog or Die
 * @version 1.0
 */
/*
Plugin Name: Blog or Die
Plugin URI: https://usefulplugins.tech
Description: Publish or your blog gets it.
Author: Oliver Denman
Version: 1.0
Author URI: www.usefulplugins.tech
*/

require_once plugin_dir_path( __FILE__ ) . '/class-blogordiefuzzytime.php';
require_once plugin_dir_path( __FILE__ ) . '/class-blogordieadminnotice.php';

class CCBlogOrDie {

	const DEFAULT_DEATH_NOTICE = 'I have failed to meet my own expectations and I should be ashamed of myself.';

	public static function run() {
		add_action( 'template_redirect', array( __CLASS__, 'prevent_page_load' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_notices', array( __CLASS__, 'display_time_info' ) );

        register_activation_hook( __FILE__, array( __CLASS__, 'set_default_interval' ) );
	}

    public static function set_default_interval() {
        update_option( "cc_interval", "7 days" );
    }

	public static function prevent_page_load() {
        // Bail out if there are no published posts
		if ( ! get_posts() ) {
			return;
		}
		if ( self::was_last_post_published_after_deadline() ) {
			self::display_death_notice();
		}
	}

	private static function was_last_post_published_after_deadline() {
		return self::deadline() - current_time('timestamp') < 0;
	}

	private static function deadline() {
		return strtotime( '+' . get_option( 'cc_interval' ), self::time_of_latest_post_in_seconds() );
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

	private static function display_death_notice() {
		if ( get_option( 'cc_death_notice' ) ) {
			self::death_notice( get_option( 'cc_death_notice' ) );
		}
		self::death_notice( self::DEFAULT_DEATH_NOTICE );
	}

	private static function death_notice($message) {
		die(
				'<style>body { background-color: black } </style>' .
				'<div class="death_notice" style="position : absolute; display: table; width: 100%; height: 100%;">' .
				'<div style="display: table-cell; vertical-align: middle; text-align: center;">' .
					 '<p style="text-align: left; font-family: sans-serif; width: 25rem; margin: 0 auto; color: white">' .
							$message .
					 '</p>' .
				'</div>' .
				'</div>'
		);
	}

	/*
	Back end
	*/

	public static function display_time_info() {
        // Bail out if there are no published post
        if ( ! get_posts() ) {
            return;
        }

        $fuzzy = new BlogOrDieFuzzyTime(self::time_of_latest_post_in_seconds());
        if (self::was_last_post_published_after_deadline()) {
            $message = "Blog or Die has disabled your blog. You must publish a new post to re-enable it. ☠️";
            (new BlogOrDieAdminNoticeError($message))->display();
        } else {
            $message = "You published your last post " . $fuzzy->over_rough_period() ." ago. " .
                       "You have until " . date_i18n( "F j, Y g:i", self::deadline()) . " to publish a new post " .
                       "or your blog will be disabled.";
            (new BlogOrDieAdminNoticeWarning($message))->display();
        }
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
		$text = self::DEFAULT_DEATH_NOTICE;
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
