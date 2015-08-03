<?php

/**
 * Pooltepress Admin Menu Class
 *
 * @package Update API Manager/Admin
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PootlePress_API_Manager_Menu' ) ) {
	class PootlePress_API_Manager_Menu {

		// Load admin menu
		public function __construct() {
			add_action( 'pp_canvas_extensions_cx_page_' . $this->cx_key, array( $this, 'pp_api_menu_config_page' ) );
			add_action( 'admin_init', array( $this, 'pp_api_menu_load_settings' ) );
			add_action( 'admin_print_styles', array( $this, 'pp_api_menu_css_scripts' ) );
		}

		// Add option page menu
		public function pp_api_menu_add_menu() {

			$page = add_options_page( __( $this->settings_menu_title, $this->text_domain ), __( $this->settings_menu_title, $this->text_domain ),
				'manage_options', $this->activation_tab_key, array( $this, 'pp_api_menu_config_page' )
			);
		}

		// Draw option page
		public function pp_api_menu_config_page() {

			$settings_tabs = apply_filters( 'pp_cx_page_' . $this->cx_key . '_tabs', array() );
			//Adding api tabs
			$settings_tabs[ $this->activation_tab_key   ] = __( $this->menu_tab_activation_title, $this->text_domain );
			$settings_tabs[ $this->deactivation_tab_key ] = __( $this->menu_tab_deactivation_title, $this->text_domain );
			$tab = $current_tab = filter_input( INPUT_GET, 'tab' );

			if ( empty( $tab ) ) {
				$tab = $this->activation_tab_key;
			}

			settings_errors();
			?>
			<div class='wrap'>
				<h2><?php _e( $this->settings_title, $this->text_domain ); ?></h2>

				<ul class="subsubsub">
					<?php
					foreach ( $settings_tabs as $tab_page => $tab_name ) {
						$active_tab = $tab == $tab_page ? 'current' : '';
						if ( 0 !== strpos( $tab_page, '?' ) && 0 !== strpos( $tab_page, 'http://' ) ) {
							echo '<li><a class="tab ' . $active_tab . '" href="?page=pp-extensions&cx=' . $this->cx_key . '&tab=' . $tab_page . '">' . $tab_name . '</a></li>';
						} else {
							echo '<li><a class="tab ' . $active_tab . '" href="' . $tab_page . '">' . $tab_name . '</a></li>';
						}
					}
					?>
				</ul>
				<div class="clear"></div>
				<?php
				if ( in_array( $tab, array($this->activation_tab_key, $this->deactivation_tab_key ) ) ) {
					?>

					<form action='options.php' method='post'>
						<div class="main">
							<?php
							if ( $tab == $this->activation_tab_key ) {
								settings_fields( $this->data_key );
								do_settings_sections( $this->activation_tab_key );
								submit_button( __( 'Save Changes', $this->text_domain ) );
							} else {
								settings_fields( $this->deactivate_checkbox );
								do_settings_sections( $this->deactivation_tab_key );
								submit_button( __( 'Save Changes', $this->text_domain ) );
							}
							?>
						</div>
					</form>
				<?php
				} elseif ( array_key_exists( $tab, $settings_tabs ) ) {
					do_action( 'pp_canvas_extensions_cx_tab_' . $this->cx_key . '_' . $tab );
				}
			?>
			</div>
		<?php
		}

		// Register settings
		public function pp_api_menu_load_settings() {

			register_setting( $this->data_key, $this->data_key, array( $this, 'pp_api_menu_validate_options' ) );

			// API Key
			add_settings_section( 'api_key', __( 'API License Activation', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_api_key_text'
			), $this->activation_tab_key );
			add_settings_field( 'status', __( 'API License Key Status', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_api_key_status'
			), $this->activation_tab_key, 'api_key' );
			add_settings_field( 'api_key', __( 'API License Key', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_api_key_field'
			), $this->activation_tab_key, 'api_key' );
			add_settings_field( 'activation_email', __( 'API License email', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_api_email_field'
			), $this->activation_tab_key, 'api_key' );

			// Activation settings
			register_setting( $this->deactivate_checkbox, $this->deactivate_checkbox, array(
				$this,
				'pp_api_menu_wc_am_license_key_deactivation'
			) );
			add_settings_section( 'deactivate_button', __( 'API License Deactivation', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_deactivate_text'
			), $this->deactivation_tab_key );
			add_settings_field( 'deactivate_button', __( 'Deactivate API License Key', $this->text_domain ), array(
				$this,
				'pp_api_menu_wc_am_deactivate_textarea'
			), $this->deactivation_tab_key, 'deactivate_button' );

		}

		// Provides text for api key section
		public function pp_api_menu_wc_am_api_key_text() {
			//
		}

		// Returns the API License Key status from the WooCommerce API Manager on the server
		public function pp_api_menu_wc_am_api_key_status() {
			$license_status       = $this->pp_api_menu_license_key_status();
			$license_status_check = ( ! empty( $license_status['status_check'] ) && $license_status['status_check'] == 'active' ) ? 'Activated' : 'Deactivated';
			if ( ! empty( $license_status_check ) ) {
				echo $license_status_check;
			}
		}

		// Returns API License text field
		public function pp_api_menu_wc_am_api_key_field() {

			echo "<input id='api_key' name='" . $this->data_key . "[" . 'api_key' . "]' size='25' type='text' value='" . $this->options['api_key'] . "' />";
			if ( $this->options['api_key'] ) {
				echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "pp-cx/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
			} else {
				echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "pp-cx/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
			}
		}

		// Returns API License email text field
		public function pp_api_menu_wc_am_api_email_field() {

			echo "<input id='activation_email' name='" . $this->data_key . "[activation_email]' size='25' type='text' value='" . $this->options['activation_email'] . "' />";
			if ( $this->options[ 'activation_email' ] ) {
				echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "pp-cx/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
			} else {
				echo "<span class='icon-pos'><img src='" . $this->plugin_url() . "pp-cx/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
			}
		}

		// Sanitizes and validates all input and output for Dashboard
		public function pp_api_menu_validate_options( $input ) {

			// Load existing options, validate, and update with changes from input before returning
			$options = $this->options;

			$options['api_key']                 = trim( $input['api_key'] );
			$options[ 'activation_email' ] = trim( $input[ 'activation_email' ] );

			/**
			 * Plugin Activation
			 */
			$api_email = trim( $input[ 'activation_email' ] );
			$api_key   = trim( $input['api_key'] );

			$activation_status = get_option( $this->activated_key );
			$checkbox_status   = get_option( $this->deactivate_checkbox );

			$current_api_key = $this->options['api_key'];

			// Should match the settings_fields() value
			if ( $_REQUEST['option_page'] != $this->deactivate_checkbox ) {

				if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key ) {

					/**
					 * If this is a new key, and an existing key already exists in the database,
					 * deactivate the existing key before activating the new key.
					 */
					if ( $current_api_key != $api_key ) {
						$this->pp_api_menu_replace_license_key( $current_api_key );
					}

					$args = array(
						'email'       => $api_email,
						'licence_key' => $api_key,
					);

					$activate_results = json_decode( $this->key_class->activate( $args ), true );

					if ( $activate_results['activated'] === true ) {
						add_settings_error( 'activate_text', 'activate_msg', __( 'Plugin activated. ', $this->text_domain ) . "{$activate_results['message']}.", 'updated' );
						update_option( $this->activated_key, 'Activated' );
						update_option( $this->deactivate_checkbox, 'off' );
					}

					if ( $activate_results == false ) {
						add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Try again later.', $this->text_domain ), 'error' );
						$options['api_key']                 = '';
						$options[ 'activation_email' ] = '';
						update_option( $this->options[ $this->activated_key ], 'Deactivated' );
					}

					if ( isset( $activate_results['code'] ) ) {

						switch ( $activate_results['code'] ) {
							case '100':
								add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options[ 'activation_email' ] = '';
								$options['api_key']                 = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '101':
								add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '102':
								add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '103':
								add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '104':
								add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '105':
								add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
							case '106':
								add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
								$options['api_key']                 = '';
								$options[ 'activation_email' ] = '';
								update_option( $this->options[ $this->activated_key ], 'Deactivated' );
								break;
						}

					}

				} // End Plugin Activation

			}

			return $options;
		}

		// Returns the API License Key status from the WooCommerce API Manager on the server
		public function pp_api_menu_license_key_status() {
			$activation_status = get_option( $this->activated_key );

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $this->options['api_key'],
			);

			return json_decode( $this->key_class->status( $args ), true );
		}

		// Deactivate the current license key before activating the new license key
		public function pp_api_menu_replace_license_key( $current_api_key ) {

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $current_api_key,
			);

			$reset = $this->key_class->deactivate( $args ); // reset license key activation

			if ( $reset == true ) {
				return true;
			}

			return add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', $this->text_domain ), 'updated' );
		}

		// Deactivates the license key to allow key to be used on another blog
		public function pp_api_menu_wc_am_license_key_deactivation( $input ) {

			$activation_status = get_option( $this->activated_key );

			$args = array(
				'email'       => $this->options[ 'activation_email' ],
				'licence_key' => $this->options['api_key'],
			);

			$options = ( $input == 'on' ? 'on' : 'off' );

			if ( $options == 'on' && $activation_status == 'Activated' && $this->options['api_key'] != '' && $this->options[ 'activation_email' ] != '' ) {

				// deactivates license key activation
				$activate_results = json_decode( $this->key_class->deactivate( $args ), true );

				// Used to display results for development
				//print_r($activate_results); exit();

				if ( $activate_results['deactivated'] === true ) {
					$update = array(
						'api_key'               => '',
						'activation_email' => ''
					);

					$merge_options = array_merge( $this->options, $update );

					update_option( $this->data_key, $merge_options );

					update_option( $this->activated_key, 'Deactivated' );

					add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', __( 'Plugin license deactivated. ', $this->text_domain ) . "{$activate_results['activations_remaining']}.", 'updated' );

					return $options;
				}

				if ( isset( $activate_results['code'] ) ) {

					switch ( $activate_results['code'] ) {
						case '100':
							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[ 'activation_email' ] = '';
							$options['api_key']                 = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '101':
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '102':
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '103':
							add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '104':
							add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '105':
							add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
						case '106':
							add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options['api_key']                 = '';
							$options[ 'activation_email' ] = '';
							update_option( $this->options[ $this->activated_key ], 'Deactivated' );
							break;
					}

				}

			} else {

				return $options;
			}

		}

		public function pp_api_menu_wc_am_deactivate_text() {
		}

		public function pp_api_menu_wc_am_deactivate_textarea() {

			echo '<input type="checkbox" id="' . $this->deactivate_checkbox . '" name="' . $this->deactivate_checkbox . '" value="on"';
			echo checked( get_option( $this->deactivate_checkbox ), 'on' );
			echo '/>';
			?><span
				class="description"><?php _e( 'Deactivates an API License Key so it can be used on another blog.', $this->text_domain ); ?></span>
		<?php
		}

		// Loads admin style sheets
		public function pp_api_menu_css_scripts() {
			if ( 'pp-extensions' != filter_input( INPUT_GET, 'page' ) || $this->cx_key != filter_input( INPUT_GET, 'cx' ) ) {
				return;
			}
			wp_register_style( $this->data_key . '-css', $this->plugin_url() . 'pp-cx/assets/css/admin-settings.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->data_key . '-css' );
		}

	}
}