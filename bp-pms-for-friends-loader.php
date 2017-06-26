<?php
/*
Plugin Name: BuddyPress Private Messages for Friends Only
Description: Only allows friends and site administrators to send private messages on your BuddyPress site.
Author: r-a-y
Author URI: http://profiles.wordpress.org/r-a-y
Version: 1.2

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CT8KZGFEVA3R6
*/

function bp_pms_for_friends_init() {
	require( dirname( __FILE__ ) . '/bp-pms-for-friends.php' );
}
add_action( 'bp_init', 'bp_pms_for_friends_init' );
