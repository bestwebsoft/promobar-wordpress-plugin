<?php
/**
 * Banners on plugin settings page
 * @package Promobar by BestWebSoft
 * @since 0.1
 */
if ( ! function_exists( 'prmbr_pro_block' ) ) {
	function prmbr_pro_block( $func, $show_cross = true ) {
		global $prmbr_plugin_info, $wp_version, $prmbr_options;
		if ( ! bws_hide_premium_options_check( $prmbr_options ) || ! $show_cross ) { ?>
			<div class="bws_pro_version_bloc prmbr_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of the plugin', 'promobar' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'promobar' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<?php call_user_func( $func ); ?>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="#" target="_blank" title="Promobar Pro Plugin"><?php _e( 'Upgrade to Pro', 'promobar' ); ?></a>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'prmbr_countdown_block' ) ) {
	function prmbr_countdown_block( $args = array() ) { 
		$dates = ( ! empty( $args['dates'] ) ) ? $args['dates'] : array();
		$forms = ( ! empty( $args['forms'] ) ) ? $args['forms'] : array(); ?>
        <div class="bws_tab_sub_label"><?php _e( 'Countdown', 'promobar' ); ?></div>
		<table class="form-table prmbr-form-table">
			<tbody>
				<tr valign="top">
					<fieldset class="prmbr_checkboxes_for_clock">
						<?php foreach ( $forms as $forms => $form ) { ?>
							<th>
								<label>
									<?php echo $form; ?>

								</label>
							</th>
							<td>
                                <input type="checkbox" value="1"  />
								<span class="bws_info"><?php _e( 'Countdown Timer displays only at minimal display width 450px.', 'promobar-pro' ); ?></span>
							</td>
						<?php }?>
					</fieldset>
				</tr>
                <tr valign="top">
                    <th>
                        <label for="">
                            <?php _e( 'Show Dismiss Button', 'promobar-pro' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="checkbox" value="1" name="prmbr_show_countdown_dismiss_button" id="prmbr_show_countdown_dismiss_button">
                        <span class="bws_info"><?php _e( 'When users close Countdown it is written to the cookies.', 'promobar-pro' ); ?></span>
                    </td>
                </tr>
				<?php foreach ( $dates as $date_slug => $date ) { ?>
					<tr valign="top">
						<th scope="row"><div class="inside"><?php _e( $date, 'promobar-pro' ); ?></div></th>
						<td>
							<input class="prmbr_inputs_date prmbr_date" id="<?php echo "prmbr_{$date_slug}";?>" type="text" disabled="disabled" />
						</td>
					</tr>
				<?php } ?>
                <tr>
                    <th scope="row"><?php _e( 'Background', 'promobar-pro' ); ?></th>
                    <td>
                        <fieldset>
                            <div>
                                <label for="prmbr_background_transparant_cntdwn">
                                    <input type="radio" name="prmbr_background_cntdwn" id="prmbr_background_transparant_cntdwn" value="transparant" class="prmbr_background_transparant_cntdwn" checked /> <?php _e( 'Transparant', 'promobar-pro' ); ?>
                                </label>
                            </div>
                            <div class="background_color_cntdwn wrapper">
                                <label for="prmbr_background_color_cntdwn">
                                    <input type="radio" name="prmbr_background_cntdwn" id="prmbr_background_color_cntdwn" value="color" class="prmbr_color_cntdwn" /> <?php _e( 'Color', 'promobar-pro' ); ?>
                                </label>
                            </div>
                            <div class="wrapper topp">
                                <label for="prmbr_background_image_cntdwn">
                                    <input type="radio" name="prmbr_background_cntdwn" id="prmbr_background_image_cntdwn" value="image" /> <?php _e( 'Image', 'promobar-pro' ); ?>
                                </label>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Text Color', 'promobar-pro' ); ?></th>
                    <td>
                        <label for="prmbr_text_color_field_cntdwn">
                            <input type="text" id="prmbr_text_color_field" value="#4c4c4c" name="prmbr_text_color_field_cntdwn" class="prmbr_color_field" data-default-color="#4c4c4c" />
                        </label>
                    </td>
                </tr>
                <tr>
				<tr>
					<th>
						<div class="inside"><?php _e( 'Block Class/ID attribute to Paste Big Banner', 'promobar-pro' ); ?></div>
					</th>
					<td>
						<input type="text" name="prmbr_selector" value="" />
						<span class="description"><?php _e( 'Paste parent selector for big countdown banner.', 'promobar-pro' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<div class="inside"><?php _e( 'Repeat Every Year', 'promobar' ); ?></div>
					</th>
					<td>
						<input type="checkbox" disabled="disabled" />
						<span class="description"><?php _e( 'Enable to repeat the discount countdown every year.', 'promobar' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"></th>
					<td>
						<div id="prmbr_tabs">
							<ul>
								<li><a href="#prmbr_pre_tab"><?php _e( 'Preliminary Period', 'promobar' ); ?></a></li>
								<li><a href="#prmbr_main_tab"><?php _e( 'Discount Period', 'promobar' ); ?></a></li>
							</ul>
							<div id="prmbr_pre_tab">
								<span class="description "><?php _e( 'Create your own homepage banner for preliminary period.', 'promobar' ); ?></span>
								<?php wp_editor( '', 'pre_block_for_big_countdown', array( 'textarea_rows' => 5 ) );?>
								<div id="pre_block_for_big_countdown"></div>
								<span class="description "><?php _e( 'Create your own banner for preliminary period.', 'promobar' ); ?></span>
								<?php wp_editor( '', 'pre_block_for_small_countdown', array( 'textarea_rows' => 5 ) );?>
								<div id="pre_block_for_small_countdown"></div>
							</div>
							<div id="prmbr_main_tab">
								<span class="description "><?php _e( 'Create your own homepage banner for discount period.', 'promobar' ); ?></span>
								<?php wp_editor( '', 'main_block_for_big_countdown', array( 'textarea_rows' => 5 ) );?>
								<div id="main_block_for_big_countdown"></div>
								<span class="description "><?php _e( 'Create your own banner for discount period.', 'promobar' ); ?></span>
								<?php wp_editor( '', 'main_block_for_small_countdown', array( 'textarea_rows' => 5 ) );?>
								<div id="main_block_for_small_countdown"></div>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	<?php }
}

if ( ! function_exists( 'prmbr_image' ) ) {
    function prmbr_image( $prmbr_options ) { ?>
        <div class="wrapper topp">
            <label for="prmbr_background_image">
                <input type="radio" name="prmbr_background" id="prmbr_background_image" value="image" <?php checked( 'image' == $prmbr_options['background'] ); ?> /> <?php _e( 'Image', 'promobar' ); ?>
            </label>
            <fieldset>
                <div class="upload-image">
                    <input class="prmbr-image-url" type="text" name="prmbr_url" id="prmbr_url" />
                    <input class="button-secondary prmbr-upload-image hide-if-no-js" id="prmbr_url" type="button" value="<?php echo __( 'Add Image', 'promobar' ); ?>"/>
                </div>
            </fieldset>
        </div>
    <?php }
}

if ( ! function_exists( 'prmbr_shortcode' ) ) {
    function prmbr_shortcode() {
        _e( 'Add Countdown to your page or post </br> using the following shortcode:', 'promobar-pro' );
        bws_shortcode_output( '[bws_countdown]' );
    }
}