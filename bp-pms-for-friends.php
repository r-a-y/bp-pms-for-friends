<?php
class BP_PMs_Friends {

	/**
	 * User IDs that shouldn't be affected by this plugin.
	 *
	 * @var array
	 */
	var $whitelist_ids = array();

	/**
	 * Init method.
	 */
	public function init() {
		if ( ! class_exists( 'BP_Friends_Friendship' ) ) {
			add_action( 'admin_notices', array( $this, 'display_requirement' ) );
		}

		load_plugin_textdomain( 'bp-pms', false, dirname(plugin_basename(__FILE__)) . '/lang' );

		if ( defined( 'BP_PM_RECIPIENT_WHITELIST' ) ) {
			$this->whitelist_ids = explode( ',', BP_PM_RECIPIENT_WHITELIST );
		}

		add_action( 'messages_message_before_save', array( $this, 'check_recipients' ) );
		add_action( 'init', array( $this, 'override_bp_l10n' ), 9 );
		add_action( 'wp_head', array( $this, 'hide_pm_btn' ), 99 );
	}

	public function check_recipients( $message_info ) {
		$recipients = $message_info->recipients;

		$u = 0; // # of recipients in the message that are not friends

		foreach ( $recipients as $key => $recipient ) {
			$is_whitelisted = in_array( $recipient->user_id, $this->whitelist_ids );

			// if recipient is whitelisted, skip check
			if( $is_whitelisted )
				continue;

			// if site admin, skip check
			if( $GLOBALS['bp']->loggedin_user->is_site_admin == 1 )
				continue;

			// make sure sender is not trying to send to themselves
			if ( $recipient->user_id == bp_loggedin_user_id() ) {
				unset( $message_info->recipients[$key] );
				continue;
			}

			// check if the attempted recipient is not a friend
			// if we get a match, remove person from recipient list
			// if there are no recipients, BP_Messages_Message:send() will return false and thus message isn't sent!
			if ( ! friends_check_friendship( bp_loggedin_user_id(), $recipient->user_id ) ) {
				unset( $message_info->recipients[$key] );
				$u++;
			}
		}

		// if there are multiple recipients and if one of the recipients is not a friend, remove everyone from the recipient's list
		// this is done to prevent the message from being sent to anyone and is another spam prevention measure
		if ( count( $recipients ) > 1 && $u > 0 )
			unset( $message_info->recipients );
	}

	// thanks to Paul Gibbs for this technique!
	public function override_bp_l10n() {
		global $l10n;
	
		$mo = new MO();
		$mo->add_entry( array( 'singular' => 'There was an error sending that message, please try again', 'translations' => array( __ ('You are not friends with the person(s) you are attempting to send a message to.  Your message has not been sent.', 'bp-pms' ) ) ) );
		$mo->add_entry( array( 'singular' => 'There was a problem sending that reply. Please try again.', 'translations' => array( __ ('You are not friends with the person(s) you are attempting to send a message to.  Your message has not been sent.', 'bp-pms' ) ) ) );	
	
		if ( isset( $l10n['buddypress'] ) )
			$mo->merge_with( $l10n['buddypress'] );
	
		$l10n['buddypress'] = &$mo;
		unset( $mo );
	}

	// low-level way of removing the private message button if not friends, whitelisted, or site admin
	public function hide_pm_btn() {
		// check if we're on a member's page
		if ( bp_displayed_user_id() ) {
			$is_whitelisted = in_array( bp_displayed_user_id(), $this->whitelist_ids );

			if ( ! friends_check_friendship( bp_loggedin_user_id(), bp_displayed_user_id() ) && ! $is_whitelisted && ( $GLOBALS['bp']->loggedin_user->is_site_admin != 1 ) ) :
	?>
		<style type="text/css">#send-private-message {display:none;}</style>
	<?php
			endif;
		}
	}

	// should this be translatable?
	public function display_requirement() {
		echo '<div class="error fade"><p>BuddyPress Private Messages for Friends Only requires the BuddyPress <strong>Friends component</strong> to be enabled. Please <a href="admin.php?page=bp-component-setup">enable</a> this now.</p></div>';
	}
}

$pms_friends = new BP_PMs_Friends();
$pms_friends->init();
?>