<?php

if (!function_exists('check_main_heading')) {
    function check_main_heading() {
        $options = get_option('woo_template');
        if (!in_array("Canvas Extensions", $options)) {
            function woo_options_add($options){
                $i = count($options);
                $options[$i++] = array(
                    'name' => __('Canvas Extensions', 'pootlepress-canvas-extensions' ),
                    'icon' => 'favorite',
                    'type' => 'heading'
                );
                return $options;
            }
        }
    }
}

function pootlepress_masonry_blog_render() {
//    while (have_posts()) {
//        the_post();
//        wc_get_template_part( 'content', 'product' );
//    }
//    get_template_part( 'loop', 'magazine' );

/**
 * Loop - Magazine
 *
 * This is the loop logic file for the "Magazine" page template.
 *
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
        // Featured Starts
        if ( isset( $woo_options['woo_magazine_feat_posts'] ) && $count <= $woo_options['woo_magazine_feat_posts'] && ! is_paged() ) {
            woo_get_template_part( 'content', 'magazine-featured' );
            continue;
        }

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