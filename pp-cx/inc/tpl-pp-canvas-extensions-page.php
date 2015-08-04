<?php
/**
 * Created by shramee
 * At: 5:45 PM 24/7/15
 * @package    PP_Canvas_Extensions_Page
 */
$def_cx = array(
	'author' => 'pootlepress',
	'author_url' => 'pootlepress',
	'wp_req' => '4.0.0',
);
$wp_ver = get_bloginfo( 'version' );

/**
 * Renders CX card html
 * @param string $id ID of card
 * @param array $cx CX data
 * @param id $wp_ver
 */
function pp_canvas_extensions_page_render_card( $id, $cx, $wp_ver ) {
	?>
	<div class="cx-card-wrap <?php echo $id; ?>-wrap">
		<div class="cx-card <?php echo $id; ?> <?php if ( ! empty( $s[ $id ] ) ) {
			echo 'active';
		} ?>">

			<div class="cx-img">
				<a href="<?php echo $cx['url'] ?>" class="thickbox"><img src="<?php echo $cx['img'] ?>"></a>
			</div>

			<div class="cx-controls">
				<?php
				if ( ! empty( $cx['settings_url'] ) ) {
					?>
					<a href="<?php echo $cx['settings_url'] ?>" class="button pootle"><?php _e( 'Settings', 'storefront-jetpack' ) ?></a>
				<?php
				} elseif ( empty( $cx['installed'] ) ) {
					?>
					<a href="<?php echo $cx['url'] ?>" class="button button-primary"><?php _e( 'Install', 'storefront-jetpack' ) ?></a>
				<?php
				}
				?>
			</div>

			<div class="cx-details">
				<div class="cx-name">
					<h4><a href="<?php echo $cx['url'] ?>" class="thickbox"><?php _e( $cx['label'], 'storefront-jetpack' ) ?></a></h4>
				</div>
				<div class="desc cx-description">
					<p class="cx-description"><?php echo $cx['description'] ?></p>
					<p class="cx-authors"> <cite><?php _e( 'By', 'storefront-jetpack' ) ?> <a href="<?php echo $cx['author_url'] ?>"><?php echo $cx['author'] ?></a></cite></p>
				</div>
			</div>
			<div class="cx-footer">
				<?php
				if ( ! empty( $cx['installed'] ) ) {
					?>
					<div class="cx-installed">
						You have this installed
					</div>
				<?php
				}

				if ( version_compare( $wp_ver, $cx['wp_req'] ) ) {
					?>
					<div class="cx-wp-compat">
						<b> <span class="dashicons dashicons-yes"></span> Compatible</b><span> with your version of WordPress</span>
					</div>
				<?php
				} else {
					?>
					<div class="cx-wp-compat">
						<b> <span class="dashicons dashicons-no"></span> Requires <?php $cx['wp_req'] ?></b>
					</div>
				<?php
				}
				?>
			</div>

		</div>
	</div>
<?php
}

?>

<div id="cx-page-settings-page" class="wrap">
	<div class="widefat cx-cards">
		<?php
		foreach( $extensions as $id => $cx ) {
			if ( empty( $cx['installed'] ) ) {
				continue;
			}
			$cx = wp_parse_args( $cx, $def_cx );
			pp_canvas_extensions_page_render_card( $id, $cx, $wp_ver );
		}
		foreach( $extensions as $id => $cx ) {
			if ( ! empty( $cx['installed'] ) ) {
				continue;
			}
			$cx = wp_parse_args( $cx, $def_cx );
			pp_canvas_extensions_page_render_card( $id, $cx, $wp_ver );
		}
		?>
	</div>
</div>