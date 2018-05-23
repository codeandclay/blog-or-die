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
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'prevent_page_load' ) );
	}

	public static function prevent_page_load() {
		if ( self::is_last_post_older_than( self::time_limit_in_seconds() ) ) {
			self::death_notice();
		}
	}

	private static function is_last_post_older_than( $seconds ) {
		return self::time_of_latest_post_in_seconds() > $seconds;
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
}

CCBlogOrDie::init();
