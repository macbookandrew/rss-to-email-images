<?php
/**
 * Plugin Name: RSS-to-Email Images
 * Plugin URI: http://code.andrewrminion.com/rss-to-email-images/
 * Description: Fixes large images in RSS-to-email campaigns; based on http://css-tricks.com/dealing-content-images-email/
 * Version: 1.1
 * Author: AndrewRMinion Design
 * Author URI: http://andrewrminion.com
 * License: GPL3
 */

/*  Copyright 2014 AndrewRMinion Design (http://andrewrminion.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// create a new RSS feed specifically for use with RSS-to-email
function rss_to_email_custom_feeds() {
  add_feed('email', 'new_feed_template');
}
add_action('init', 'rss_to_email_custom_feeds');


// flush rewrite rule cache on activation/deactivation
function rss_to_email_activate() {
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'rss_to_email_activate');

function rss_to_email_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'rss_to_email_deactivate');

// get the email feed template
function new_feed_template() {
  add_filter('the_content_feed', 'rss_to_email_feed_image_magic');
  include(ABSPATH . '/wp-includes/feed-rss2.php' );
}


// change the HTML in the feed content
function rss_to_email_feed_image_magic($content) {
  // Weirdness we need to add to strip the doctype with later.
  $content = '<div>' . $content . '</div>';
  $doc = new DOMDocument();
  $doc->LoadHTML($content);
  $images = $doc->getElementsByTagName('img');
  foreach ($images as $image) {
    $image->removeAttribute('height');
    $image->setAttribute('width', '320');
  }
  // Strip weird DOCTYPE that DOMDocument() adds in
  $content = substr($doc->saveXML($doc->getElementsByTagName('div')->item(0)), 5, -6);
  return $content;
}


?>
