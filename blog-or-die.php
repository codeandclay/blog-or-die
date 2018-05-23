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
	return cc_time_of_latest_post_in_seconds() > $seconds;
}

function cc_time_of_latest_post_in_seconds() {
	// It is not gauranteed that the first item returned by get_posts() is the
	// latest post. So here I compare the timestamps of all posts.
	$times = array_map(
		function( $post ) {
			return $post->post_date;
		}, get_posts()
	);
	sort( $times );
	return end( $times );
}

function cc_death_notice() {
	// wp_die('Your blog is dead.');
	die( '<h1>Your blog is dead</h1>' );
}

add_action( 'template_redirect', 'cc_prevent_page_load' );
