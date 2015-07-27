<?php
$health = 'ok';

if (!function_exists('check_main_heading')) {
    function check_main_heading() {
        global $health;
        if (!function_exists('woo_options_add') ) {
            function woo_options_add($options) {
                $cx_heading = array( 'name' => __('Canvas Extensions', 'pootlepress-canvas-extensions' ),
                    'icon' => 'favorite', 'type' => 'heading' );
                if (!in_array($cx_heading, $options))
                    $options[] = $cx_heading;
                return $options;
            }
        } else {	// another ( unknown ) child-theme or plugin has defined woo_options_add
            $health = 'ng';
        }
    }
}

add_action( 'admin_init', 'poo_commit_suicide' );

if(!function_exists('poo_commit_suicide')) {
    function poo_commit_suicide() {
        global $health;
        $pluginFile = str_replace('-functions', '', __FILE__);
        $plugin = plugin_basename($pluginFile);
        $plugin_data = get_plugin_data( $pluginFile, false );
        if ( $health == 'ng' && is_plugin_active($plugin) ) {
            deactivate_plugins( $plugin );
            wp_die( "ERROR: <strong>woo_options_add</strong> function already defined by another plugin. " .
                $plugin_data['Name']. " is unable to continue and has been deactivated. " .
                "<br /><br />Please contact PootlePress at <a href=\"mailto:support@pootlepress.com?subject=Woo_Options_Add Conflict\"> support@pootlepress.com</a> for additional information / assistance." .
                "<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>." );
        }
    }
}

function pootlepress_masonry_blog_render() {

	/**
	 * Loop - Magazine
	 * This is the loop logic file for the "Magazine" page template.
	 * @package WooFramework
	 * @subpackage Template
	 */

	global $wp_query, $woo_options, $paged, $page, $post;
	global $more; $more = 0;

	remove_action( 'woo_post_inside_before', 'woo_display_post_image', 10 );

	add_action( 'woo_post_inside_after', 'woo_post_more' );

	$query = $wp_query;
	if ( $query->have_posts() ) { $count = 0; $column_count_1 = 0; $column_count_2 = 0;
	    ?>

	    <?php
	    while ( $query->have_posts() ) { $query->the_post(); $count++;

	        $column_count_1++; $column_count_2++;
	        ?>
	        <div class="block<?php if ( $column_count_1 > 1 ) { echo esc_attr( ' last' ); $column_count_1 = 0; } ?>">
	            <?php
	            woo_get_template_part( 'content', 'magazine-grid' );
	            ?>
	        </div><!--/.block-->
	        <?php

	        if ( $column_count_1 == 0 ) { ?><div class="fix"></div><?php } // End IF Statement
	    } // End WHILE Loop
	} else {
	    get_template_part( 'content', 'noposts' );
	}

	woo_loop_after();
	woo_pagenav( $query );

	wp_reset_query();

	add_action( 'woo_post_inside_before', 'woo_display_post_image', 10 );
}