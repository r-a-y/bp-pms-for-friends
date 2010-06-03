<?php
/*
Plugin Name: BuddyPress Private Messages for Friends Only
Description: This plugin only allows friends and site administrators to send private messages on your BuddyPress site.
Author: r-a-y
Author URI: http://buddypress.org/community/members/r-a-y
Plugin URI: http://buddypress.org/community/groups/buddypress-private-message-for-friends-only
Version: 1.1

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CT8KZGFEVA3R6
*/

function bp_pms_for_friends_init() {
	require( dirname( __FILE__ ) . '/bp-pms-for-friends.php' );
}
add_action( 'bp_init', 'bp_pms_for_friends_init' );
?>