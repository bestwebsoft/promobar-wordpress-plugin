<?php
/*
Plugin Name: PromoBar by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/promobar/
Description: Add and display HTML advertisement on WordPress website. Customize bar styles and appearance.
Author: BestWebSoft
Text Domain: promobar
Domain Path: /languages
Version: 1.1.4
Author URI: https://bestwebsoft.com/
License: GPLv3 or later
*/

/*  @ Copyright 2017  BestWebSoft  ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );
/**
* Add Wordpress page 'bws_panel' and sub-page of this plugin to admin-panel.
* @return void
*/
if ( ! function_exists( 'add_prmbr_admin_menu' ) ) {
	function add_prmbr_admin_menu() {
		bws_general_menu();
		$settings = add_submenu_page( 'bws_panel', __( 'PromoBar Settings', 'promobar' ), 'PromoBar', 'manage_options', 'promobar.php', 'prmbr_settings_page' );

		add_action( 'load-' . $settings, 'prmbr_add_tabs' );
	}
}

/**
* Internationalization.
* @return void
*/
if ( ! function_exists( 'prmbr_plugins_loaded' ) ) {
	function prmbr_plugins_loaded() {
		load_plugin_textdomain( 'promobar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/**
* Initialize plugin.
* @return void
*/
if ( ! function_exists( 'prmbr_init' ) ) {
	function prmbr_init() {
		global $prmbr_plugin_info;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $prmbr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$prmbr_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $prmbr_plugin_info, '3.9' );

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "promobar.php" == $_GET['page'] ) ) {
			prmbr_default_options();
		}
	}
}

/**
* Admin interface init.
* @return void
*/
if ( ! function_exists( 'prmbr_admin_init' ) ) {
	function prmbr_admin_init() {
		global $bws_plugin_info, $prmbr_plugin_info, $bws_shortcode_list;
		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '196', 'version' => $prmbr_plugin_info["Version"] );
		}

		/* add PromoBar to global $bws_shortcode_list */
		$bws_shortcode_list['prmbr'] = array( 'name' => 'PromoBar' );
	}
}

if ( ! function_exists ( 'prmbr_default_options' ) ) {
	function prmbr_default_options() {
		global $prmbr_options, $prmbr_plugin_info, $prmbr_default_options;

		/* default values */
		$prmbr_default_options = array(
			'view'						=> 'all_pages',
			'position_desktop'			=> 'top',
			'position_tablet'			=> 'top',
			'position_mobile'			=> 'top',
			/* settings positions */
			/* desktop */
			'width_left_desktop'		=> '10',
			'unit_left_desktop'			=> '%',
			'width_right_desktop'		=> '10',
			'unit_right_desktop'		=> '%',
			/* tablet */
			'width_left_tablet'			=> '10',
			'unit_left_tablet'			=> '%',
			'width_right_tablet'		=> '10',
			'unit_right_tablet'			=> '%',
			/* mobile */
			'width_left_mobile'			=> '10',
			'unit_left_mobile'			=> '%',
			'width_right_mobile'		=> '10',
			'unit_right_mobile'			=> '%',

			'background_color_field'	=> '#c4e9ff',
			'text_color_field'			=> '#4c4c4c',
			'html'						=> '',
			'plugin_option_version'		=> $prmbr_plugin_info["Version"],
			'first_install'				=>	strtotime( "now" ),
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1
		);
		/* install the option defaults */
		if ( ! get_option( 'prmbr_options' ) ) {
			add_option( 'prmbr_options', $prmbr_default_options );
		}

		$prmbr_options = get_option( 'prmbr_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $prmbr_options['plugin_option_version'] ) || $prmbr_options['plugin_option_version'] != $prmbr_plugin_info["Version"] ) {

			prmbr_plugin_activate();

			/**
			 * @deprecated since 1.1.4
			 * @todo remove after 01.07.2018
			 */
			prmbr_old_options();
			/* end deprecated */

			/* show pro features */
			$prmbr_options['hide_premium_options'] = array();

			$prmbr_options = array_merge( $prmbr_default_options, $prmbr_options );
			$prmbr_options['plugin_option_version'] = $prmbr_plugin_info["Version"];
			update_option( 'prmbr_options', $prmbr_options );
		}
	}
}

/**
 * Activation plugin function
 */
if ( ! function_exists( 'prmbr_plugin_activate' ) ) {
	function prmbr_plugin_activate() {
		/* Activation function for network, check if it is a network activation - if so, run the activation function for each blog id */
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'prmbr_plugin_uninstall' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'prmbr_plugin_uninstall' );
		}
	}
}

/**
 * Settings page.
 * @return void
 */
if ( ! function_exists ( 'prmbr_settings_page' ) ) {
	function prmbr_settings_page() {
		global $prmbr_options, $prmbr_default_options, $prmbr_plugin_info, $wp_version;
		$message = $error = "";
		$plugin_basename = plugin_basename( __FILE__ );

		/* Checking data before writing to the database */
		if ( isset( $_POST['prmbr_save'] ) && check_admin_referer( $plugin_basename, 'prmbr_nonce_name' ) ) {

			$prmbr_options['view'] = (
				isset( $_POST['prmbr_view'] ) &&
				in_array( $_POST['prmbr_view'], array( 'all_pages', 'homepage', 'shortcode_or_function_for_view' ) )
			) ? $_POST['prmbr_view'] : 'all_pages';

			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $position ) {
				$prmbr_options['position_' . $position] = (
					isset( $_POST['prmbr_position_' . $position] ) &&
					in_array( $_POST['prmbr_position_' . $position], array( 'top', 'bottom', 'right', 'left', 'none' ) )
				) ? $_POST['prmbr_position_' . $position] : 'top';

				/* Check the filling of the width units field. Add width fields */
				$prmbr_options['unit_left_' . $position] = ( 'px' == $_POST['prmbr_unit_left_' . $position] ) ? 'px' : '%';
				if ( isset( $_POST['prmbr_width_left_' . $position] ) ) {
					if ( 'px' == $prmbr_options['unit_left_' . $position] ) {
						$prmbr_options['width_left_' . $position] = absint( $_POST['prmbr_width_left_' . $position] );
					} else {
						$prmbr_options['width_left_' . $position] = absint ( $_POST['prmbr_width_left_' . $position] ) < 100 ? absint( $_POST['prmbr_width_left_' . $position] ) : 100;
					}
				}

				$prmbr_options['unit_right_' . $position] = ( 'px' == $_POST['prmbr_unit_right_' . $position] ) ? 'px' : '%';
				if ( isset( $_POST['prmbr_width_right_' . $position] ) ) {
					if ( 'px' == $prmbr_options['unit_right_' . $position] ) {
						$prmbr_options['width_right_' . $position] = absint( $_POST['prmbr_width_right_' . $position] );
					} else {
						$prmbr_options['width_right_' . $position] = absint( $_POST['prmbr_width_right_' . $position] ) < 100 ? absint( $_POST['prmbr_width_right_' . $position] ) : 100;
					}
				}
			}

			/* Checking on the validity of the data */
			if ( isset( $_POST['prmbr_background_color_field'] ) ) {
				$prmbr_background_color = htmlspecialchars( $_POST['prmbr_background_color_field'] );
				if ( preg_match( '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i', $prmbr_background_color ) ) {
					$prmbr_options['background_color_field'] = $prmbr_background_color;
				} else {
					$error .= '&nbsp;' . __( 'Please select the correct value in the Background field.', 'promobar' );
				}
			}
			/* Checking on the validity of the data */
			if ( isset( $_POST['prmbr_text_color_field'] ) ) {
				$prmbr_text_color = htmlspecialchars( $_POST['prmbr_text_color_field'] );
				if ( preg_match( '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i', $prmbr_text_color ) ) {
					$prmbr_options['text_color_field'] = $prmbr_text_color;
				} else {
					$error .= '&nbsp;' . __( 'Please select the correct value in the Text Color field.', 'promobar' );
				}
			}
			/* Html clean before the show */
			if ( isset( $_POST['prmbr_html'] ) ) {
				$prmbr_options['html'] = stripslashes( $_POST['prmbr_html'] );
			}
			update_option( 'prmbr_options', $prmbr_options );
			$message = __( 'Settings saved.', 'promobar' );
		}

		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$prmbr_options = $prmbr_default_options;
			update_option( 'prmbr_options', $prmbr_options );
			$message = __( 'All plugin settings were restored.', 'promobar' );
		}

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'prmbr_options' );
			if ( ! empty( $go_pro_result['error'] ) ) {
				$error = $go_pro_result['error'];
			} elseif ( ! empty( $go_pro_result['message'] ) ) {
				$message = $go_pro_result['message'];
			}
		}
		?>
		<div class="wrap">
			<h1>PromoBar <?php _e( 'Settings', 'promobar' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( ! isset( $_GET['action'] ) ) { echo ' nav-tab-active'; } ?>" href="admin.php?page=promobar.php"> <?php _e( 'Settings', 'promobar' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) { echo 'nav-tab-active'; } ?>" href="admin.php?page=promobar.php&amp;action=extra"><?php _e( 'Extra Settings', 'promobar' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) { echo ' nav-tab-active'; } ?>" href="admin.php?page=promobar.php&amp;action=custom_code"><?php _e( 'Custom code', 'promobar' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) { echo ' nav-tab-active'; } ?>" href="admin.php?page=promobar.php&amp;action=go_pro"><?php _e( 'Go PRO', 'promobar' ); ?></a>
			</h2>
			<?php bws_show_settings_notice(); ?>
			<div class="updated fade below-h2" <?php if ( empty( $message ) || ! empty( $error ) ) { echo "style=\"display:none\""; } ?>>
				<p><strong><?php echo $message; ?></strong></p>
			</div>
			<div class="error below-h2" <?php if ( empty( $error ) ) { echo "style=\"display:none\""; } ?>>
				<p><strong><?php echo $error; ?></strong></p>
			</div>
			<?php if ( ! isset( $_GET['action'] ) ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { ?>
					<p><?php _e( 'If you would like to use this plugin on certain pages, please paste the following strings into the template source code', 'promobar' ); ?>: <span class="bws_code">&nbsp;&#60;?php do_action( 'prmbr_box' ); ?&#62;&nbsp;</span></p>
					<div>
						<?php printf( __( "If you would like to add PromoBar to your page or post, please use %s button", 'promobar' ),
							'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>'
						);
						echo bws_add_help_box( sprintf(
							__( "You can add PromoBar to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s.", 'promobar' ),
							'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>',
							'<code>[prmbr_shortcode]</code>'
						) ); ?>
					</div>
					<form method="post" action="admin.php?page=promobar.php" name="prmbr_exceptions" class="bws_form">
						<table class="form-table">
							<tr>
								<th scope="row"><?php _e( 'Display PromoBar', 'promobar' ); ?></th>
								<td>
									<fieldset>
										<label for="prmbr_all_pages">
											<input type="radio" id="prmbr_all_pages" name="prmbr_view" value="all_pages" <?php checked( 'all_pages' == $prmbr_options['view'] ); ?> /> <?php _e( 'on all pages', 'promobar' ) ?>
										</label>
										<br />
										<label for="prmbr_homepage">
											<input type="radio" id="prmbr_homepage" name="prmbr_view" value="homepage" <?php checked( 'homepage' == $prmbr_options['view'] ); ?> /> <?php _e( 'on the homepage', 'promobar' ) ?>
										</label>
										<br />
										<label for="shortcode_or_function_for_view">
											<input type="radio" id="shortcode_or_function_for_view" name="prmbr_view" value="shortcode_or_function_for_view" <?php checked( 'shortcode_or_function_for_view' == $prmbr_options['view'] ); ?> /> <?php _e( 'display via shortcode or function only', 'promobar' ); ?>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Position', 'promobar' ); ?></th>
								<td>
									<?php foreach ( array( 'desktop' => __( 'Desktop', 'promobar' ), 'tablet' => __( 'Tablet', 'promobar' ), 'mobile' => __( 'Mobile', 'promobar' ) ) as $position => $position_name ) { ?>
										<div class="prmbr_position_column">
											<p><strong><?php echo $position_name; ?></strong></p>
											<br>
											<fieldset class="prmbr_position_cell">
												<label>
													<input type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="top" <?php checked( 'top' == $prmbr_options['position_' . $position] ); ?> /> <?php _e( 'Top', 'promobar' ); ?>
												</label>
												<label>
													<input type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="bottom" <?php checked( 'bottom' == $prmbr_options['position_' . $position] ); ?> /> <?php _e( 'Bottom', 'promobar' ); ?>
												</label>
												<label>
													<input type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_left_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="left" <?php checked( 'left' == $prmbr_options['position_' . $position] ); ?> /> <?php _e( 'Left', 'promobar' ); ?>
													<div class="prmbr_left_options_<?php echo $position; ?> prmbr_emerging_options">
														<span class="bws_info">
															<?php _e( 'width', 'promobar' ); ?>
														</span>
														<input id="width_left_<?php echo $position; ?>" type="number" min="1" class="small-text" name="prmbr_width_left_<?php echo $position; ?>" value="<?php echo $prmbr_options['width_left_' . $position]; ?>" />
														<select name="prmbr_unit_left_<?php echo $position; ?>">
															<option value="px" <?php if ( 'px' == $prmbr_options['unit_left_' . $position] ) echo 'selected'; ?>><?php _e( 'px', 'promobar' ); ?></option>
															<option value="%" <?php if ( '%' == $prmbr_options['unit_left_' . $position] ) echo 'selected'; ?>>%</option>
														</select>
													</div>
												</label>
												<label>
													<input type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_right_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="right" <?php checked( 'right' == $prmbr_options['position_' . $position] ); ?> /> <?php _e( 'Right', 'promobar' ); ?>
													<div class="prmbr_right_options_<?php echo $position; ?> prmbr_emerging_options">
														<span class="bws_info">
															<?php _e( 'width', 'promobar' ); ?>
														</span>
														<input id="width_right_<?php echo $position; ?>" type="number" min="1" class="small-text" name="prmbr_width_right_<?php echo $position; ?>" value="<?php echo $prmbr_options['width_right_' . $position]; ?>" />
														<select name="prmbr_unit_right_<?php echo $position; ?>">
															<option value="px" <?php if ( 'px' == $prmbr_options['unit_right_' . $position] ) echo 'selected'; ?>><?php _e( 'px', 'promobar' ); ?></option>
															<option value="%" <?php if ( '%' == $prmbr_options['unit_right_' . $position] ) echo 'selected'; ?>>%</option>
														</select>
													</div>
												</label>
												<label>
													<input type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="none" <?php checked( 'none' == $prmbr_options['position_' . $position] ); ?> /> <?php _e( 'None', 'promobar' ); ?>
												</label>
											</fieldset>
										</div>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Background', 'promobar' ); ?></th>
								<td>
									<label for="prmbr_background_color_field">
										<input type="text" id="prmbr_background_color_field" value="<?php echo $prmbr_options['background_color_field']; ?>" name="prmbr_background_color_field" class="prmbr_color_field" data-default-color="<?php echo $prmbr_default_options['background_color_field']; ?>" />
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Text Color', 'promobar' ); ?></th>
								<td>
									<label for="prmbr_text_color_field">
										<input type="text" id="prmbr_text_color_field" value="<?php echo $prmbr_options['text_color_field']; ?>" name="prmbr_text_color_field" class="prmbr_color_field" data-default-color="<?php echo $prmbr_default_options['text_color_field']; ?>" />
									</label>
									</td>
								</tr>
								<tr>
								<th scope="row"><?php _e( 'HTML', 'promobar' ); ?></th>
								<td class="prmbr_give_notice">
									<?php wp_editor( $prmbr_options['html'], "prmbr_html", array(
										'teeny'			=> true,
										'media_buttons' => true,
										'textarea_rows' => 5,
										'textarea_name' => 'prmbr_html',
										'quicktags' 	=> true
									)); ?>
								</td>
							</tr>
						</table>
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" name="prmbr_save" value="<?php _e( 'Save Changes', 'promobar' ); ?>" />
							<?php wp_nonce_field( $plugin_basename, 'prmbr_nonce_name' ); ?>
						</p>
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename );
				}
			} elseif ( 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr valign="top">
								<td colspan="2">
									<p><?php _e( 'Please choose the necessary post types (or single pages) where you would like to display PromoBar', 'promobar' ); ?>: </p>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" type="checkbox" name="prmbr_jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'promobar' );?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'promobar' ); ?>" title="<?php _e( "Example of the site's pages tree", 'promobar' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'promobar' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2">
									* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'promobar' ); ?>
								</th>
							</tr>
						</table>
					</div>
					<div class="bws_pro_version_tooltip">
						<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/promobar/?k=d765697418cb3510ea536e47c1e26396&amp;pn=196&amp;v=<?php echo $prmbr_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="PromoBar Pro"><?php _e( 'Learn More', 'promobar' ); ?></a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab_show( false, $prmbr_plugin_info, $plugin_basename, 'promobar.php', 'promobar-pro.php', 'promobar-pro/promobar-pro.php', 'promobar', 'd765697418cb3510ea536e47c1e26396', '196', isset( $go_pro_result['pro_plugin_is_activated'] ) );
			} else {
				bws_custom_code_tab();
			}
			bws_plugin_reviews_block( $prmbr_plugin_info['Name'], 'promobar' ); ?>
		</div><!-- .wrap -->
	<?php }
}

/**
* Show PromoBar block when "Display PromoBar" in settings is "on all pages", "on the homepage"
*/
if ( ! function_exists ( 'add_prmbr_function' ) ) {
	function add_prmbr_function() {
		global $prmbr_options;

		/* Check the appropriate conditions for the show PromoBar block */
		if ( 'all_pages' == $prmbr_options['view'] || ( 'homepage' == $prmbr_options['view'] && ( is_home() || is_front_page() ) ) ) {

			if ( ! empty( $prmbr_options['html'] ) ) {
				/**
				 * Check which positions are set for resolutions. If positions are 'top', 'left' or 'right', then recording in the data-attributes.
				 * Positions left and right are saved as 'side'.
				 */
				$data_attr = '';
				$positions = array( 'desktop', 'tablet', 'mobile' );
				foreach ( $positions as $position ) {
					if ( 'left' == $prmbr_options['position_' . $position] || 'right' == $prmbr_options['position_' . $position] ) {
						$data_attr .= ' data-prmbr-position_' . $position . '="side"';
					} elseif ( 'top' == $prmbr_options['position_' . $position] ) {
						$data_attr .= ' data-prmbr-position_' . $position . '="' . $prmbr_options['position_' . $position] . '"';
					}
				}

				/* Getting a theme name for adding styles to a specific theme */
				$name_theme = get_stylesheet(); ?>

				<!-- Create the stylesheet and promobar-block to display in the frontend -->
				<style type="text/css">
					.prmbr_main {
						color: <?php echo $prmbr_options['text_color_field']; ?>;
						background: <?php echo $prmbr_options['background_color_field']; ?>;
						<?php if ( 'twentytwelve' == $name_theme || 'twentyfourteen' == $name_theme ) {
							echo 'box-sizing: border-box;';
						} ?>
					}
					@media screen and (min-width: 769px) {
						<?php if( 'left' == $prmbr_options['position_desktop'] ) {
							echo 'body { margin-left: ' . prmbr_checking_indentation( 'desktop' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_desktop'] ) {
							echo 'body { margin-right: ' . prmbr_checking_indentation( 'desktop' ) . ' }';
						} ?>
						.prmbr_main {
							<?php echo prmbr_definition_position( $prmbr_options['position_desktop'] );
							if ( 'left' == $prmbr_options['position_desktop'] || 'right' == $prmbr_options['position_desktop'] ) {
								echo ' width: ' . prmbr_checking_indentation( 'desktop' );
							} ?>
						}
					}
					@media screen and (max-width: 768px) and (min-width: 426px) {
						<?php if( 'left' == $prmbr_options['position_tablet'] ) {
							echo 'body { margin-left: ' . prmbr_checking_indentation( 'tablet' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_tablet'] ) {
							echo 'body { margin-right: ' . prmbr_checking_indentation( 'tablet' ) . ' }';
						} ?>
						.prmbr_main {
							<?php echo prmbr_definition_position( $prmbr_options['position_tablet'] );
							if ( 'left' == $prmbr_options['position_tablet'] || 'right' == $prmbr_options['position_tablet'] ) {
								echo ' width: ' . prmbr_checking_indentation( 'tablet' );
							} ?>
						}
					}
					@media screen and (max-width: 425px) {
						<?php if( 'left' == $prmbr_options['position_mobile'] ) {
							echo 'body { margin-left: ' . prmbr_checking_indentation( 'mobile' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_mobile'] ) {
							echo 'body { margin-right: ' . prmbr_checking_indentation( 'mobile' ) . ' }';
						} ?>
						.prmbr_main {
							<?php echo prmbr_definition_position( $prmbr_options['position_mobile'] );
							if ( 'left' == $prmbr_options['position_mobile'] || 'right' == $prmbr_options['position_mobile'] ) {
								echo ' width: ' . prmbr_checking_indentation( 'mobile' );
							} ?>
						}
					}
					<?php /* Adding styles for the sidebar for correct work Theme 2015 */
					if ( 'twentyfifteen' == $name_theme ) {
						echo '.sidebar { position: relative !important; top: 0 !important; } 
						@media screen and (min-width: 59.6875em) { .site { max-width: none; } }';
					}
					if ( 'twentyfourteen' == $name_theme ) {
						echo '@media screen and (min-width: 783px) { .admin-bar.masthead-fixed .site-header { top: 0; position: relative; } 
						.masthead-fixed .site-main { margin-top: 0; } }';
					} ?>
				</style>
				<div class="prmbr_main prmbr_no_js" <?php echo $data_attr; ?>>
					<?php echo prmbr_content(); ?>
				</div>
				<?php prmbr_scripts();
			}
		}
	}
}

/* The function checking whether the position is selected on the left or on the right. Adds the selected size if necessary. */
if ( ! function_exists ( 'prmbr_checking_indentation' ) ) {
	function prmbr_checking_indentation( $position ) {
		global $prmbr_options;
		$width = '';
		if ( 'left' == $prmbr_options['position_' . $position] ) {
			$width = $prmbr_options['width_left_' . $position] . $prmbr_options['unit_left_' . $position];
		} elseif ( 'right' == $prmbr_options['position_' . $position] ) {
			$width = $prmbr_options['width_right_' . $position] . $prmbr_options['unit_right_' . $position];
		}
		return $width;
	}
}

/**
 * The function allows you to determine the position selected by the user at a specific screen resolution.
 */
if ( ! function_exists ( 'prmbr_definition_position' ) ) {
	function prmbr_definition_position( $position ) {

		$bar_location = '';
		switch ( $position ) {
			case 'left':
				/* add when position left*/
				$bar_location = "top: 0; left: 0; float: left; min-height: 100%; overflow: auto;";
				break;
			case 'right':
				/* add when position right */
				$bar_location = "top: 0; min-height: 100%; overflow: auto; right: 0;";
				break;
			case 'top':
				/* add when position top */
				$bar_location = "top: 0; left: 0; width: 100%; height: auto;";
				break;
			case 'bottom':
				/* add when position bottom */
				$bar_location = "width: 100%; left: 0; margin-top: 0 !important";
				break;
			case 'none':
				/* add when position is none */
				$bar_location = 'display: none;';
				break;
		}
		return $bar_location;
	}
}

/**
* Function for creating a block that can be inserted using a shortcode or into the template code.
*/
if ( ! function_exists ( 'prmbr_block' ) ) {
	function prmbr_block() {
		global $prmbr_options; ?>
		<style type="text/css">
			.prmbr_block_shortcode {
				position: relative;
				padding: 10px;
				color: <?php echo $prmbr_options['text_color_field']; ?>;
				background: <?php echo $prmbr_options['background_color_field']; ?>;
			}
		</style>
		<div class="prmbr_block_shortcode">
			<?php echo prmbr_content() ?>
			<div class="clear"></div>
		</div>
	<?php }
}

/* Function allows you add shortcode content */
if ( ! function_exists( 'prmbr_shortcode_button_content' ) ) {
	function prmbr_shortcode_button_content( $content ) { ?>
		<div id="prmbr" style="display:none;">
			<fieldset>
				<?php _e( 'Insert the shortcode to add PromoBar block.', 'promobar' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[prmbr_shortcode]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/**
* Function allows you to set block when you insert shortcode.
* @return $html
*/
if ( ! function_exists ( 'prmbr_content' ) ) {
	function prmbr_content() {
		global $prmbr_options;
		$html = $prmbr_options['html'];
		/* insteed 'the_content' filter we use its functions to compability with Multilanguage and social buttons */
		/* Hack to get the [embed] shortcode to run before wpautop() */
		require_once( ABSPATH . WPINC . '/class-wp-embed.php' );
		$wp_embed = new WP_Embed();
		$html = $wp_embed->run_shortcode( $html );
		$html = $wp_embed->autoembed( $html );

		$html = wptexturize( $html );
		$html = convert_smilies( $html );
		$html = wpautop( $html );
		$html = shortcode_unautop( $html );
		if ( function_exists( 'wp_make_content_images_responsive' ) ) {
			$html = wp_make_content_images_responsive( $html );
		}

		$html = do_shortcode( $html ); /* AFTER wpautop() */
		$html = str_replace( ']]>', ']]&gt;', $html );
		return $html;
	}
}

/**
* Style and script for admin page.
* @return void
*/
if ( ! function_exists ( 'prmbr_enqueue_admin_part' ) ) {
	function prmbr_enqueue_admin_part() {
		if ( isset( $_GET['page'] ) && 'promobar.php' == $_GET['page'] ) {
			wp_enqueue_script( 'prmbr_color_picker', plugins_url( 'js/admin_script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ) );
			wp_enqueue_style( 'prmbr_style', plugins_url( 'css/style.css', __FILE__ ), array( 'wp-color-picker' ) );

			bws_enqueue_settings_scripts();
			if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) {
				bws_plugins_include_codemirror();
			}
		}
	}
}

/**
* Style and script for frontend.
* @return void
*/
if ( ! function_exists ( 'prmbr_scripts' ) ) {
	function prmbr_scripts() {
		/* Add style */
		wp_enqueue_style( 'prmbr_styles', plugins_url( 'css/frontend_style.css', __FILE__ ) );
		/* Add scripts */
		wp_enqueue_script( 'prmbr_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
	}
}

/**
* Register plugin links function.
* @return $links array().
*/
if ( ! function_exists( 'prmbr_register_plugin_links' ) ) {
	function prmbr_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="admin.php?page=promobar.php">' . __( 'Settings', 'promobar' ) . '</a>';
			}
			$links[]	=	'<a href="https://support.bestwebsoft.com/hc/en-us/sections/200935775" target="_blank">' . __( 'FAQ', 'promobar' ) . '</a>';
			$links[]	=	'<a href="https://support.bestwebsoft.com">' . __( 'Support', 'promobar' ) . '</a>';
		}
		return $links;
	}
}

/* add help tab */
if ( ! function_exists( 'prmbr_add_tabs' ) ) {
	function prmbr_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'prmbr',
			'section' 		=> '200935775',
		);
		bws_help_tab( $screen, $args );
	}
}

/**
* Action plugin links function.
* @param $links string
* @param $file string
* @return list of links
*/
if ( ! function_exists( 'prmbr_plugin_action_links' ) ) {
	function prmbr_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=promobar.php">' . __( 'Settings', 'promobar' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists ( 'prmbr_plugin_banner' ) ) {
	function prmbr_plugin_banner() {
		global $hook_suffix, $prmbr_plugin_info, $prmbr_options;
		if ( 'plugins.php' == $hook_suffix ) {
			if ( empty( $prmbr_options ) ) {
				$prmbr_options = get_option( 'prmbr_options' );
			}
			if ( isset( $prmbr_options['first_install'] ) && strtotime( '-1 week' ) > $prmbr_options['first_install'] ) {
				bws_plugin_banner( $prmbr_plugin_info, 'prmbr', 'promobar', 'e5cf3af473cbbd5e21b53f512bac8570', '196', '//ps.w.org/promobar/assets/icon-128x128.png' );
			}

			bws_plugin_banner_to_settings( $prmbr_plugin_info, 'prmbr_options', 'promobar', 'admin.php?page=promobar.php' );
		}
		if ( isset( $_GET['page'] ) && 'promobar.php' == $_GET['page'] ) {
			bws_plugin_suggest_feature_banner( $prmbr_plugin_info, 'prmbr_options', 'promobar' );
		}
	}
}

/**
* Uninstall the PromoBar.
* @return void
*/
/* Uninstall function. */
if ( ! function_exists( 'prmbr_plugin_uninstall' ) ) {
	function prmbr_plugin_uninstall() {
		global $wpdb;

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'promobar-pro/promobar-pro.php', $all_plugins ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				$old_blog = $wpdb->blogid;
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					delete_option( 'prmbr_options' );
				}
				switch_to_blog( $old_blog );
			} else {
				delete_option( 'prmbr_options' );
			}
		}
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

register_activation_hook( __FILE__, 'prmbr_plugin_activate' );
/* Activate PromoBar settings page in admin menu. */
add_action( 'admin_menu', 'add_prmbr_admin_menu' );
/* Initialize plugin. */
add_action( 'plugins_loaded', 'prmbr_plugins_loaded' );
add_action( 'init', 'prmbr_init' );
add_action( 'admin_init', 'prmbr_admin_init' );
/* Add PromoBar on site */
add_action( 'wp_footer', 'add_prmbr_function' );
/* Add PromoBar by using shortcode */
add_shortcode( 'prmbr_shortcode', 'prmbr_block' );
add_filter( 'widget_text', 'do_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'prmbr_shortcode_button_content' );
/* Add PromoBar by using spesial function do_action('prmbr_box'); */
add_action( 'prmbr_box', 'prmbr_block' );
add_action( 'admin_enqueue_scripts', 'prmbr_enqueue_admin_part' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'prmbr_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'prmbr_register_plugin_links', 10, 2 );
add_action( 'admin_notices', 'prmbr_plugin_banner' );