<?php
/*
Plugin Name: Canvas Extension - Masonry Blog
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to use masonry effect in Magazine template.
Version: 1.1.2
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-masonry-blog-functions.php' );
require_once( 'classes/class-pootlepress-masonry-blog.php' );
require_once( 'classes/class-pootlepress-canvas-options.php' );

$GLOBALS['pootlepress_masonry_blog'] = new Pootlepress_Masonry_Blog( __FILE__ );
$GLOBALS['pootlepress_masonry_blog']->version = '1.1.2';

//CX API
require 'pp-cx/class-pp-cx-init.php';
new PP_Canvas_Extensions_Init(
	array(
		'key'          => 'masonry-blog',
		'label'        => 'Masonry Blog',
		'url'          => 'http://www.pootlepress.com/shop/masonry-blog-for-woothemes-canvas/',
		'description'  => "Allows you to style the standard Canvas mobile menu without complex CSS. What would take hours, now takes minutes.",
		'img'          => 'http://www.pootlepress.com/wp-content/uploads/2014/01/masonry.png',
		'installed'    => true,
		'settings_url' => admin_url( 'admin.php?page=woothemes&tab=masonry-blog' ),
	),
	__FILE__
);
