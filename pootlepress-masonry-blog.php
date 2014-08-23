<?php
/*
Plugin Name: Canvas Extension - Masonry Blog
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to use masonry effect in Magazine template.
Version: 1.1.1
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-masonry-blog-functions.php' );
require_once( 'classes/class-pootlepress-masonry-blog.php' );
require_once( 'classes/class-pootlepress-updater.php');

$GLOBALS['pootlepress_masonry_blog'] = new Pootlepress_Masonry_Blog( __FILE__ );
$GLOBALS['pootlepress_masonry_blog']->version = '1.1.1';

add_action('init', 'pp_mb_updater');
function pp_mb_updater()
{
    if (!function_exists('get_plugin_data')) {
        include(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data(__FILE__);
    $wptuts_plugin_current_version = $data['Version'];
    $wptuts_plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new Pootlepress_Updater ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}
?>
