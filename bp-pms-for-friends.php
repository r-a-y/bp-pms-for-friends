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
		// Admin notice.
		if ( ! class_exists( 'BP_Friends_Friendship' ) ) {
			add_action( 'admin_notices', array( $this, 'display_requirement' ) );
		}

		// 110n.
		load_plugin_textdomain( 'bp-pms', false, dirname(plugin_basename(__FILE__)) . '/lang' );

		// Set up recipient whitelist.
		if ( defined( 'BP_PM_RECIPIENT_WHITELIST' ) ) {
			$this->whitelist_ids = explode( ',', BP_PM_RECIPIENT_WHITELIST );
		}

		// Hooks.
		add_action( 'messages_message_before_save', array( $this, 'check_recipients' ) );
		add_action( 'template_redirect', array( $this, 'override_bp_l10n' ), 9 );
		add_action( function_exists( 'bp_is_user' ) ? 'bp_members_screen_display_profile' : 'init', array( $this, 'hide_pm_btn' ), 99 );
	}

	/**
	 * Check recipients before saving message.
	 *
	 * @param BP_Messages_Message $message_info Current message object.
	 */
	public function check_recipients( $message_info ) {
		$recipients = $message_info->recipients;

		$u = 0;

		foreach ( $recipients as $key => $recipient ) {
			$is_whitelisted = in_array( $recipient->user_id, $this->whitelist_ids );

			// if recipient is whitelisted, skip check
			if ( $is_whitelisted ) {
				continue;
			}

			// if site admin, skip check
			if( $GLOBALS['bp']->loggedin_user->is_site_admin == 1 ) {
				continue;
			}

			// make sure sender is not trying to send to themselves
			if ( $recipient->user_id == bp_loggedin_user_id() ) {
				unset( $message_info->recipients[$key] );
				continue;
			}

			/*
			 * Check if the attempted recipient is not a friend.
			 *
			 * If we get a match, remove person from recipient list. If there are no
			 * recipients, BP_Messages_Message:send() will bail out of sending.
			 */
			if ( ! friends_check_friendship( bp_loggedin_user_id(), $recipient->user_id ) ) {
				unset( $message_info->recipients[$key] );
				$u++;
			}
		}

		/*
		 * If there are multiple recipients and if one of the recipients is not a
		 * friend, remove everyone from the recipient's list.
		 *
		 * This is done to prevent the message from being sent to anyone and is
		 * another spam prevention measure.
		 */
		if ( count( $recipients ) > 1 && $u > 0 ) {
			unset( $message_info->recipients );
		}
	}

	/**
	 * Error message overrider method.
	 *
	 * Thanks to Paul Gibbs for this technique!
	 */
	public function override_bp_l10n() {
		// Bail if not on the compose screen.
		if ( ! bp_is_messages_compose_screen() ) {
			return;
		}

		$message = __( 'You are not friends with the person(s) you are attempting to send a message to.  Your message has not been sent.', 'bp-pms' );

		$mo = new MO();
		$mo->add_entry( array( 'singular' => 'There was an error sending that message, please try again', 'translations' => array( $message ) ) );
		$mo->add_entry( array( 'singular' => 'There was a problem sending that reply. Please try again.', 'translations' => array( $message ) ) );
		$mo->add_entry( array( 'singular' => 'Message was not sent. Please try again.', 'translations' => array( $message ) ) );

		if ( isset( $GLOBALS['l10n']['buddypress'] ) ) {
			$mo->merge_with( $GLOBALS['l10n']['buddypress'] );
		}

		$GLOBALS['l10n']['buddypress'] = &$mo;
		unset( $mo );
	}

	/**
	 * Remove the private message button on user pages.
	 *
	 * The button is removed if:
	 *  - displayed user isn't on whitelist, AND
	 *  - logged-in user isn't a site admin, AND
	 *  - logged-in user isn't a friend of the displayed user.
	 *
	 * In BP 1.5, button is removed entirely. In BP <= 1.2, button is just hidden.
	 */
	public function hide_pm_btn() {
		// Bail if user isn't logged in or not on a user page.
		if ( ! is_user_logged_in() || ! bp_displayed_user_id() ) {
			return;
		}

		$is_whitelisted = in_array( bp_displayed_user_id(), $this->whitelist_ids );

		// Hide the button.
		if ( ! $is_whitelisted && ( $GLOBALS['bp']->loggedin_user->is_site_admin != 1 ) && ! friends_check_friendship( bp_loggedin_user_id(), bp_displayed_user_id() ) ) {
			// For BP 1.5+.
			remove_action( 'bp_member_header_actions', 'bp_send_private_message_button', 20 );

			// For BP 1.2 and below.
			if ( ! function_exists( 'bp_is_user' ) ) {
				echo '<style type="text/css">#send-private-message {display:none;}</style>';
			}
		}
	}

	/**
	 * Display requirement if Friends component isn't active.
	 *
	 * @todo l10n?
	 */
	public function display_requirement() {
		echo '<div class="error fade"><p>BuddyPress Private Messages for Friends Only requires the BuddyPress <strong>Friends component</strong> to be enabled. Please <a href="admin.php?page=bp-component-setup">enable</a> this now.</p></div>';
	}
}

$pms_friends = new BP_PMs_Friends();
$pms_friends->init();
