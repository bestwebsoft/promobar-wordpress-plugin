<?php
/**
 * Displays the content on the plugin settings page
 */


if ( ! class_exists( 'Prmbr_Settings_Tabs' ) ) {
	class Prmbr_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $prmbr_options, $prmbr_plugin_info;


			$tabs = array(
				'settings' 				    => array( 'label' => __( 'Settings', 'promobar' ) ),
				'display'				    => array( 'label' => __( 'Display', 'promobar' ), 'is_pro' => 1 ),
				'misc' 					    => array( 'label' => __( 'Misc', 'promobar' ) ),
				'custom_code' 			    => array( 'label' => __( 'Custom Code', 'promobar' ) ),
				'license' 				    => array( 'label' => __( 'License key', 'promobar' ) )
			);

			parent::__construct( array(
				'plugin_basename' 			=> $plugin_basename,
				'plugins_info'				=> $prmbr_plugin_info,
				'prefix' 					=> 'prmbr',
				'default_options' 			=> prmbr_default_options(),
				'options' 					=> $prmbr_options,
				'tabs' 						=> $tabs,
				'wp_slug'					=> 'promobar',
				'link_key'					=> 'd765697418cb3510ea536e47c1e26396',
				'link_pn'					=> '196',
				'doc_link'			        => 'https://bestwebsoft.com/documentation/promobar/promobar-user-guide/',

			) );
			
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			
			$this->background = array(
				'transparent'		        => __( 'Transparent', 'promobar' ),
				'color'				        => __( 'Color', 'promobar' ),
				'image'				        => __( 'Image', 'promobar' )
			);
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$message = $notice = $error = '';

			$this->options['enable'] = isset( $_POST['prmbr_enable'] ) ? 1 : 0;

			$this->options['view'] = (
				isset( $_POST['prmbr_view'] ) &&
				in_array( $_POST['prmbr_view'], array( 'all_pages', 'homepage', 'shortcode_or_function_for_view' ) )
			) ? $_POST['prmbr_view'] : 'all_pages';

            /* Show Dismiss Button */
			$this->options['dismiss_promobar'] = isset( $_POST['prmbr_show_promobar_dismiss_button'] ) ? 1 : 0;
			
			/* Position ALL */
			$this->options['position_all'] = (
				isset( $_POST['prmbr_position_all'] ) &&
				in_array( $_POST['prmbr_position_all'], array( 'absolute', 'fixed' ) )
			) ? $_POST['prmbr_position_all'] : 'absolute';

			/* Position */
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $position ) {

				if ( isset( $_POST['prmbr_position_' . $position . '_enabled'] ) ) {

					$this->options['position_' . $position] = (
						isset( $_POST['prmbr_position_' . $position] ) &&
						in_array( $_POST['prmbr_position_' . $position], array( 'top', 'bottom', 'right', 'left' ) )
					) ? $_POST['prmbr_position_' . $position] : 'top';

					/* Check the filling of the width units field. Add width fields */
					$this->options['unit_left_' . $position] = ( 'px' == $_POST['prmbr_unit_left_' . $position] ) ? 'px' : '%';
					if ( isset( $_POST['prmbr_width_left_' . $position] ) ) {
						if ( 'px' == $this->options['unit_left_' . $position] ) {
							$this->options['width_left_' . $position] = absint( $_POST['prmbr_width_left_' . $position] );
						} else {
							$this->options['width_left_' . $position] = absint ( $_POST['prmbr_width_left_' . $position] ) < 100 ? absint( $_POST['prmbr_width_left_' . $position] ) : 100;
						}
					}

					$this->options['unit_right_' . $position] = ( 'px' == $_POST['prmbr_unit_right_' . $position] ) ? 'px' : '%';
					if ( isset( $_POST['prmbr_width_right_' . $position] ) ) {
						if ( 'px' == $this->options['unit_right_' . $position] ) {
							$this->options['width_right_' . $position] = absint( $_POST['prmbr_width_right_' . $position] );
						} else {
							$this->options['width_right_' . $position] = absint( $_POST['prmbr_width_right_' . $position] ) < 100 ? absint( $_POST['prmbr_width_right_' . $position] ) : 100;
						}
					}
				} else {
					$this->options['position_' . $position] = 'none';
				}
			}

			/* Promobar Background */
			$this->options['background'] = (
                 isset( $_POST['prmbr_background'] ) &&
                 in_array( $_POST['prmbr_background'], array( 'transparent', 'color', 'image' ) )
            ) ? $_POST['prmbr_background'] : 'transparent';

			/* Promobar Background Select Color */
			if ( isset( $_POST['prmbr_background_color_field'] ) ) {
				$this->options['background_color_field'] = sanitize_hex_color( $_POST['prmbr_background_color_field'] );
			} 

			/* Promobar Background Image */
            if ( isset( $_POST['prmbr_url'] ) && 'image' == $_POST['prmbr_background'] ) {
                if ( ! empty( $_POST['prmbr_url'] ) ) {
                    $this->options['url'] = stripslashes( esc_url_raw( $_POST['prmbr_url'] ) );
                } else {
                    $this->options['url'] = '';
                }
            }

			/* Promobar Text Color */
			$this->options['text_color_field'] = sanitize_hex_color( $_POST['prmbr_text_color_field'] );

			/* Html clean before the show */
			$this->options['html'] = stripslashes( $_POST['prmbr_html'] );

			update_option( 'prmbr_options', $this->options );

            $message = __( 'Settings saved.', 'promobar' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'Promobar', 'promobar' ); ?></div>
			<table class="form-table">
				<tr>
                    <th><?php _e( 'Promobar', 'promobar' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" value="1" class="bws_option_affect" data-affect-show=".prmbr_enable" name="prmbr_enable" <?php checked( $this->options['enable'] ); ?>/>
							<span class="bws_info"><?php _e( 'Enable to display a promo bar.', 'promobar' ); ?></span>
                        </label>
                    </td>
                </tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php _e( 'Display Promobar', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_all_pages">
								<input type="radio" id="prmbr_all_pages" name="prmbr_view" value="all_pages" <?php checked( 'all_pages' == $this->options['view'] ); ?> /> <?php _e( 'on all pages', 'promobar' ) ?>
							</label>
							<br />
							<label for="prmbr_homepage">
								<input type="radio" id="prmbr_homepage" name="prmbr_view" value="homepage" <?php checked( 'homepage' == $this->options['view'] ); ?> /> <?php _e( 'on the homepage', 'promobar' ) ?>
							</label>
							<br />
							<label for="shortcode_or_function_for_view">
								<input type="radio" id="shortcode_or_function_for_view" name="prmbr_view" value="shortcode_or_function_for_view" <?php checked( 'shortcode_or_function_for_view' == $this->options['view'] ); ?> /> <?php _e( 'display via shortcode or function only', 'promobar' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th><?php _e( 'Display on', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox"  name="prmbr_position_desktop_enabled" class="prmbr_option_affect_columns" data-affect-show=".prmbr_position_column_desktop" <?php if ( $this->options['position_desktop'] !== 'none' ) echo 'checked'; ?> /> <?php _e( 'Desktop', 'promobar' ) ?>
							</label>
							<br />
							<label>
								<input type="checkbox"  name="prmbr_position_tablet_enabled" class="prmbr_option_affect_columns" data-affect-show=".prmbr_position_column_tablet" <?php if ( $this->options['position_tablet'] != 'none' ) echo 'checked'; ?> /> <?php _e( 'Tablet', 'promobar' ) ?>
							</label>
							<br />
							<label>
								<input type="checkbox"  name="prmbr_position_mobile_enabled"  class="prmbr_option_affect_columns"  data-affect-show=".prmbr_position_column_mobile" <?php if ( $this->options['position_mobile'] != 'none' ) echo 'checked'; ?> /> <?php _e( 'Mobile', 'promobar' ) ?>
							</label>
						</fieldset>						
					</td>
				</tr>
                <tr valign="top" class="prmbr_enable">
                    <th>
                    	<?php _e( 'Close Icon', 'promobar' ); ?>
                    </th>
                    <td>
                        <label for="prmbr_show_promobar_dismiss_button">
                            <input type="checkbox" value="1" name="prmbr_show_promobar_dismiss_button" id="prmbr_show_promobar_dismiss_button" <?php checked( $this->options['dismiss_promobar'] ); ?>/>
                            <span class="bws_info"><?php _e( 'Enable to display a close/dismiss icon on the promo bar.', 'promobar' ); ?></span>
                        </label>
                    </td>
                </tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php _e( 'Position', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_absolute">
								<input type="radio" id="prmbr_absolute" name="prmbr_position_all" value="absolute" <?php checked( 'absolute' == $this->options['position_all'] ); ?> /> <?php _e( 'absolute', 'promobar' ); ?>
							</label>
							<br />
							<label for="prmbr_fixed">
								<input type="radio" id="prmbr_fixed" name="prmbr_position_all" value="fixed" <?php checked( 'fixed' == $this->options['position_all'] ); ?> /> <?php _e( 'fixed', 'promobar' ); ?>
							</label>			
						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th class="prmbr_header_alignment" scope="row"><?php _e( 'Alignment', 'promobar' ); ?></th>
					<td class="prmbr_header_alignment">
						<?php foreach ( array( 'desktop' => __( 'Desktop', 'promobar' ), 'tablet' => __( 'Tablet', 'promobar' ), 'mobile' => __( 'Mobile', 'promobar' ) ) as $position => $position_name ) { ?>
							<div class="prmbr_position_column_<?php echo $position; ?> prmbr_position_column">
								<p><strong><?php echo $position_name; ?></strong></p>
								<br>
								<fieldset class="prmbr_position_cell">
									<label>
										<input  type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="top" <?php checked( 'none' == $this->options['position_' . $position] || 'top' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Top', 'promobar' ); ?>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="bottom" <?php checked( 'none' == $this->options['position_' . $position] || 'bottom' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Bottom', 'promobar' ); ?>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_left_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="left" <?php checked( 'none' == $this->options['position_' . $position] || 'left' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Left', 'promobar' ); ?>
										<div  class="prmbr_left_options_<?php echo $position; ?> prmbr_emerging_options">
											<span class="bws_info">
												<?php _e( 'width', 'promobar' ); ?>
											</span>
											<input id="width_left_<?php echo $position; ?>" type="number" min="1" class="small-text" name="prmbr_width_left_<?php echo $position; ?>" value="<?php echo $this->options['width_left_' . $position]; ?>" />
                                            <select name="prmbr_unit_left_<?php echo $position; ?>">
												<option value="px" <?php if ( 'px' == $this->options['unit_left_' . $position] ) echo 'selected'; ?>><?php _e( 'px', 'promobar' ); ?></option>
												<option value="%" <?php if ( '%' == $this->options['unit_left_' . $position] ) echo 'selected'; ?>>%</option>
											</select>
										</div>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_right_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="right" <?php checked( 'none' == $this->options['position_' . $position] || 'right' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Right', 'promobar' ); ?>
										<div  class="prmbr_right_options_<?php echo $position; ?> prmbr_emerging_options">
											<span class="bws_info">
												<?php _e( 'width', 'promobar' ); ?>
											</span>
											<input id="width_right_<?php echo $position; ?>" type="number" min="1" class="small-text" name="prmbr_width_right_<?php echo $position; ?>" value="<?php echo $this->options['width_right_' . $position]; ?>" />
											<select name="prmbr_unit_right_<?php echo $position; ?>">
												<option value="px" <?php if ( 'px' == $this->options['unit_right_' . $position] ) echo 'selected'; ?>><?php _e( 'px', 'promobar' ); ?></option>
												<option value="%" <?php if ( '%' == $this->options['unit_right_' . $position] ) echo 'selected'; ?>>%</option>
											</select>
										</div>
									</label>
								</fieldset>
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php _e( 'Background', 'promobar' ); ?></th>
					<td>
                        <fieldset>
                            <div>
                                <label for="prmbr_background_transparent">
                                    <input type="radio" name="prmbr_background" id="prmbr_background_transparent" value="transparent" class="prmbr_background_transparent" <?php checked( 'transparent' == $this->options['background'] ); ?> /> <?php _e( 'Transparent', 'promobar' ); ?>
                                </label>
                            </div>
                            <div class="background_color wrapper">
                                <label for="prmbr_background_color">
                                    <input type="radio" name="prmbr_background" id="prmbr_background_color" value="color" class="prmbr_color"<?php checked( 'color' == $this->options['background'] ); ?> /> <?php _e( 'Color', 'promobar' ); ?>
                                </label>
                                <input type="text" id="prmbr_background_color_field" value="<?php echo $this->options['background_color_field']; ?>" name="prmbr_background_color_field" class="prmbr_color_field" data-default-color="#c4e9ff" />
                            </div>
                            <?php $this->pro_block( 'prmbr_image', array( 'background' => $this->options['background'], 'url' => $this->options['url'] ) ); ?>
                        </fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php _e( 'Text Color', 'promobar' ); ?></th>
					<td>
						<label for="prmbr_text_color_field">
							<input  type="text" id="prmbr_text_color_field" value="<?php echo $this->options['text_color_field']; ?>" name="prmbr_text_color_field" class="prmbr_color_field" data-default-color="#4c4c4c" />
						</label>
                    </td>
                </tr>
                <tr class="prmbr_enable">
					<th scope="row"><?php _e( 'HTML', 'promobar' ); ?></th>
					<td class="prmbr_give_notice">
						<?php wp_editor( $this->options['html'], 'prmbr_html', array(
							'teeny'			=> true,
							'media_buttons' => true,
							'textarea_rows' => 5,
							'textarea_name' => 'prmbr_html',
							'quicktags' 	=> true
						) ); ?>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label"><?php _e( 'Countdown', 'promobar' ); ?></div>
			<?php $this->pro_block( 'prmbr_countdown_block' ); ?>
		<?php }

		/* Display bws_pro_version block by its name */
		function pro_block( $block_name = '', $args = array(), $force = false ) {
			if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) { ?>
				<div class="bws_pro_version_bloc prmbr-pro-feature">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'promobar' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php $block_name( $args ); ?>
						</div>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php }
		}

		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table">
							<tr valign="top">
								<td colspan="2">
									<p><?php _e( 'Choose the necessary post types (or single pages) where you would like to display PromoBar', 'promobar' ); ?>: </p>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" type="checkbox" name="prmbr_jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'promobar' ); ?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo plugins_url( '../images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'promobar' ); ?>" title="<?php _e( "Example of the site's pages tree", 'promobar' ); ?>" />
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
		<?php }

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Promobar Shortcode', 'promobar' ); ?>
				</h3>
				<div class="inside">
					<?php _e( 'Add PromoBar to your page or post </br> using the following shortcode:', 'promobar' ); ?>
					<?php bws_shortcode_output( '[bws_promobar]' ); ?>
				</div>
                <div class="inside prmbr_shortcode_pro">
                    <?php $this->pro_block( 'prmbr_shortcode' ); ?>
                </div>
				<div class="inside">
					<?php _e( 'If you would like to use this plugin on certain pages, please paste the following strings into the template source code:', 'promobar' ); ?>
					<br>
					<span class="bws_code">&nbsp;&#60;?php do_action( 'prmbr_box' ); ?&#62;&nbsp;</span>
				</div>
			</div>
		<?php }
	}
}
