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
		 * * settings_url e.g. admin_url('admin.php?page=pp-extensions&cx=menu-customizer'),
		 * @param array $cx_tabs Extension data
		 * * key => name,
		 * @param string $token Plugin token
		 * @param string $name Plugin identifier for pootlepress
		 * @param string $ver Current plugin version
		 * @param string $file __FILE__ of main plugin file
		 * @param string $domain Translation text domain of plugin
		 * @param string $upgrade_url Upgrade server url
		 *
		 * @access public
		 */
		public function __construct( $cx_data, $cx_tabs, $token, $name, $ver, $file, $domain = null, $upgrade_url = 'http://pootlepress.com/' ) {

			if ( empty( $cx_data['key'] ) ) {
				$cx_data['key'] = $token;
			}

			//Assigning properties for later use
			$this->cx_data      = $cx_data;
			$this->cx_tabs      = $cx_tabs;
			$this->token        = $token;
			$this->name         = $name;
			$this->version      = $ver;
			$this->file         = $file;
			$this->domain       = $cx_data['key'];
			$this->upgrade_url  = $upgrade_url;

			$this->add_hooks();

			//Canvas extension page
			include 'admin/class-pp-canvas-extensions-page.php';

			//Pootlepress api integration
			include 'admin/class-pp-api-manager.php';
			new PootlePress_CX_API_Manager( $cx_data['key'], $token, $name, $ver, $file, $domain, $upgrade_url );

		}

		private function add_hooks() {
			add_filter( 'pp_canvas_extensions_list', array( $this, 'cx_active' ) );
			add_filter( 'pp_canvas_extensions_cx_tabs', array( $this, 'add_page' ) );
			add_filter( 'pp_cx_page_' . $this->cx_data['key'] . '_tabs', array( $this, 'add_tabs' ) );
		}

		public function cx_active( $cxs ) {
			$cxs[ $this->cx_data['key'] ] = $this->cx_data;
			$cxs[ $this->cx_data['key'] ]['installed'] = true;

			return $cxs;
		}

		public function add_page( $tabs ) {

			if ( is_array( $tabs ) ) {
				$tabs[ $this->cx_data['key'] ] = $this->cx_data['key'];
			}

			return $tabs;
		}

		public function add_tabs( $tabs ) {
			return array_merge( $this->cx_tabs, $tabs );
		}
	}
}