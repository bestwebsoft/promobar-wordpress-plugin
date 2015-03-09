<?php
/*
Plugin Name: PromoBar
Plugin URI: http://bestwebsoft.com/products/
Description: This plugin allows you to display an alert to warn its users about some changes on the site, place an advertisement or any other information.
Author: BestWebSoft
Version: 1.0.2
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  @ Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
* Add Wordpress page 'bws_plugins' and sub-page of this plugin to admin-panel.
* @return void
*/
if ( ! function_exists( 'add_prmbr_admin_menu' ) ) {
	function add_prmbr_admin_menu() {		
		bws_add_general_menu( plugin_basename( __FILE__ ) );		
		add_submenu_page( 'bws_plugins', __( 'PromoBar Settings', 'promobar' ), 'PromoBar', 'manage_options', 'promobar.php', 'prmbr_settings_page' );
	}
}

/**
* Initialize plugin.
* @return void
*/
if ( ! function_exists( 'prmbr_init' ) ) {
	function prmbr_init() {
		global $prmbr_plugin_info;
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'promobar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );
		
		if ( empty( $prmbr_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$prmbr_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_version_check( plugin_basename( __FILE__ ), $prmbr_plugin_info, "3.5" );

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "promobar.php" == $_GET['page'] ) )
			prmbr_default_options();		
	}
}

/**
* Admin interface init.
* @return void
*/
if ( ! function_exists( 'prmbr_admin_init' ) ) {
	function prmbr_admin_init() {
		global $bws_plugin_info, $prmbr_plugin_info;
		/* Add variable for bws_menu */
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '196', 'version' => $prmbr_plugin_info["Version"] );
	}
}

if ( ! function_exists ( 'prmbr_default_options' ) ) {
	function prmbr_default_options() {
		global $prmbr_options, $prmbr_plugin_info, $prmbr_default_options, $prmbr_width;

		/* default values */
		$prmbr_default_options = array(
			'view'						=> 'all_pages',
			'position'					=> 'prmbr_top',
			'width_left'				=> '10',
			'width_right'				=> '10',
			'background_color_field'	=> '#c4e9ff',
			'text_color_field'			=> '#4c4c4c',
			'html'						=> '',
			'plugin_option_version'		=> $prmbr_plugin_info["Version"],
		);
		/* install the option defaults */
		if ( ! get_option( 'prmbr_options' ) )
			add_option( 'prmbr_options', $prmbr_default_options );

		$prmbr_options = get_option( 'prmbr_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $prmbr_options['plugin_option_version'] ) || $prmbr_options['plugin_option_version'] != $prmbr_plugin_info["Version"] ) {
			$prmbr_options = array_merge( $prmbr_default_options, $prmbr_options );
			$prmbr_options['plugin_option_version'] = $prmbr_plugin_info["Version"];
			update_option( 'prmbr_options', $prmbr_options );
		}

		/* Get options from the database */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "promobar.php" == $_GET['page'] ) ) {
			/* Get/Register and check settings for plugin */
			if ( $prmbr_options['position'] == 'prmbr_left' ) {
				$prmbr_width = 'width:' . $prmbr_options['width_left'] . '%;';
			} elseif ( $prmbr_options['position'] == 'prmbr_right' ) {
				$prmbr_width = 'width:' . $prmbr_options['width_right'] . '%;';
			}
		}
	}
}

/**
 * Settings page.
 * @return void
 */
if ( ! function_exists ( 'prmbr_settings_page' ) ) {
	function prmbr_settings_page() {
		global $wpdb, $prmbr_options, $prmbr_default_options, $prmbr_plugin_info, $wp_version;
		$error = "";
		$message = __( 'Settings saved.', 'promobar' );
		$plugin_basename = plugin_basename( __FILE__ );

		/* Checking data before writing to the database */
		if ( isset( $_POST['prmbr_save'] ) && check_admin_referer( $plugin_basename, 'prmbr_nonce_name' ) ) {
			if ( isset( $_POST['prmbr_view'] ) ) {
				$prmbr_options['view'] = $_POST['prmbr_view']; 
			}
			if ( isset( $_POST['prmbr_position'] ) ) {
				$prmbr_options['position'] = $_POST['prmbr_position'];
			}
			/* add width if position is left */
			if ( isset( $_POST['prmbr_width_left'] ) ) { 
				$prmbr_check_width_left = htmlspecialchars( $_POST['prmbr_width_left'] );
				if ( preg_match( '/^([0-9]{1,3})$/i', $prmbr_check_width_left ) ) {
					$prmbr_options['width_left'] = $prmbr_check_width_left;
				} else {
					$error .= '&nbsp;' . __( 'Please enter the correct value in the Width field.', 'promobar' );
				}
			}

			/* add width if position is right */
			if ( isset( $_POST['prmbr_width_right'] ) ) {
				$prmbr_check_width_right = htmlspecialchars( $_POST['prmbr_width_right'] );
				if ( preg_match( '/^([0-9]{1,3})$/i', $prmbr_check_width_right ) ) {
					$prmbr_options['width_right'] = $prmbr_check_width_right;
				} else {
					$error .= '&nbsp;' . __( 'Please enter the correct value in the Width field.', 'promobar' );
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
		}

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {			
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
		} ?><div class="wrap">
			<h2>PromoBar <?php _e( 'Settings', 'promobar' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=promobar.php"> <?php _e( 'Settings', 'promobar' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo 'nav-tab-active'; ?>" href="admin.php?page=promobar.php&amp;action=extra"><?php _e( 'Extra Settings', 'promobar' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/promobar/faq/" target="_blank"><?php _e( 'FAQ', 'promobar' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=promobar.php&amp;action=go_pro"><?php _e( 'Go PRO', 'promobar' ); ?></a>
			</h2>
			<div id="prmbr_settings_notice" class="updated fade" style="display:none">
				<p><strong><?php _e( 'Notice', 'promobar' ); ?></strong>: <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'promobar' ); ?></p>
			</div>
			<div class="updated fade" <?php if ( ! isset( $_POST['prmbr_save'] ) || "" != $error ) echo "style=\"display:none\""; ?>>
				<p><strong><?php _e( 'Notice', 'promobar' ); ?>: </strong><?php echo $message; ?></p>
			</div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>>
				<p><strong><?php _e( 'Notice', 'promobar' ); ?>: </strong><?php echo $error; ?></p>
			</div>
			<?php if ( ! isset( $_GET['action'] ) ) { ?>
				<p><?php _e( 'If you would like to use this plugin on certain pages, please paste the following strings into the template source code', 'promobar' ); ?>: <span class="prmbr_code">&nbsp;&#60;?php do_action( 'prmbr_box' ); ?&#62;&nbsp;</span><br /><?php _e( 'And if you want to add the block to the website page or post, please paste the following shortcode', 'promobar' ); ?>: <span class="prmbr_code">&nbsp;[prmbr_shortcode]&nbsp;</span></p>
				<form method="post" action="admin.php?page=promobar.php" name="prmbr_exceptions" id="prmbr_settings_form">
					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Display PromoBar', 'promobar' ); ?></th>
							<td>
								<label for="prmbr_all_pages">
									<input type="radio" id="prmbr_all_pages" name="prmbr_view" value="all_pages" <?php if ( $prmbr_options['view'] == 'all_pages' ) echo 'checked' ?> /> <?php _e( 'on all pages', 'promobar' ); ?>
								</label>
								<br />
								<label for="prmbr_homepage">
									<input type="radio" id="prmbr_homepage" name="prmbr_view" value="homepage" <?php if ( $prmbr_options['view'] == 'homepage' ) echo 'checked' ?> /> <?php _e( 'on the homepage', 'promobar' ); ?>
								</label>
								<br />
								<label for="shortcode_or_function_for_view">
									<input type="radio" id="shortcode_or_function_for_view" name="prmbr_view" value="shortcode_or_function_for_view" <?php if ( $prmbr_options['view'] == 'shortcode_or_function_for_view' ) echo 'checked' ?> /> <?php _e( 'display via shortcode or function only', 'promobar' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Position', 'promobar' ); ?></th>
							<td>
								<label for="prmbr_position1">
									<input type="radio" id="prmbr_position1" name="prmbr_position" value="prmbr_top" <?php if ( $prmbr_options['position'] == 'prmbr_top' ) echo 'checked' ?> /> <?php _e( 'Top', 'promobar' ); ?>
								</label>
								<br />
								<label for="prmbr_position2">
									<input type="radio" id="prmbr_position2" name="prmbr_position" value="prmbr_bottom" <?php if ( $prmbr_options['position'] == 'prmbr_bottom' ) echo 'checked' ?> /> <?php _e( 'Bottom', 'promobar' ); ?>
								</label>
								<br />
								<label for="prmbr_position3">
									<input type="radio" id="prmbr_position3" name="prmbr_position" value="prmbr_left" <?php if ( $prmbr_options['position'] == 'prmbr_left' ) echo 'checked' ?> /> <?php _e( 'Left', 'promobar' ); ?>&nbsp;&nbsp;&nbsp;
								</label>
								<span class="prmbr_info">
									&nbsp;&nbsp;&nbsp;<?php _e( 'width', 'promobar' ); ?>
								</span>
								<label for="prmbr_width_position3" >
									<input type="text" id="prmbr_width_position3" class="prmbr_width <?php if ( $prmbr_options['position'] != 'prmbr_left') echo 'prmbr_width_disabled';?>" name="prmbr_width_left" value="<?php echo $prmbr_options['width_left'];?>" />%
								</label>
								<br />
								<label for="prmbr_position4">
									<input type="radio" id="prmbr_position4" name="prmbr_position" value="prmbr_right" <?php if ( $prmbr_options['position'] == 'prmbr_right' ) echo 'checked' ?> /> <?php _e( 'Right', 'promobar' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;
								</label>
								<span class="prmbr_info">
									<?php _e( 'width', 'promobar' ); ?>
								</span>
								<label for="prmbr_width_position4">
									<input type="text" id="prmbr_width_position4" class="prmbr_width <?php if ( $prmbr_options['position'] != 'prmbr_right') echo 'prmbr_width_disabled'; ?>" name="prmbr_width_right" value="<?php echo $prmbr_options['width_right']; ?>" />%
								</label>
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
									'quicktags' 	=> true,
								)); ?>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" name="prmbr_save" value="<?php _e( 'Save Changes', 'promobar' ); ?>" />
						<?php wp_nonce_field( $plugin_basename, 'prmbr_nonce_name' ); ?>
					</p>					
				</form>
				<?php bws_plugin_reviews_block( $prmbr_plugin_info['Name'], 'promobar' );
			} else if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr valign="top">
								<td colspan="2">
									<p><?php _e( 'Please choose the necessary post types (or single pages) PromoBar will be displayed with', 'promobar' ); ?>: </p>
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
						<div class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to a PRO version.', 'promobar' ); ?>
							<a href="http://bestwebsoft.com/products/promobar/?k=d765697418cb3510ea536e47c1e26396&amp;pn=196&amp;v=<?php echo $prmbr_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="PromoBar Pro"><?php _e( 'Learn More', 'promobar' ); ?></a>
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/products/promobar/buy/?k=d765697418cb3510ea536e47c1e26396&amp;pn=196&amp;v=<?php echo $prmbr_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="PromoBar Pro">
							<?php _e( 'Go', 'promobar' ); ?> <strong>PRO</strong>
						</a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } elseif ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab( $prmbr_plugin_info, $plugin_basename, 'promobar.php', 'promobar-pro.php', 'promobar-pro/promobar-pro.php', 'promobar', 'd765697418cb3510ea536e47c1e26396', '196', isset( $go_pro_result['pro_plugin_is_activated'] ) );
			} ?>
		</div><!-- .wrap -->
	<?php }
}

/**
* Show PromoBar block when "Display PromoBar" in settings is "on all pages", "on the homepage" or "on selected pages"
* @return $main_position
*/
if ( ! function_exists ( 'add_prmbr_function' ) ) {
	function add_prmbr_function() {
		global $wpdb, $prmbr_options, $post, $prmbr_width;
		
		/* Add styles in some settings where there is no JS */
		if ( ( $prmbr_options['position'] == 'prmbr_left') || ( $prmbr_options['position'] == 'prmbr_right' ) ) {
			$prmbr_options['position'] .= ' prmbr_no_js';
		}
		/* Define a variable in a block to display*/
		$main_position = '<div style="' . $prmbr_width . 'color:' . $prmbr_options['text_color_field'] . '; background:' . $prmbr_options['background_color_field'] . '" class="prmbr_main ' . $prmbr_options['position'] . '">' . $prmbr_options['html'] . '</div>'; 		
		/* Check the appropriate conditions for the show PromoBar block */
		if ( ! isset( $prmbr_options['exception']['post_type'][ $post->post_type ] ) ) {
			/* exclude categories */
			$exclude_post = false;
			if ( 'post' == $post->post_type && ! empty( $prmbr_options['exception']['categories'] ) ) {
				$post_categories = wp_get_post_categories( $post->ID );
				foreach ( $post_categories as $key => $value ) {
					if ( isset( $prmbr_options['exception']['categories'][ $value ] ) ) {
						$exclude_post = true;
						break;
					}
				}
			}
		}
		/* Check the appropriate conditions for the show PromoBar block */
		if ( ( $prmbr_options['view'] == 'all_pages' ) || ( ( $prmbr_options['view'] == 'homepage' ) && ( is_home() || is_front_page() ) ) || ( ! isset( $prmbr_options['exception']['post_type'][ $post->post_type ] ) ) && ( ! isset( $prmbr_options['exception']['post_id'][ $post->ID ] ) && $exclude_post == false ) )  {		
			echo $main_position;
		}
	}
}

/**
* Function allows you to set block when you insert shortcode.
* @return $main_position
*/
if ( ! function_exists ( 'add_prmbr_shortcode' ) ) {
	function add_prmbr_shortcode() {
		global $prmbr_options, $prmbr_width;
		$main_position = '<div style="' . $prmbr_width . 'color:' . $prmbr_options['text_color_field'] . '; background:' . $prmbr_options['background_color_field'] . '" class="prmbr_main ' . $prmbr_options['position'] . '">' . $prmbr_options['html'] . '</div>';
		return $main_position;
	}
}

/**
* Function allows you to set block when you insert function in you code.
* @return $main_position
*/
if ( ! function_exists ( 'prmbr_by_using_function' ) ) {
	function prmbr_by_using_function() {
		global $prmbr_options, $prmbr_width;
		$main_position = '<div style="' . $prmbr_width . 'color:' . $prmbr_options['text_color_field'] . '; background:' . $prmbr_options['background_color_field'] . '" class="prmbr_main ' . $prmbr_options['position'] . '">' . $prmbr_options['html'] . '</div>';
		echo $main_position;
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
		}	
	}
}

/**
* Style and script for frontend .
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
			if ( ! is_network_admin() )
				$links[]    =    '<a href="admin.php?page=promobar.php">' . __( 'Settings', 'promobar' ) . '</a>';
			$links[]    =    '<a href="http://wordpress.org/plugins/promobar/faq/" target="_blank">' . __( 'FAQ', 'promobar' ) . '</a>';
			$links[]    =    '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'promobar' ) . '</a>';
		}
		return $links;
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
			if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ){
				$settings_link = '<a href="admin.php?page=promobar.php">' . __( 'Settings', 'promobar' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists ( 'prmbr_plugin_banner' ) ) {
	function prmbr_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix ) {
			global $prmbr_plugin_info;
			bws_plugin_banner( $prmbr_plugin_info, 'prmbr', 'promobar', 'e5cf3af473cbbd5e21b53f512bac8570', '196', plugins_url( 'images/banner.png', __FILE__ ) );   
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
		delete_option( 'prmbr_options' );
	}
}

/* Activate PromoBar settings page in admin menu. */
add_action( 'admin_menu', 'add_prmbr_admin_menu' );
/* Initialize plugin. */
add_action( 'init', 'prmbr_init' );
add_action( 'admin_init', 'prmbr_admin_init' );
/* Add PromoBar on site */
add_action( 'wp_footer', 'add_prmbr_function' );
/* Add PromoBar by using shortcode */
add_shortcode( 'prmbr_shortcode', 'add_prmbr_shortcode' );
/* Add PromoBar by using spesial function do_action('prmbr_box'); */
add_action( 'prmbr_box', 'prmbr_by_using_function' ); 

add_action( 'admin_enqueue_scripts', 'prmbr_enqueue_admin_part' );
add_action( 'wp_enqueue_scripts', 'prmbr_scripts' ); 
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'prmbr_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'prmbr_register_plugin_links', 10, 2 );

add_action( 'admin_notices', 'prmbr_plugin_banner' );
/* Uninstall plugin. Drop tables, delete options. */
register_uninstall_hook( __FILE__, 'prmbr_plugin_uninstall' );