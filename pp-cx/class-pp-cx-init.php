<?php
/**
 * Created by shramee
 * At: 11:16 PM 28/7/15
 */

if ( ! class_exists( 'PP_Canvas_Extensions_Init' ) ) {
	/**
	 * Creates Canvas Extensions Page
	 * * Initiates pootlepress wc-api\
	 * * Adds extension menu page
	 * Class PP_Canvas_Extensions_Init
	 */
	class PP_Canvas_Extensions_Init {

		/**
		 * Constructor Function
		 * Attaches the required hooks
		 *
		 * @param array $cx_data Extension data
		 *
		 * * key e.g. menu-customizer,
		 * * label e.g. Menu Customizer,
		 * * url e.g. 'http://www.pootlepress.com/shop/menu-customizer-woothemes-canvas/',
		 * * description e.g. "Imagine if you create just about ANY menu you wanted in Canvas? Imagine if you could do it REALLY quickly, with zero coding? That’s what Menu Customizer can do for you.",
		 * * img e.g. 'http://www.pootlepress.com/wp-content/uploads/2014/09/menu-customizer-icon3.png',
		 * * settings_url e.g. admin_url('admin.php?page=woothemes&tab=menu-customizer'),
		 * @param string $file __FILE__ of main plugin file
		 *
		 * @access public
		 */
		public function __construct( $cx_data, $file ) {

			//Assigning properties for later use
			$this->cx_data      = $cx_data;
			$this->file         = $file;

			$this->add_hooks();

			//Canvas extension page
			include 'inc/class-pp-canvas-extensions-page.php';
			include 'inc/class-pp-updator.php';
		}

		private function add_hooks() {
			add_filter( 'pp_canvas_extensions_list', array( $this, 'cx_active' ) );
			add_action( 'init', array( $this, 'pp_updator_init' ) );
		}

		public function pp_updator_init() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				include( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$data = get_plugin_data( $this->file );
			$plugin_current_version = $data['Version'];
			$plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
			$plugin_slug = plugin_basename( $this->file );
			new Pootlepress_Updater ( $plugin_current_version, $plugin_remote_path, $plugin_slug );
		}

		public function cx_active( $cxs ) {
			$cxs[ $this->cx_data['key'] ] = $this->cx_data;
			$cxs[ $this->cx_data['key'] ]['installed'] = true;

			return $cxs;
		}
	}
}