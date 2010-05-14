<?php
load_plugin_textdomain( 'bp-pms', false, dirname(plugin_basename(__FILE__)) . '/lang' );

function ray_messages_check_recipients_on_send( $message_info ) {
	global $bp;

	$recipients = $message_info->recipients;

	foreach ( $recipients as $recipient ) {
		if ( $recipient->user_id == $bp->loggedin_user->id )
			continue;

		// check if any of the attempted recipients is not a friend
		// if we get a match, break the recipient loop immediately
		if ( !friends_check_friendship( $bp->loggedin_user->id, $recipient->user_id ) ) {
			$is_friend = false;
			break;
		}
		else {
			$is_friend = true;
		}
	}

	// since the logged-in user isn't friends, we unset the recipients array so BP_Messages_Message:send() returns false
	// thus, the message isn't sent; thus no more spam! :)
	if ( $is_friend == false && ( $bp->loggedin_user->is_site_admin != 1 ) ) {
		unset( $message_info->recipients );
	}
}
add_action( 'messages_message_before_save', 'ray_messages_check_recipients_on_send' );

// thanks to Paul Gibbs for this technique!
function ray_pms_override_bp_l10n() {
	global $l10n;

	$mo = new MO();
	$mo->add_entry( array( 'singular' => 'There was an error sending that message, please try again', 'translations' => array( __ ('You are not friends with the person(s) you are attempting to send a message to.  Your message has not been sent.', 'bp-pms' ) ) ) );
	$mo->add_entry( array( 'singular' => 'There was a problem sending that reply. Please try again.', 'translations' => array( __ ('You are not friends with the person(s) you are attempting to send a message to.  Your message has not been sent.', 'bp-pms' ) ) ) );	

	if ( isset( $l10n['buddypress'] ) )
		$mo->merge_with( $l10n['buddypress'] );

	$l10n['buddypress'] = &$mo;
	unset( $mo );
}
add_action( 'init', 'ray_pms_override_bp_l10n', 9 );

// low-level way of removing the private message button if not friends
function ray_hide_private_message_btn() {
	global $bp;

	// check if we're on a member's page
	if ( bp_is_member() ) : 
		if ( !friends_check_friendship( $bp->loggedin_user->id, $bp->displayed_user->id ) ) :
?>
	<style type="text/css">#send-private-message {display:none;}</style>
<?php
		endif;
	endif;
}
add_action( 'wp_head', 'ray_hide_private_message_btn', 99 );

?>