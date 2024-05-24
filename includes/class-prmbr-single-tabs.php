<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Prmbr_Single_Tabs' ) ) {
	/**
	 * Class Prmbr_Single_Tabs for disp[lay Settings Tabs
	 */
	class Prmbr_Single_Tabs extends Bws_Settings_Tabs {

		/**
		 * Promobar ID
		 *
		 * @var int $prmbr_id
		 */
		private $prmbr_id;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename - name for plugin.
		 */
		public function __construct( $plugin_basename ) {
			global $prmbr_options, $prmbr_plugin_info;

			/* Get promobar ID. */
			$this->prmbr_id = ! empty( $_REQUEST['prmbr_id'] ) ? absint( $_REQUEST['prmbr_id'] ) : '';

			if ( empty( $this->prmbr_id ) ) {
				$tabs = array(
					'settings' => array( 'label' => __( 'Settings', 'promobar' ) ),
				);
			} else {
				$tabs = array(
					'settings' => array( 'label' => __( 'Settings', 'promobar' ) ),
					'display'  => array( 'label' => __( 'Display', 'promobar' ) ),
				);
			}

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $prmbr_plugin_info,
					'prefix'             => 'prmbr',
					'default_options'    => prmbr_default_options(),
					'options'            => $prmbr_options,
					'is_network_options' => is_network_admin(),
					'tabs'               => $tabs,
					'wp_slug'            => 'promobar',
					'link_key'           => 'd765697418cb3510ea536e47c1e26396',
					'link_pn'            => '196',
					'doc_link'           => 'https://bestwebsoft.com/documentation/promobar/promobar-user-guide/',
				)
			);

			$this->background = array(
				'transparent' => __( 'Transparent', 'promobar' ),
				'color'       => __( 'Color', 'promobar' ),
				'image'       => __( 'Image', 'promobar' ),
			);
		}

		/**
		 * Save to the database
		 *
		 * @access public
		 * @return array    The action results
		 */
		public function save_options() {
			$message = '';
			$notice  = '';
			$error   = '';

			if ( isset( $_POST['prmbr_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['prmbr_nonce_field'] ) ), 'prmbr_settings_action' ) ) {

				$this->options['enable'] = isset( $_POST['prmbr_enable'] ) ? 1 : 0;
				$this->options['view']   = (
					isset( $_POST['prmbr_view'] ) &&
					in_array( sanitize_text_field( wp_unslash( $_POST['prmbr_view'] ) ), array( 'all_pages', 'homepage', 'shortcode_or_function_for_view' ), true )
				) ? sanitize_text_field( wp_unslash( $_POST['prmbr_view'] ) ) : 'all_pages';

				/* Show Dismiss Button */
				$this->options['dismiss_promobar'] = isset( $_POST['prmbr_show_promobar_dismiss_button'] ) ? 1 : 0;

				/* Position ALL */
				$this->options['position_all'] = (
					isset( $_POST['prmbr_position_all'] ) &&
					in_array( sanitize_text_field( wp_unslash( $_POST['prmbr_position_all'] ) ), array( 'absolute', 'fixed' ), true )
				) ? sanitize_text_field( wp_unslash( $_POST['prmbr_position_all'] ) ) : 'absolute';

				/* Position */
				foreach ( array( 'desktop', 'tablet', 'mobile' ) as $position ) {

					if ( isset( $_POST[ 'prmbr_position_' . $position . '_enabled' ] ) ) {

						$this->options[ 'position_' . $position ] = (
							isset( $_POST[ 'prmbr_position_' . $position ] ) &&
							in_array( sanitize_text_field( wp_unslash( $_POST[ 'prmbr_position_' . $position ] ) ), array( 'top', 'bottom', 'right', 'left' ), true )
						) ? sanitize_text_field( wp_unslash( $_POST[ 'prmbr_position_' . $position ] ) ) : 'top';

						/* Check the filling of the width units field. Add width fields */
						$this->options[ 'unit_left_' . $position ] = isset( $_POST[ 'prmbr_unit_left_' . $position ] ) && ( 'px' === $_POST[ 'prmbr_unit_left_' . $position ] ) ? 'px' : '%';
						if ( isset( $_POST[ 'prmbr_width_left_' . $position ] ) ) {
							if ( 'px' === $this->options[ 'unit_left_' . $position ] ) {
								$this->options[ 'width_left_' . $position ] = absint( $_POST[ 'prmbr_width_left_' . $position ] );
							} else {
								$this->options[ 'width_left_' . $position ] = absint( $_POST[ 'prmbr_width_left_' . $position ] ) < 100 ? absint( $_POST[ 'prmbr_width_left_' . $position ] ) : 100;
							}
						}

						$this->options[ 'unit_right_' . $position ] = isset( $_POST[ 'prmbr_unit_right_' . $position ] ) && ( 'px' === $_POST[ 'prmbr_unit_right_' . $position ] ) ? 'px' : '%';
						if ( isset( $_POST[ 'prmbr_width_right_' . $position ] ) ) {
							if ( 'px' === $this->options[ 'unit_right_' . $position ] ) {
								$this->options[ 'width_right_' . $position ] = absint( $_POST[ 'prmbr_width_right_' . $position ] );
							} else {
								$this->options[ 'width_right_' . $position ] = absint( $_POST[ 'prmbr_width_right_' . $position ] ) < 100 ? absint( $_POST[ 'prmbr_width_right_' . $position ] ) : 100;
							}
						}
					} else {
						$this->options[ 'position_' . $position ] = 'none';
					}
				}

				/* Promobar Background */
				$this->options['background'] = (
					isset( $_POST['prmbr_background'] ) &&
					in_array( sanitize_text_field( wp_unslash( $_POST['prmbr_background'] ) ), array( 'transparent', 'color', 'image' ), true )
				) ? sanitize_text_field( wp_unslash( $_POST['prmbr_background'] ) ) : 'transparent';

				/* Promobar Background Select Color */
				if ( isset( $_POST['prmbr_background_color_field'] ) ) {
					$this->options['background_color_field'] = sanitize_hex_color( wp_unslash( $_POST['prmbr_background_color_field'] ) );
				}

				/* Promobar Background Image */
				if ( isset( $_POST['prmbr_url'] ) && 'image' === sanitize_text_field( wp_unslash( $_POST['prmbr_background'] ) ) ) {
					if ( ! empty( $_POST['prmbr_url'] ) ) {
						$this->options['url'] = esc_url_raw( wp_unslash( $_POST['prmbr_url'] ) );
					} else {
						$this->options['url'] = '';
					}
				}

				/* Close Icon Color */
				$this->options['close_icon_color_field'] = isset( $_POST['prmbr_close_icon_color_field'] ) ? sanitize_hex_color( wp_unslash( $_POST['prmbr_close_icon_color_field'] ) ) : '';

				/* Promobar Text Color */
				$this->options['text_color_field'] = isset( $_POST['prmbr_text_color_field'] ) ? sanitize_hex_color( wp_unslash( $_POST['prmbr_text_color_field'] ) ) : '';

				/* Close Icon Size */
				$this->options['close_icon_size']              = isset( $_POST['prmbr_close_icon_size'] ) ? absint( $_POST['prmbr_close_icon_size'] ) : 30;

				$this->options['position_close_icon'] = (
					isset( $_POST['prmbr_position_close_icon'] ) &&
					in_array( sanitize_text_field( wp_unslash( $_POST['prmbr_position_close_icon'] ) ), array( 'right', 'left' ), true )
				) ? sanitize_text_field( wp_unslash( $_POST['prmbr_position_close_icon'] ) ) : 'right';

				/* Html clean before the show */
				$this->options['html'] = isset( $_POST['prmbr_html'] ) ? wp_kses_post( wp_unslash( $_POST['prmbr_html'] ) ) : '';

				update_option( 'prmbr_options', $this->options );

				$message = __( 'Settings saved.', 'promobar' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays the content of the "Settings" on the plugin settings page
		 *
		 * @access public
		 * @return void
		 */
		public function display_content() {
			global $wpdb;

			$save_results = $this->save_all_tabs_options(); ?>
			<h1>
				<?php
				/* Add page name and add new button to page */
				if ( ! empty( $this->prmbr_id ) ) {
					echo esc_html__( 'Edit Promobar', 'promobar' ) . '<span id="prmbr_btn_add_new" class="page-title-action" >' . esc_html__( 'Add New', 'promobar' ) . '</span>';
				} else {
					esc_html_e( 'Add New Promobar', 'promobar' );
				}
				?>
			</h1>
			<?php $this->display_messages( $save_results ); ?>
			<form class="bws_form" method="POST" action="admin.php?page=promobar-new.php<?php echo esc_attr( ! empty( $this->prmbr_id ) ? '&prmbr_id=' . $this->prmbr_id : '' ); ?>">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content" style="position: relative;">
							<?php
							$this->pro_block(
								'prmbr_title',
								array(
									'background' => $this->options['background'],
									'url'        => $this->options['url'],
								)
							);
							$this->display_tabs();
							?>
						</div><!-- #post-body-content -->
						<div id="postbox-container-1" class="postbox-container">
							<div class="meta-box-sortables ui-sortable">
								<div id="submitdiv" class="postbox">
									<h3 class="hndle"><?php esc_html_e( 'Publish', 'promobar' ); ?></h3>
									<div class="inside">
										<div class="submitbox" id="submitpost">
											<div id="major-publishing-actions">
												<div id="publishing-action">
													<input type="hidden" name="<?php echo esc_attr( $this->prefix ); ?>_form_submit" value="submit" />
													<input id="bws-submit-button" type="submit" class="button button-primary button-large" value="<?php esc_html_e( 'Update', 'promobar' ); ?>" />
													<?php wp_nonce_field( $this->plugin_basename, 'bws_nonce_name' ); ?>
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="postbox">
									<h3 class="hndle">
										<?php esc_html_e( 'Promobar Shortcode', 'promobar' ); ?>
									</h3>
									<div class="inside">
										<?php esc_html_e( 'Add PromoBar to your posts, pages, custom post types or widgets by using the following shortcode:', 'promobar' ); ?><br /><br />
										<?php bws_shortcode_output( '[bws_promobar]' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<?php
		}

		/**
		 * Display Promobar Settings tab
		 */
		public function tab_settings() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label">Promobar</div>
			<table class="form-table">
				<tr>
					<th><?php esc_html_e( 'Display Promobar', 'promobar' ); ?></th>
					<td>
						<label>
							<input type="checkbox" value="1" class="bws_option_affect" data-affect-show=".prmbr_enable" name="prmbr_enable" <?php checked( $this->options['enable'] ); ?>/>
							<span class="bws_info"><?php esc_html_e( 'Enable to display a promo bar.', 'promobar' ); ?></span>
						</label>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php esc_html_e( 'Display Promobar', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_all_pages">
								<input type="radio" id="prmbr_all_pages" name="prmbr_view" value="all_pages" <?php checked( 'all_pages' === $this->options['view'] ); ?> /> <?php esc_html_e( 'on all pages', 'promobar' ); ?>
							</label>
							<br />
							<label for="prmbr_homepage">
								<input type="radio" id="prmbr_homepage" name="prmbr_view" value="homepage" <?php checked( 'homepage' === $this->options['view'] ); ?> /> <?php esc_html_e( 'on the homepage', 'promobar' ); ?>
							</label>
							<br />
							<label for="shortcode_or_function_for_view">
								<input type="radio" id="shortcode_or_function_for_view" name="prmbr_view" value="shortcode_or_function_for_view" <?php checked( 'shortcode_or_function_for_view' === $this->options['view'] ); ?> /> <?php esc_html_e( 'display via shortcode or function only', 'promobar' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			$this->pro_block(
				'prmbr_date',
				array(
					'background' => $this->options['background'],
					'url'        => $this->options['url'],
				)
			);
			?>
			<table class="form-table">
				<tr class="prmbr_enable">
					<th><?php esc_html_e( 'Display on', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="prmbr_position_desktop_enabled" class="prmbr_option_affect_columns" data-affect-show=".prmbr_position_column_desktop" <?php checked( 'none' !== $this->options['position_desktop'] ); ?> /> <?php esc_html_e( 'Desktop', 'promobar' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" name="prmbr_position_tablet_enabled" class="prmbr_option_affect_columns" data-affect-show=".prmbr_position_column_tablet" <?php checked( 'none' !== $this->options['position_tablet'] ); ?> /> <?php esc_html_e( 'Tablet', 'promobar' ); ?> <span class="bws_info">(<?php esc_html_e( 'From 728 to 426', 'promobar' ); ?>)</span>
							</label>
							<br />
							<label>
								<input type="checkbox" name="prmbr_position_mobile_enabled" class="prmbr_option_affect_columns" data-affect-show=".prmbr_position_column_mobile" <?php checked( 'none' !== $this->options['position_mobile'] ); ?> /> <?php esc_html_e( 'Mobile', 'promobar' ); ?> <span class="bws_info">(<?php esc_html_e( 'Less then 426', 'promobar' ); ?>)</span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top" class="prmbr_enable">
					<th>
						<?php esc_html_e( 'Close Icon', 'promobar' ); ?>
					</th>
					<td>
						<label for="prmbr_show_promobar_dismiss_button">
							<input type="checkbox" value="1" name="prmbr_show_promobar_dismiss_button" id="prmbr_show_promobar_dismiss_button" <?php checked( $this->options['dismiss_promobar'] ); ?>/>
							<span class="bws_info"><?php esc_html_e( 'Enable to display a close/dismiss icon on the promo bar.', 'promobar' ); ?></span>
						</label>
					</td>
				</tr>
				<?php
				$close_icon_class = '';
				if ( 1 === $this->options['dismiss_promobar'] ) {
					$close_icon_status = ' display="none"';
				}
				?>
				<tr class="prmbr_enable prmbr_enable_icon_color" <?php echo esc_html( $close_icon_status ); ?>>
					<th scope="row"><?php esc_html_e( 'Close Icon Color', 'promobar' ); ?></th>
					<td>
						<label for="prmbr_close_icon_color_field <?php echo esc_html( $close_icon_class ); ?>">
							<input  type="text" id="prmbr_close_icon_color_field" value="<?php echo esc_attr( $this->options['close_icon_color_field'] ); ?>" name="prmbr_close_icon_color_field" class="prmbr_color_field" data-default-color="#bbb" />
						</label>
					</td>
				</tr>
				<tr class="prmbr_enable prmbr_enable_icon_size" <?php echo esc_html( $close_icon_status ); ?>>
					<th scope="row"><?php esc_html_e( 'Close Icon Size', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label><input class="small-text" name="prmbr_close_icon_size" type="text" id="prmbr_close_icon_size" value="<?php echo esc_attr( $this->options['close_icon_size'] ); ?>" /> <?php esc_html_e( 'Font-Size', 'promobar' ); ?> (px)<br />
						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable prmbr_enable_icon_position" <?php echo esc_attr( $close_icon_status ); ?>>
					<th scope="row"><?php esc_html_e( 'Close Icon Position', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_icon_left">
								<input type="radio" id="prmbr_left" name="prmbr_position_close_icon" value="left" <?php checked( 'left' === $this->options['position_close_icon'] ); ?> /> <?php esc_html_e( 'Left', 'promobar' ); ?>
							</label>
							<br />
							<label for="prmbr_icon_right">
								<input type="radio" id="prmbr_right" name="prmbr_position_close_icon" value="right" <?php checked( 'right' === $this->options['position_close_icon'] ); ?> /> <?php esc_html_e( 'Right', 'promobar' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php esc_html_e( 'Position', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<label for="prmbr_absolute">
								<input type="radio" id="prmbr_absolute" name="prmbr_position_all" value="absolute" <?php checked( 'absolute' === $this->options['position_all'] ); ?> /> <?php esc_html_e( 'absolute', 'promobar' ); ?>
							</label>
							<br />
							<label for="prmbr_fixed">
								<input type="radio" id="prmbr_fixed" name="prmbr_position_all" value="fixed" <?php checked( 'fixed' === $this->options['position_all'] ); ?> /> <?php esc_html_e( 'fixed', 'promobar' ); ?>
							</label>    
							<?php
							$this->pro_block(
								'prmbr_popup',
								array(
									'background' => $this->options['background'],
									'url'        => $this->options['url'],
								)
							);
							?>

						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th class="prmbr_header_alignment" scope="row"><?php esc_html_e( 'Alignment', 'promobar' ); ?></th>
					<td class="prmbr_header_alignment">
						<?php
						foreach ( array(
							'desktop' => __( 'Desktop', 'promobar' ),
							'tablet'  => __( 'Tablet', 'promobar' ),
							'mobile'  => __( 'Mobile', 'promobar' ),
						) as $position => $position_name ) {
							?>
							<div class="prmbr_position_column_<?php echo esc_attr( $position ); ?> prmbr_position_column">
								<p><strong><?php echo esc_html( $position_name ); ?></strong></p>
								<br>
								<fieldset class="prmbr_position_cell">
									<label>
										<input  type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo esc_attr( $position ); ?>" value="top" <?php checked( 'none' === $this->options[ 'position_' . $position ] || 'top' === $this->options[ 'position_' . $position ] ); ?> /> <?php esc_html_e( 'Top', 'promobar' ); ?>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" name="prmbr_position_<?php echo esc_attr( $position ); ?>" value="bottom" <?php checked( 'none' === $this->options[ 'position_' . $position ] || 'bottom' === $this->options[ 'position_' . $position ] ); ?> /> <?php esc_html_e( 'Bottom', 'promobar' ); ?>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_left_options_<?php echo esc_attr( $position ); ?>" name="prmbr_position_<?php echo esc_attr( $position ); ?>" value="left" <?php checked( 'none' === $this->options[ 'position_' . $position ] || 'left' === $this->options[ 'position_' . $position ] ); ?> /> <?php esc_html_e( 'Left', 'promobar' ); ?>
										<div  class="prmbr_left_options_<?php echo esc_attr( $position ); ?> prmbr_emerging_options">
											<span class="bws_info">
												<?php esc_html_e( 'width', 'promobar' ); ?>
											</span>
											<input id="width_left_<?php echo esc_attr( $position ); ?>" type="number" min="1" class="small-text" name="prmbr_width_left_<?php echo esc_attr( $position ); ?>" value="<?php echo esc_attr( $this->options[ 'width_left_' . $position ] ); ?>" />
											<select name="prmbr_unit_left_<?php echo esc_attr( $position ); ?>">
												<option value="px" 
												<?php
												if ( 'px' === $this->options[ 'unit_left_' . $position ] ) {
													echo 'selected';
												}
												?>
												><?php esc_html_e( 'px', 'promobar' ); ?></option>
												<option value="%" 
												<?php
												if ( '%' === $this->options[ 'unit_left_' . $position ] ) {
													echo 'selected';
												}
												?>
												>%</option>
											</select>
										</div>
									</label>
									<label>
										<input  type="radio" class="prmbr_option_affect" data-affect-show=".prmbr_right_options_<?php echo esc_attr( $position ); ?>" name="prmbr_position_<?php echo esc_attr( $position ); ?>" value="right" <?php checked( 'none' === $this->options[ 'position_' . $position ] || 'right' === $this->options[ 'position_' . $position ] ); ?> /> <?php esc_html_e( 'Right', 'promobar' ); ?>
										<div  class="prmbr_right_options_<?php echo esc_attr( $position ); ?> prmbr_emerging_options">
											<span class="bws_info">
												<?php esc_html_e( 'width', 'promobar' ); ?>
											</span>
											<input id="width_right_<?php echo esc_attr( $position ); ?>" type="number" min="1" class="small-text" name="prmbr_width_right_<?php echo esc_attr( $position ); ?>" value="<?php echo esc_attr( $this->options[ 'width_right_' . $position ] ); ?>" />
											<select name="prmbr_unit_right_<?php echo esc_attr( $position ); ?>">
												<option value="px" 
												<?php
												if ( 'px' === $this->options[ 'unit_right_' . $position ] ) {
													echo 'selected';
												}
												?>
												><?php esc_html_e( 'px', 'promobar' ); ?></option>
												<option value="%" 
												<?php
												if ( '%' === $this->options[ 'unit_right_' . $position ] ) {
													echo 'selected';
												}
												?>
												>%</option>
											</select>
										</div>
									</label>
								</fieldset>
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php esc_html_e( 'Background', 'promobar' ); ?></th>
					<td>
						<fieldset>
							<div>
								<label for="prmbr_background_transparent">
									<input type="radio" name="prmbr_background" id="prmbr_background_transparent" value="transparent" class="prmbr_background_transparent" <?php checked( 'transparent' === $this->options['background'] ); ?> /> <?php esc_html_e( 'Transparent', 'promobar' ); ?>
								</label>
							</div>
							<div class="background_color wrapper">
								<label for="prmbr_background_color">
									<input type="radio" name="prmbr_background" id="prmbr_background_color" value="color" class="prmbr_color"<?php checked( 'color' === $this->options['background'] ); ?> /> <?php esc_html_e( 'Color', 'promobar' ); ?>
								</label>
								<input type="text" id="prmbr_background_color_field" value="<?php echo esc_attr( $this->options['background_color_field'] ); ?>" name="prmbr_background_color_field" class="prmbr_color_field" data-default-color="#c4e9ff" />
							</div>
							<?php
							$this->pro_block(
								'prmbr_image',
								array(
									'background' => $this->options['background'],
									'url'        => $this->options['url'],
								)
							);
							?>
						</fieldset>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php esc_html_e( 'Text Color', 'promobar' ); ?></th>
					<td>
						<label for="prmbr_text_color_field">
							<input  type="text" id="prmbr_text_color_field" value="<?php echo esc_attr( $this->options['text_color_field'] ); ?>" name="prmbr_text_color_field" class="prmbr_color_field" data-default-color="#4c4c4c" />
						</label>
					</td>
				</tr>
				<tr class="prmbr_enable">
					<th scope="row"><?php esc_html_e( 'HTML', 'promobar' ); ?></th>
					<td class="prmbr_give_notice">
						<?php
						wp_editor(
							$this->options['html'],
							'prmbr_html',
							array(
								'teeny'         => true,
								'media_buttons' => true,
								'textarea_rows' => 5,
								'textarea_name' => 'prmbr_html',
								'quicktags'     => true,
							)
						);
						?>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'prmbr_settings_action', 'prmbr_nonce_field' ); ?>
			<?php
		}

		/**
		 * Display bws_pro_version block by its name
		 *
		 * @param string $block_name - function name.
		 * @param array  $args - args for function.
		 * @param bool   $force - status.
		 */
		public function pro_block( $block_name = '', $args = array(), $force = false ) {
			if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) {
				?>
				<div class="bws_pro_version_bloc prmbr-pro-feature">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'promobar' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php $block_name( $args ); ?>
						</div>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
				<?php
			}
		}

		/**
		 * Display Promobar Settings tab
		 */
		public function tab_display() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Promobar Settings', 'promobar' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table">
							<tr valign="top">
								<td colspan="2">
									<p><?php esc_html_e( 'Choose the necessary post types (or single pages) where you would like to display PromoBar', 'promobar' ); ?>: </p>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" type="checkbox" name="prmbr_jstree_url" value="1" />
										<?php esc_html_e( 'Show URL for pages', 'promobar' ); ?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo esc_url( plugins_url( '../images/pro_screen_1.png', __FILE__ ) ); ?>" alt="<?php esc_html_e( "Example of the site's pages tree", 'promobar' ); ?>" title="<?php esc_html_e( "Example of the site's pages tree", 'promobar' ); ?>" />
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php
		}
	}
}
