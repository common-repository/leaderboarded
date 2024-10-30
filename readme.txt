=== Plugin Name ===
Contributors: tobyberesford
Donate link: http://www.microaid.org/
Tags: leaderboard, leaderboarded, gamification, table, list, rise, rise.global, risemob
Requires at least: 2.0.2
Tested up to: 4.6.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed a public or unlisted leaderboard from rise.global on any page or post using a shortcode.

== Description ==

Embed a public leaderboard from Rise.global on any page or post using a shortcode.

For example:

*  [leaderboarded slug='gurus' release='578'] will show the Gamification Gurus for May 2012
*  [leaderboarded slug='gurus'] will show the latest release of the Gamification Gurus

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `leaderboarded.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit Rise.global to get the embed shortcode for your leaderboard
1. Add the shortcode to one of your posts or pages

== Frequently Asked Questions ==

= What is a slug? =

This is the web address for the leaderboard.

= What if I always want to show the latest release? =

Instead of the release id number in the short code, use 'latest' instead. Or simply leave it out.

== Screenshots ==

1. A leaderboard embedded into a Wordpress post / page


== Changelog ==

= 0.28 =
* Fix for servers not allowing php short codes

= 0.27 =
* Support for different format embeds

= 0.26 =
* Change cache key to be theme specific
* Allow shortcodes within widgets

= 0.25 =
* Fix display error on saving

= 0.24 =
* Bug fix to support unsigned requests with earlier versions of PHP

= 0.23 =
* Bug fix

= 0.22 =
* Avoid warning notifications on first instal

= 0.21 =
* Test for curl
* Support unsigned requests as an option

= 0.20 =
* Changed name of plugin back to leaderboarded for now

= 0.19 =
* Updated instructions for use to rise.global.

= 0.18 =
* Use rise.global URL instead of leaderboarded.com

= 0.17 =
* Caching now enabled by default

= 0.16 =
* Support for caching

= 0.1 =
* First release of this plugin

== Upgrade Notice ==

Upgrade now to enable caching of your leaderboards - this will greatly improve page performance.