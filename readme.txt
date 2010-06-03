=== BuddyPress Private Messages for Friends Only ===
Contributors: r-a-y
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CT8KZGFEVA3R6
Tags: buddypress, message, private message, pm, spam
Requires at least: WP 2.9 & BuddyPress 1.2
Tested up to: WP 2.9.2 & BuddyPress 1.2.3
Stable tag: 1.1

This plugin only allows friends and site administrators to send private messages on your BuddyPress site.

== Description ==

By default, any member on your BuddyPress site is allowed to send private messages to anyone in your userbase.

This plugin only allows friends and site administrators to send private messages.  Thus, acting as another deterrent from illegitimate users.


== Installation ==

1. Download, install and activate the plugin.
1. That's it! :)


== Frequently Asked Questions ==

#### What happens when I try to send someone a private message who isn't my friend? ####

You'll get a nice error message saying that your message wasn't sent because you are not the recipient's friend!
Site administrators can always send private messages.


#### Can I allow certain user IDs to be private messaged without being friends? ####

As of version 1.1, you can!  Simply add the following line to /wp-config.php or /wp-content/plugins/bp-custom.php:

`define( 'BP_PM_RECIPIENT_WHITELIST', '1,2' );`

In the example above, anyone can send a message to user ID 1 and 2 without needing to be their friend.


#### I noticed that the error message is in English! ####

Yes it is.  But fear not!  You can send me a translation file for inclusion in the next release of the plugin.


== Donate! ==

I'm a forum moderator on the buddypress.org forums.  I spend a lot of my free time helping people - pro bono!

If you downloaded this plugin and like it, please:

* [Fund my work soundtrack!](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CT8KZGFEVA3R6)  Music helps me help you!  A dollar lets me buy a new tune off Amazon MP3, Amie Street or emusic.com!  Or if you're feeling generous, you can help me buy a whole CD!  If you choose to donate, let me know what songs or which CD you want me to listen to! :)
* Rate this plugin
* Spread the gospel of BuddyPress


== Changelog ==

= 1.1 =
* Added support to whitelist recipient user IDs
* Make sure site admins can always view the "Send Private Message" button (thanks to intimez and brianglanz for reporting)
* Added check to see if friends component is enabled
* Restructured the plugin into a class

= 1.0 =
* First version!