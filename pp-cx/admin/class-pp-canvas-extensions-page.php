<?php
/**
 * Created by shramee
 * At: 5:22 PM 24/7/15
 */

if ( ! class_exists( 'PP_Canvas_Extensions_Page' ) ) {
	/**
	 * Creates Canvas Extensions Page
	 * Class PP_Canvas_Extensions_Page
	 */
	class PP_Canvas_Extensions_Page {

		/**
		 * @var PP_Canvas_Extensions_Page Instance
		 */
		private static $_instance;

		/**
		 * Returns the instance
		 * @return PP_Canvas_Extensions_Page
		 */
		public static function instance() {

			if ( empty( PP_Canvas_Extensions_Page::$_instance ) ) {
				PP_Canvas_Extensions_Page::$_instance = new PP_Canvas_Extensions_Page();
			}

			return PP_Canvas_Extensions_Page::$_instance;
		}

		/**
		 * Constructor Function
		 * Attaches the required hooks
		 * @access private
		 */
		private function __construct() {

			//Adding Canvas Extensions page
			add_action( 'admin_menu', array( $this, 'add_extensions_page' ), 99 );

			//Enqueue scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		}

		/**
		 * Adds the extension page to wp menu
		 * @action admin_menu
		 */
		public function enqueue() {

			if ( 'pp-extensions' == filter_input( INPUT_GET, 'page' ) ) {
				wp_enqueue_style( 'pp-cx-page-styles', plugin_dir_url( __FILE__ ) . '../assets/css/cx-cards.css' );
			}

		} // End add_extensions_page()

		/**
		 * Adds the extension page to wp menu
		 * @action admin_menu
		 */
		public function add_extensions_page() {

			// Add extensions page
			add_submenu_page( 'woothemes', __( 'Extensions', 'woothemes' ), __( 'Extensions', 'woothemes' ), 'manage_options', 'pp-extensions', array( $this, 'extensions_page' ) );
		} // End add_extensions_page()

		/**
		 * Outputs the html for extensions page
		 */
		public function extensions_page() {
			do_action( 'wf_screen_get_header' );
			$all_tabs = apply_filters( 'pp_canvas_extensions_cx_tabs', array() );
			/** Current tab */
			$c_tab = '';
			if ( array_key_exists( filter_input( INPUT_GET, 'cx' ), $all_tabs ) ) {
				$c_tab = filter_input( INPUT_GET, 'cx' );
			}
			?>
			<style>
				.nav-tab-wrapper .logo img {
					display: none;
				}
				.nav-tab-wrapper .logo {
					background: url(<?php echo plugin_dir_url( $GLOBALS['pootlepress_primary_nav_manager']->file ) ?>styles/images/pootle-logo.png) center/contain no-repeat;
					height: 45px;
					width: 160px;
					margin: -14px -14px 0 0;
				}
				@media only screen and (max-width: 1100px) {
					.nav-tab-wrapper .logo {
						height: 30px;
						width: 115px;
						margin: 3px -15px 0 3px;
					}
				}
			</style>
			<div class="clear"></div>
			<?php
			if ( empty( $c_tab ) ) {
				$extensions = $this->add_cx();
				include plugin_dir_path( __FILE__ ) . '../admin/tpl-pp-canvas-extensions-page.php';
			}
			do_action( 'pp_canvas_extensions_cx_page_' . $c_tab );
			do_action( 'wf_screen_get_footer' );
		}

		public function add_cx() {

			return apply_filters( 'pp_canvas_extensions_list', array() );
		}
	}
}
PP_Canvas_Extensions_Page::instance();
