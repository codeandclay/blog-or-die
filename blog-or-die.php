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

function cc_prevent_page_load() {
	if ( cc_is_last_post_older_than( 10 ) ) {
		cc_death_notice();
	}
}

function cc_is_last_post_older_than( $seconds ) {
	return cc_age_of_post_in_seconds() > $seconds;
}

function cc_age_of_post_in_seconds() {
	return current_time( 'timestamp' ) - strtotime( get_posts()[0]->post_date );
}

function cc_death_notice() {
	// wp_die('Your blog is dead.');
	die( '<h1>Your blog is dead</h1>' );
}

add_action( 'template_redirect', 'cc_prevent_page_load' );
