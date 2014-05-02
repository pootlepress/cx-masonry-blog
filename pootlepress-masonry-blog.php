<?php
/*
Plugin Name: Canvas Extension - Masonry Blog
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to use masonry effect in Magazine template.
Version: 1.0.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-masonry-blog-functions.php' );
require_once( 'classes/class-pootlepress-masonry-blog.php' );

$GLOBALS['pootlepress_masonry_blog'] = new Pootlepress_Masonry_Blog( __FILE__ );
$GLOBALS['pootlepress_masonry_blog']->version = '1.0.0';

?>
