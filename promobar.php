<?php
/*
Plugin Name: PromoBar by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/promobar/
Description: Add and display HTML advertisement on WordPress website. Customize bar styles and appearance.
Author: BestWebSoft
Text Domain: promobar
Domain Path: /languages
Version: 1.1.7
Author URI: https://bestwebsoft.com/
License: GPLv3 or later
*/

/*  @ Copyright 2020  BestWebSoft  ( https://support.bestwebsoft.com )

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

/**
* Add Wordpress page 'bws_panel' and sub-page of this plugin to admin-panel.
* @return void
*/
if ( ! function_exists( 'add_prmbr_admin_menu' ) ) {
	function add_prmbr_admin_menu() {
		if( ! is_plugin_active( 'promobar-pro/promobar-pro.php' ) ) {
            global $submenu, $prmbr_plugin_info, $wp_version;
            $settings = add_menu_page(
                __( 'PromoBar Settings', 'promobar' ),
                'PromoBar',
                'manage_options',
                'promobar.php',
                'prmbr_settings_page'
            );
            add_submenu_page(
                'promobar.php',
                __( 'Promobar', 'promobar' ),
                __( 'Settings', 'promobar' ),
                'manage_options',
                'promobar.php',
                'prmbr_settings_page'
            );
            add_submenu_page(
                'promobar.php',
                'BWS Panel',
                'BWS Panel',
                'manage_options',
                'prmbr-bws-panel',
                'bws_add_menu_render'
            );
            if ( isset( $submenu['promobar.php'] ) ) {
                $submenu['promobar.php'][] = array(
                    '<span style="color:#d86463"> ' . __( 'Upgrade to Pro', 'promobar' ) . '</span>',
                    'manage_options',
                    'https://bestwebsoft.com/products/wordpress/plugins/promobar/?k=0&pn=0&v=' . $prmbr_plugin_info["Version"] . '&wp_v=' . $wp_version );
            }
            add_action( 'load-' . $settings, 'prmbr_add_tabs' );
		}
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
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $prmbr_plugin_info, '4.5' );

		if ( ! is_admin() || ( isset( $_GET['page'] ) && "promobar.php" == $_GET['page'] ) ) {
			prmbr_settings();
		}
	}
}

/**
* Admin interface init.
* @return void
*/
if ( ! function_exists( 'prmbr_admin_init' ) ) {
	function prmbr_admin_init() {
		global $bws_plugin_info, $prmbr_plugin_info, $bws_shortcode_list, $prmbr_options , $pagenow;
		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '196', 'version' => $prmbr_plugin_info["Version"] );
		}
		if ( 'plugins.php' == $pagenow ) {
			/* Install the option defaults */
			if ( function_exists( 'bws_plugin_banner_go_pro' ) ) {
				prmbr_settings();
				bws_plugin_banner( $prmbr_options, $prmbr_plugin_info, 'prmbr', 'promobar', 'e5cf3af473cbbd5e21b53f512bac8570', '196', 'promobar' );
			}
		}
		/* add PromoBar to global $bws_shortcode_list */
		$bws_shortcode_list['prmbr'] = array( 'name' => 'PromoBar' );
	}
}

if ( ! function_exists( 'prmbr_settings' ) ) {
	function prmbr_settings() {
		global $prmbr_options, $prmbr_plugin_info;

		/* Install the option defaults */
		if ( ! get_option( 'prmbr_options' ) ) {
			$options_default = prmbr_default_options();
			add_option( 'prmbr_options', $options_default );
		}

		/* Get options from the database */
		$prmbr_options = get_option( 'prmbr_options' );

		if ( ! isset( $prmbr_options['plugin_option_version'] ) ||
            $prmbr_options['plugin_option_version'] != $prmbr_plugin_info["Version"]
        ) {
			prmbr_plugin_activate();
			
			$options_default = prmbr_default_options();			
			$prmbr_options = array_merge( $options_default, $prmbr_options );

			/* show pro features */
			$prmbr_options['hide_premium_options'] = array();
			$prmbr_options['plugin_option_version'] = $prmbr_plugin_info["Version"];

			update_option( 'prmbr_options', $prmbr_options );
		}		
	}
}

if ( ! function_exists ( 'prmbr_default_options' ) ) {
	function prmbr_default_options() {
		global $prmbr_options, $prmbr_plugin_info, $prmbr_default_options;

		/* default values */
		$prmbr_default_options = array(
			'plugin_option_version'		=> $prmbr_plugin_info["Version"],
			'first_install'				=>	strtotime( "now" ),
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,

			'enable'					=> 1,
			'view'						=> 'all_pages',			
            'dismiss_promobar'          => 0,
			/* settings all_positions */
			'position_all'				=> 'absolute',
			/* settings positions */
			'position_desktop'			=> 'top',
			'position_tablet'			=> 'top',
			'position_mobile'			=> 'top',
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
			/* background promobar*/
			'background'	            => 'transparent',
			'background_color_field'	=> '#c4e9ff',
			/* text color promobar */
			'text_color_field'			=> '#4c4c4c',
			'url'                       => '',
			'html'						=> ''			
		);

		return $prmbr_default_options;
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
		global $prmbr_options, $prmbr_default_options, $prmbr_plugin_info;
		$message = $error = '';
		$plugin_basename = plugin_basename( __FILE__ );
		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
		if ( ! class_exists( 'Bws_Settings_Tabs' ) )
			require_once( dirname( __FILE__ )  . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-prmbr-settings.php' );
		$page = new Prmbr_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
		<div class="wrap">
			<h1>Promobar <?php _e( 'Settings', 'promobar' ); ?></h1>
            <noscript>
                <div class="error below-h2">
                    <p><strong><?php _e( 'Please, enable JavaScript in your browser.', 'promobar' ); ?></strong></p>
                </div>
            </noscript>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

/**
* Show PromoBar block when "Display PromoBar" in settings is "on all pages", "on the homepage"
*/
if ( ! function_exists ( 'add_prmbr_function' ) ) {
	function add_prmbr_function() {
		global $prmbr_options, $bstwbsftwppdtplgns_cookie_add;
		/* Check the appropriate conditions for the show PromoBar block */
		if ( 1 == $prmbr_options['enable'] ) {
			if ( ( 'all_pages' == $prmbr_options['view'] || ( 'homepage' == $prmbr_options['view'] && ( is_home() || is_front_page() ) ) ) &&
				! empty( $prmbr_options['html'] ) &&
				( 0 == $prmbr_options['dismiss_promobar'] || ! isset( $_COOKIE['prmbr_banner_block'] ) )
			) { 
				/**
				 * Check which positions are set for resolutions. If positions are 'top', 'left' or 'right', then recording in the data-attributes.
				 * Positions left and right are saved as 'side'.
				 */
				$data_attr = '';
				$positions = array( 'desktop', 'tablet', 'mobile' );
				foreach ( $positions as $position ) {
					if ( 'left' == $prmbr_options['position_' . $position] ||
						'right' == $prmbr_options['position_' . $position]
					) {
						$data_attr .= ' data-prmbr-position_' . $position . '="side"';
					} else {
						$data_attr .= ' data-prmbr-position_' . $position . '="' . $prmbr_options['position_' . $position] . '"';
					}
				}

				/* Getting a theme name for adding styles to a specific theme */
				$name_theme = get_stylesheet(); ?>

				<!-- Create the stylesheet and promobar-block to display in the frontend -->
				<style type="text/css">
					body {
						position: relative;
						<?php if ( 'twentytwelve' == $name_theme || 'twentyfourteen' == $name_theme ) {
							echo 'position: inherit;';							
						} ?>
					}
					<?php if ( 'twentytwelve' == $name_theme || 'twentyfourteen' == $name_theme ) { ?>
						@media screen and (max-width: 782px) {
							body.logged-in .prmbr_main {
								margin-top: 46px !important;
							}
						}
					<?php } ?>
					.prmbr_main {
						color: <?php echo $prmbr_options['text_color_field']; ?>;
						background: <?php echo $prmbr_options['background_color_field']; ?>;
						<?php if ( 'twentytwelve' == $name_theme || 'twentyfourteen' == $name_theme ) {
							echo 'box-sizing: border-box;';
							if ( is_user_logged_in() ) {
								echo 'margin-top: 32px;';
							}
						}
						if ( 'fixed' == $prmbr_options['position_all'] ) {
							echo 'position: fixed;';
							echo prmbr_definition_position( $prmbr_options['position_desktop'] );
						}
						if ( 'absolute' == $prmbr_options['position_all'] ) {
							if ( 'twentytwelve' == $name_theme || 'twentyfourteen' == $name_theme ) {
								echo 'position: relative;';
								echo 'margin-top: -48px;';
							} else {
								echo 'position: absolute;';
							}
							echo prmbr_definition_position( $prmbr_options['position_desktop'] );
							echo 'z-index: 99999 !important;';
						}
						if ( 'transparent' == $prmbr_options['background'] ) {
							echo 'background: transparent;';
						}
						if ( 'image' == $prmbr_options['background'] ) {
							echo 'background-image: url( ' . $prmbr_options["url"] . ' );';
						} ?>
					}

					#prmbr_close_button_main {
						<?php if ( 'bottom' == $prmbr_options['position_desktop'] ||
						'bottom' == $prmbr_options['position_tablet'] ||
						'bottom' == $prmbr_options['position_mobile'] ||
						'top' == $prmbr_options['position_desktop'] ||
						'top' == $prmbr_options['position_tablet'] ||
						'top' == $prmbr_options['position_mobile']
						) {
							echo 'top: 2px !important;';
							echo 'right: 17px !important;';
							echo 'position: absolute !important;';
						}
						if ( 'left' == $prmbr_options['position_desktop'] ||
						'right' == $prmbr_options['position_desktop']
						) {
							echo 'bottom: 13px !important;';
							echo 'float: right !important;';
							echo 'right 0px !important;';
							echo 'position: relative;';
						} ?>
					}
					@media screen and (max-width: 960px) {
						<?php if ( 'absolute' == $prmbr_options['position_all'] ) {
							echo '.prmbr_main { margin-top: 0px; }';
						} ?>
					}
					@media screen and (min-width: 782px) {
						<?php if ( is_user_logged_in() &&
						'fixed' == $prmbr_options['position_all'] &&
						( 'top' == $prmbr_options['position_desktop'] ||
							'top' == $prmbr_options['position_tablet'] ||
							'top' == $prmbr_options['position_mobile'] )
						) {
							//echo 'body.logged-in .prmbr_main';
							echo '.prmbr_main { margin-top: 32px; }';
						} ?>
					}

					@media screen and (max-width: 782px) {
						<?php if ( is_user_logged_in() &&
						'fixed' == $prmbr_options['position_all'] &&
						( 'top' == $prmbr_options['position_desktop'] ||
							'top' == $prmbr_options['position_tablet'] ||
							'top' == $prmbr_options['position_mobile'] )
						) {
							//echo 'body.logged-in .prmbr_main';
							echo '.prmbr_main { margin-top: 46px; }';
						} ?>
					}
					@media screen and (min-width: 769px) {
						<?php if ( 'left' == $prmbr_options['position_desktop'] ) {
							echo 'body { padding-left: ' . prmbr_checking_indentation( 'desktop' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_desktop'] ) {
							echo 'body { padding-right: ' . prmbr_checking_indentation( 'desktop' ) . ' }';
						} ?>
						.prmbr_main {
							<?php echo prmbr_definition_position( $prmbr_options['position_desktop'] );
							if ( 'left' == $prmbr_options['position_desktop'] || 'right' == $prmbr_options['position_desktop'] ) {
								echo ' width: ' . prmbr_checking_indentation( 'desktop' );
							} ?>
						}
					}
					@media screen and (max-width: 768px) and (min-width: 426px) {
						<?php if ( 'left' == $prmbr_options['position_tablet'] ) {
							echo 'body { padding-left: ' . prmbr_checking_indentation( 'tablet' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_tablet'] ) {
							echo 'body { padding-right: ' . prmbr_checking_indentation( 'tablet' ) . ' }';
						} ?>
						.prmbr_main {
							<?php if ( 'twentytwelve' !== $name_theme && 'twentyfourteen' !== $name_theme ) {
								echo prmbr_definition_position( $prmbr_options['position_tablet'] );
							}
							if ( 'left' == $prmbr_options['position_tablet'] || 'right' == $prmbr_options['position_tablet'] ) {
								echo ' width: ' . prmbr_checking_indentation( 'tablet' );
							} ?>
						}
					}
					@media screen and (max-width: 600px) {
						<?php echo '#wpadminbar { top: 0px !important; }';
						if ( 'top' == $prmbr_options['position_desktop'] ||
						'top' == $prmbr_options['position_tablet'] ||
						'top' == $prmbr_options['position_mobile']
						) {
							echo '.prmbr_main { margin-top: 46px; }';
							echo '#wpadminbar { position: fixed; }';
						} ?>
					}
					@media screen and (max-width: 425px) {
						<?php if ( 'left' == $prmbr_options['position_mobile'] ) {
							echo 'body { padding-left: ' . prmbr_checking_indentation( 'mobile' ) . ' }';
						} elseif ( 'right' == $prmbr_options['position_mobile'] ) {
							echo 'body { padding-right: ' . prmbr_checking_indentation( 'mobile' ) . ' }';
						} ?>
						.prmbr_main {
							<?php if ( 'twentytwelve' !== $name_theme && 'twentyfourteen' !== $name_theme ) {
								echo prmbr_definition_position( $prmbr_options['position_mobile'] );
							}
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
				<?php if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
					wp_enqueue_script( 'bws_menu_cookie', bws_menu_url( 'js/c_o_o_k_i_e.js' ) );
					$bstwbsftwppdtplgns_cookie_add = true;
				}
				if ( 1 == $prmbr_options['dismiss_promobar'] ) { 
					$script = "( function( $ ) {
						$( document ).ready( function() {
							var hide_message        = $.cookie( 'prmbr_banner_block' ),
								prmbr_main          = $( '.prmbr_main' ),
								prmbr_timer_block   = $( '.prmbr_timer_block' );
							if ( hide_message === 'true' ) {
								prmbr_main.hide();
							} else {
								prmbr_main.prepend(
									'<div><span id=\"prmbr_close_button_main\" class=\"dashicons dashicons-no-alt\"></span></div>'
								).show().css( 'display', '' );
							}
							$( '#prmbr_close_button_main' ).click( function() {
								prmbr_main.hide();
								$.cookie( 'prmbr_banner_block', 'true', { expires: 32 } );
							} );
						} );
					} )( jQuery );";
					wp_register_script( 'prmbr_dismiss_promobar', '' );
					wp_enqueue_script( 'prmbr_dismiss_promobar' );
					wp_add_inline_script( 'prmbr_dismiss_promobar', sprintf( $script ) );
				}
				if ( ! is_user_logged_in() ) { ?>
					<div class="prmbr_main prmbr_no_js" <?php echo $data_attr; ?>>
						<?php echo prmbr_content(); ?>
					</div>
				<?php } else if ( is_user_logged_in() ) { ?>
					<div class="prmbr_main prmbr_no_js prmbr_no_js_logged" <?php echo $data_attr; ?>>
						<?php echo prmbr_content(); ?>
					</div>
				<?php }
				prmbr_scripts();
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
				$bar_location = "top: 0; left: 0; width: 100%; height: auto; bottom: auto;";
				break;
			case 'bottom':
				/* add when position bottom */
				$bar_location = "width: 100%; left: 0; margin-top: 0 !important; bottom: 0; top: auto;";
				break;
			case 'none':
				/* add when position is none */
                $bar_location = "display: none;";
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
		global $prmbr_options;
		if ( 1 == $prmbr_options['enable'] ) { ?>
			<style type="text/css">
				.prmbr_block_shortcode {
					position: relative;
					padding: 10px;
					color: <?php echo $prmbr_options['text_color_field']; ?>;
	                <?php if ( 'color' == $prmbr_options['background'] ) { ?>
	                    background: <?php echo $prmbr_options['background_color_field']; ?>;
	                <?php } else { ?>
	                    background: <?php echo $prmbr_options['background']; ?>;
	                <?php } ?>
				}
			</style>
			<div class="prmbr_block_shortcode">
				<?php echo prmbr_content() ?>
				<div class="clear"></div>
			</div>
		<?php }
	}
}

/* Function allows you add shortcode content */
if ( ! function_exists( 'prmbr_shortcode_button_content' ) ) {
	function prmbr_shortcode_button_content( $content ) { ?>
		<div id="prmbr" style="display:none;">
			<fieldset>
				<?php _e( 'Add promobar to your page or post', 'promobar' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_promobar]" />
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

/* add shortcode promobar content */
if ( ! function_exists( 'prmbr_shortcode_promobar_button_content' ) ) {
	function prmbr_shortcode_promobar_button_content( $content ) { ?>
		<div id="prmbr" style="display:none;">
			<fieldset>
				<?php _e( 'Insert the shortcode to add PromoBar block.', 'promobar' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_promobar]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/**
* Style and script for admin page.
* @return void
*/
if ( ! function_exists ( 'prmbr_enqueue_admin_part' ) ) {
	function prmbr_enqueue_admin_part() {
		wp_enqueue_style( 'prmbr_icon_style', plugins_url( 'css/icon_style.css', __FILE__ ) );
		
		if ( isset( $_GET['page'] ) && 'promobar.php' == $_GET['page'] ) {
			wp_enqueue_script( 'prmbr_color_picker', plugins_url( 'js/admin_script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ) );
			wp_enqueue_style( 'prmbr_style', plugins_url( 'css/admin_style.css', __FILE__ ), array( 'wp-color-picker' ) );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui' );
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

/**
 * Style RTL and style when the user isn't logged of the dismiss button
 */
if ( ! function_exists ( 'prmbr_enqueues' ) ) {
    function prmbr_enqueues() {
        if ( is_rtl() ) {
            wp_enqueue_style( 'style-rtl', plugins_url( '/css/style-rtl.css', __FILE__ ) );
        }
		if ( ! is_user_logged_in() ) {
			wp_enqueue_style( 'dashicons' );
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
		wp_enqueue_style( 'prmbr_styles', plugins_url( 'css/style.css', __FILE__ ) );
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
			$links[] = '<a href="https://support.bestwebsoft.com/hc/en-us/sections/200935775" target="_blank">' . __( 'FAQ', 'promobar' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'promobar' ) . '</a>';
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
			'section' 		=> '200935775'
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
		global $hook_suffix, $prmbr_plugin_info;
		if ( 'plugins.php' == $hook_suffix ) {
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
add_shortcode( 'bws_promobar', 'prmbr_block' );
add_filter( 'widget_text', 'do_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'prmbr_shortcode_button_content' );
/* Add PromoBar by using spesial function do_action( 'prmbr_box' ); */
add_action( 'prmbr_box', 'prmbr_block' );
add_action( 'wp_enqueue_scripts', 'prmbr_enqueues' );
add_action( 'admin_enqueue_scripts', 'prmbr_enqueue_admin_part' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'prmbr_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'prmbr_register_plugin_links', 10, 2 );
add_action( 'admin_notices', 'prmbr_plugin_banner' );
