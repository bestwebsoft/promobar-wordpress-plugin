<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

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
				'custom_css_settings'	    => array( 'label' => __( 'Custom CSS Settings', 'promobar' ) ),
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
				'pro_page'					=> 'admin.php?page=promobar-pro.php',
				'bws_license_plugin'		=> 'promobar-pro/promobar-pro.php',
				'link_key'					=> 'd765697418cb3510ea536e47c1e26396',
				'link_pn'					=> '196'
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

			$this->options['view'] = (
				isset( $_POST['prmbr_view'] ) &&
				in_array( $_POST['prmbr_view'], array( 'all_pages', 'homepage', 'shortcode_or_function_for_view' ) )
			) ? $_POST['prmbr_view'] : 'all_pages';

            /* Show Dismiss Button */
            $this->options["dismiss_promobar"] = isset( $_POST['prmbr_show_promobar_dismiss_button'] ) ? 1 : 0;

			/* Position ALL */
			$this->options['position_all'] = (
				isset( $_POST['prmbr_position_all'] ) &&
				in_array( $_POST['prmbr_position_all'], array( 'absolute', 'fixed' ) )
			) ? $_POST['prmbr_position_all'] : 'absolute';

			/* Position */
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $position ) {
				$this->options['position_' . $position] = (
					isset( $_POST['prmbr_position_' . $position] ) &&
					in_array( $_POST['prmbr_position_' . $position], array( 'top', 'bottom', 'right', 'left', 'none' ) )
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
			}

			/* Promobar Background */
			$this->options['background'] = (
                 isset( $_POST['prmbr_background'] ) &&
                 in_array( $_POST['prmbr_background'], array( 'transparent', 'color', 'image' ) )
            ) ? $_POST['prmbr_background'] : 'transparent';

			/* Promobar Background Select Color */
			if ( isset( $_POST['prmbr_background_color_field'] ) && in_array( $_POST['prmbr_background'], array ('color') ) ) {
				$prmbr_background_color = htmlspecialchars( $_POST['prmbr_background_color_field'] );
				if ( preg_match( '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i', $prmbr_background_color ) ) {
					$this->options['background_color_field'] = $prmbr_background_color;
				} else {
					$error .= '&nbsp;' . __( 'Please select the correct value in the Background field.', 'promobar' );
				}
			}

			/* Promobar Background Image */
            if ( isset( $_POST['prmbr_url'] ) && in_array( $_POST['prmbr_background'], array ('image') ) ) {
                if ( ! empty( $_POST['prmbr_url'] ) ) {
                    $this->options['url'] = stripslashes( $_POST['prmbr_url'] );
                } else {
                    $this->options['url'] = '';
                }
            }

			/* Promobar Text Color */
			if ( isset( $_POST['prmbr_text_color_field'] ) ) {
				$prmbr_text_color = htmlspecialchars( $_POST['prmbr_text_color_field'] );
				if ( preg_match( '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i', $prmbr_text_color ) ) {
					$this->options['text_color_field'] = $prmbr_text_color;
				} else {
					$error .= '&nbsp;' . __( 'Please select the correct value in the Text Color field.', 'promobar' );
				}
			}
			/* Html clean before the show */
			if ( isset( $_POST['prmbr_html'] ) ) {
				$this->options['html'] = stripslashes( $_POST['prmbr_html'] );
			}

			if ( isset( $_POST['prmbr_delete_button'] ) ) {
				unset( $this->options["css"][ $_POST['prmbr_delete_button'] ] );
			} else {
				$this->options["css"] = array();
				if ( isset( $_REQUEST["prmbr_css_file_path"] ) ) {
					foreach ( $_REQUEST["prmbr_css_file_path"] as $key => $value ) {
						$date_from		= strtotime( $_REQUEST["prmbr_start_date_for_css_file"][ $key ] );
						$date_to		= strtotime( $_REQUEST["prmbr_end_date_for_css_file"][ $key ] );
						
						if ( $date_from > $date_to ) {
							$date_to = $date_from + DAY_IN_SECONDS;
						}
						$this->options["css"][ $key ] = array(
							'file_path'		=> $value,
							'start_date'	=> date( 'd-m-Y', $date_from ),
							'end_date'		=> date( 'd-m-Y', $date_to ),
							'repeat' 		=> isset( $_REQUEST["prmbr_file_repeat"][ $key ] ) ? 1 : 0
						);
					}
				}
			}

			update_option( 'prmbr_options', $this->options );

            $message = __( 'Settings saved.', 'promobar' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 *
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); 
			$change_permission_attr = ''; ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'Promobar', 'promobar' ); ?></div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Display Promobar', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_all_pages">
								<input<?php echo $change_permission_attr; ?> type="radio" id="prmbr_all_pages" name="prmbr_view" value="all_pages" <?php checked( 'all_pages' == $this->options['view'] ); ?> /> <?php _e( 'on all pages', 'promobar' ) ?>
							</label>
							<br />
							<label for="prmbr_homepage">
								<input<?php echo $change_permission_attr; ?> type="radio" id="prmbr_homepage" name="prmbr_view" value="homepage" <?php checked( 'homepage' == $this->options['view'] ); ?> /> <?php _e( 'on the homepage', 'promobar' ) ?>
							</label>
							<br />
							<label for="shortcode_or_function_for_view">
								<input<?php echo $change_permission_attr; ?> type="radio" id="shortcode_or_function_for_view" name="prmbr_view" value="shortcode_or_function_for_view" <?php checked( 'shortcode_or_function_for_view' == $this->options['view'] ); ?> /> <?php _e( 'display via shortcode or function only', 'promobar' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
                <tr valign="top">
                    <th>
                        <label for="prmbr_show_promobar_dismiss_button">
                            <?php _e( 'Show Dismiss Button', 'promobar-pro' ); ?>
                        </label>
                    </th>
                    <td>
                        <label for="prmbr_show_promobar_dismiss_button">
                            <input type="checkbox" value="1" name="prmbr_show_promobar_dismiss_button" id="prmbr_show_promobar_dismiss_button" <?php checked( $this->options['dismiss_promobar'] ); ?>/>
                            <span class="bws_info"><?php _e( 'When users close Promobar it is written to the cookies.', 'promobar-pro' ); ?></span>
                        </label>
                    </td>
                </tr>
				<tr>
					<th scope="row"><?php _e( 'Position', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_absolute">
								<input<?php echo $change_permission_attr; ?> type="radio" id="prmbr_absolute" name="prmbr_position_all" value="absolute" <?php checked( 'absolute' == $this->options['position_all'] ); ?> /> <?php _e( 'absolute', 'promobar' ); ?>
							</label>
							<br />
							<label for="prmbr_fixed">
								<input<?php echo $change_permission_attr; ?> type="radio" id="prmbr_fixed" name="prmbr_position_all" value="fixed" <?php checked( 'fixed' == $this->options['position_all'] ); ?> /> <?php _e( 'fixed', 'promobar' ); ?>
							</label>			
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Alignment', 'promobar' ); ?></th>
					<td>
						<?php foreach ( array( 'desktop' => __( 'Desktop', 'promobar' ), 'tablet' => __( 'Tablet', 'promobar' ), 'mobile' => __( 'Mobile', 'promobar' ) ) as $position => $position_name ) { ?>
							<div class="prmbr_position_column">
								<p><strong><?php echo $position_name; ?></strong></p>
								<br>
								<fieldset class="prmbr_position_cell">
									<label>
										<input <?php echo $change_permission_attr; ?> type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="top" <?php checked( 'top' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Top', 'promobar' ); ?>
									</label>
									<label>
										<input <?php echo $change_permission_attr; ?> type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="bottom" <?php checked( 'bottom' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Bottom', 'promobar' ); ?>
									</label>
									<label>
										<input <?php echo $change_permission_attr; ?> type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_left_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="left" <?php checked( 'left' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Left', 'promobar' ); ?>
										<div <?php echo $change_permission_attr; ?> class="prmbr_left_options_<?php echo $position; ?> prmbr_emerging_options">
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
										<input <?php echo $change_permission_attr; ?> type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_right_options_<?php echo $position; ?>" name="prmbr_position_<?php echo $position; ?>" value="right" <?php checked( 'right' == $this->options['position_' . $position] ); ?> /> <?php _e( 'Right', 'promobar' ); ?>
										<div <?php echo $change_permission_attr; ?> class="prmbr_right_options_<?php echo $position; ?> prmbr_emerging_options">
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
									<label>
										<input <?php echo $change_permission_attr; ?> type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo $position; ?>" value="none" <?php checked( 'none' == $this->options['position_' . $position] ); ?> /> <?php _e( 'None', 'promobar' ); ?>
									</label>
								</fieldset>
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Background', 'promobar' ); ?></th>
					<td>
                        <fieldset>
                            <div>
                                <label for="prmbr_background_transparent">
                                    <input<?php echo $change_permission_attr; ?> type="radio" name="prmbr_background" id="prmbr_background_transparent" value="transparent" class="prmbr_background_transparent" <?php checked( 'transparent' == $this->options['background'] ); ?> /> <?php _e( 'Transparent', 'promobar' ); ?>
                                </label>
                            </div>
                            <div class="background_color wrapper">
                                <label for="prmbr_background_color">
                                    <input<?php echo $change_permission_attr; ?> type="radio" name="prmbr_background" id="prmbr_background_color" value="color" class="prmbr_color"<?php checked( 'color' == $this->options['background'] ); ?> /> <?php _e( 'Color', 'promobar' ); ?>
                                </label>
                                <input<?php echo $change_permission_attr; ?> type="text" id="prmbr_background_color_field" value="<?php echo $this->options['background_color_field']; ?>" name="prmbr_background_color_field" class="prmbr_color_field" data-default-color="#c4e9ff" />
                            </div>
                            <?php $this->pro_block_image( 'prmbr_image', array( 'background' => $this->options['background'], 'url' => $this->options['url'] ) ); ?>
                        </fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Text Color', 'promobar' ); ?></th>
					<td>
						<label for="prmbr_text_color_field">
							<input <?php echo $change_permission_attr; ?> type="text" id="prmbr_text_color_field" value="<?php echo $this->options['text_color_field']; ?>" name="prmbr_text_color_field" class="prmbr_color_field" data-default-color="#4c4c4c" />
						</label>
                    </td>
                </tr>
                <tr>
					<th scope="row"><?php _e( 'HTML', 'promobar' ); ?></th>
					<td class="prmbr_give_notice">
						<?php wp_editor( $this->options['html'], "prmbr_html", array(
							'teeny'			=> true,
							'media_buttons' => true,
							'textarea_rows' => 5,
							'textarea_name' => 'prmbr_html',
							'quicktags' 	=> true
						) ); ?>
					</td>
				</tr>
			</table>
			<?php $this->pro_block( 'prmbr_countdown_block', array( 'dates' => $this->options['dates'], 'forms' => $this->options['forms'] ) );
		}
        public function pro_block_image( $block_name = '', $args = array(), $force = false ) {
            if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) { ?>
                <div class="bws_pro_version_bloc prmbr-pro-feature image-pro">
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
        public function pro_block_shortcode( $block_name = '', $args = array(), $force = false ) {
            if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) { ?>
                <div class="bws_pro_version_bloc prmbr-pro-feature shortcode_pro">
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
		/* Display bws_pro_version block by its name */
		public function pro_block( $block_name = '', $args = array(), $force = false ) {
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
		public function tab_custom_css_settings() {
			$count_key = count( $this->options['css'] );
			$new_field_class = 'hidden';
			$new_field_is_disabled = disabled( 1, 1, false );
			?>
			<h3 class="bws_tab_label"><?php _e( 'Custom CSS', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="prmbr_file_fields_place" >
				<noscript>
					<table class="form-table prmbr-form-table prmbr_file_fields_template prmbr_noscript">
						<tbody >
							<tr valign="top">
								<th>
									<span class='prmbr_name_of_fields prmbr_file_path'><?php _e( 'CSS File Path', 'promobar' ); ?></span>
								</th>
								<td>
									<input type="text" value="" name="prmbr_css_file_path[<?php echo $count_key; ?>]" class="prmbr_css_file_path prmbr_inputs" />
								</td>
							</tr>
							<tr valign="top">
								<th>
									<span class='prmbr_name_of_fields prmbr_file_date_from'><?php _e( 'Date From', 'promobar' ); ?></span>
								</th>
								<td>
									<input type="text" value="" name="prmbr_start_date_for_css_file[<?php echo $count_key; ?>]" class="prmbr_inputs_date prmbr_start_date_for_css_file prmbr_inputs" />
								</td>
							</tr>
							<tr valign="top">
								<th>
									<span class='prmbr_name_of_fields prmbr_file_date_to'><?php _e( 'Date To', 'promobar' ); ?></span>
								</th>
								<td>
									<input type="text" value="" name="prmbr_end_date_for_css_file[<?php echo $count_key; ?>]" class="prmbr_inputs_date prmbr_end_date_for_css_file prmbr_inputs" />
								</td>
							</tr>
							<tr valign="top">
								<th>
									<span class='prmbr_name_of_fields prmbr_file_repeat'><?php _e( 'Repeat Every Year', 'promobar' ); ?></span>
								</th>
								<td>
									<input type="checkbox" value="" name="prmbr_file_repeat[<?php echo $count_key; ?>]" class="prmbr_file_repeat prmbr_inputs" />
								</td>
							</tr>
							<tr valign="top" class="prmbr_tbody_border">
								<th>
									<span class='prmbr_name_of_fields prmbr_file_delete'><?php _e( 'Delete', 'promobar' ); ?></span>
								</th>
								<td>
									<button class="prmbr_file_delete_button" name="prmbr_delete_button" type="submit" value="<?php echo $count_key; ?>"><?php _e( 'Delete', 'promobar' ); ?></button>
								</td>
								<hr class="hidden"/>
							</tr>
						</tbody>
					</table>
				</noscript>
				<?php if( ! empty( $this->options['css'] ) ) {
					foreach ( $this->options['css'] as $key => $item ) { ?>
						<table valign="top" class="form-table prmbr-form-table">
							<tbody class="prmbr_options['fields']">
								<tr valign="top" class="prmbr_options['fields']_holder">
									<th>
										<span class='prmbr_name_of_fields prmbr_file_path'><?php _e( 'CSS File Path', 'promobar' ); ?></span>
									</th>
									<td>
										<input type="text" value="<?php echo $item['file_path']; ?>" name="prmbr_css_file_path[<?php echo $key; ?>]" class="prmbr_css_file_path prmbr_inputs" />
									</td>
								</tr>
								<tr valign="top" class="prmbr_options['fields']_holder">
									<th>
										<span class='prmbr_name_of_fields prmbr_file_date_from'><?php _e( 'Date From', 'promobar' ); ?></span>
									</th>
									<td>
										<input type="text" required value="<?php echo $item['start_date']; ?>" name="prmbr_start_date_for_css_file[<?php echo $key; ?>]" class="prmbr_inputs_date prmbr_start_date_for_css_file prmbr_inputs" />
									</td>
								</tr>
								<tr valign="top" class="prmbr_options['fields']_holder">
									<th>
										<span class='prmbr_name_of_fields prmbr_file_date_to'><?php _e( 'Date To', 'promobar' ); ?></span>
									</th>
									<td>
										<input type="text" required value="<?php echo $item['end_date']; ?>" name="prmbr_end_date_for_css_file[<?php echo $key; ?>]" class="prmbr_inputs_date prmbr_end_date_for_css_file prmbr_inputs" />
									</td>
								</tr>
								<tr valign="top" class="prmbr_options['fields']_holder">
									<th>
										<span class='prmbr_name_of_fields prmbr_file_repeat'><?php _e( 'Repeat Every Year', 'promobar' ); ?></span>
									</th>
									<td>
										<input type="checkbox" value="<?php echo $item['repeat']; ?>" name="prmbr_file_repeat[<?php echo $key; ?>]" <?php checked( $item['repeat'], 1 ); ?> class="prmbr_file_repeat prmbr_inputs" />
									</td>
								</tr>
								<tr valign="top" class="prmbr_options['fields']_holder prmbr_tbody_border">
									<th>
										<span class='prmbr_name_of_fields prmbr_file_delete'><?php _e( 'Delete', 'promobar' ); ?></span>
									</th>
									<td>
										<button class="prmbr_file_delete_button" name="prmbr_delete_button" type="submit" value="<?php echo $key; ?>" type="button"><?php _e( 'Delete', 'promobar' ); ?></button>
									</td>
									<hr class="hidden"/>
								</tr>
							</tbody>
						</table>
					<?php } 
				}?>
				<table class="form-table prmbr-form-table prmbr_file_fields_template <?php echo $new_field_class; ?>">
					<tbody >
						<tr valign="top">
							<th>
								<span class='prmbr_name_of_fields prmbr_file_path'><?php _e( 'CSS File Path', 'promobar' ); ?></span>
							</th>
							<td>
								<input type="text" value="" name="prmbr_css_file_path[NUMB]" class="prmbr_css_file_path prmbr_inputs" <?php echo $new_field_is_disabled; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th>
								<span class='prmbr_name_of_fields prmbr_file_date_from'><?php _e( 'Date From', 'promobar' ); ?></span>
							</th>
							<td>
								<input type="text" value="" name="prmbr_start_date_for_css_file[NUMB]" class="prmbr_inputs_date prmbr_start_date_for_css_file prmbr_inputs" <?php echo $new_field_is_disabled; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th>
								<span class='prmbr_name_of_fields prmbr_file_date_to'><?php _e( 'Date To', 'promobar' ); ?></span>
							</th>
							<td>
								<input type="text" value="" name="prmbr_end_date_for_css_file[NUMB]" class="prmbr_inputs_date prmbr_end_date_for_css_file prmbr_inputs" <?php echo $new_field_is_disabled; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th>
								<span class='prmbr_name_of_fields prmbr_file_repeat'><?php _e( 'Repeat Every Year', 'promobar' ); ?></span>
							</th>
							<td>
								<input type="checkbox" value="" name="prmbr_file_repeat[NUMB]" class="prmbr_file_repeat prmbr_inputs" <?php echo $new_field_is_disabled; ?> />
							</td>
						</tr>
						<tr valign="top" class="prmbr_tbody_border">
							<th>
								<span class='prmbr_name_of_fields prmbr_file_delete'><?php _e( 'Delete', 'promobar' ); ?></span>
							</th>
							<td>
								<button class="prmbr_file_delete_button"  type="submit" name="prmbr_delete_button" value="[NUMB]"><?php _e( 'Delete', 'promobar' ); ?></button>
							</td>
							<hr class="hidden"/>
						</tr>
					</tbody>
				</table>
			</div>
			<div id='addItem'><button class="prmbr_file_add_button" name="prmbr_add_new_css" type="button" href=''><?php _e( 'Add New Styles File', 'promobar' ); ?></button></div>
		<?php }

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
                <div class="inside shortcode_pro">
                    <?php $this->pro_block_shortcode('prmbr_shortcode'); ?>
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