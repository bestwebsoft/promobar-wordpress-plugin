<?php
/**
 * Banners on plugin settings page
 * @package Promobar by BestWebSoft
 * @since 0.1
 */

if ( ! function_exists( 'prmbr_countdown_block' ) ) {
	function prmbr_countdown_block() { 
		$dates = array(
			'start_date_of_the_preliminary_period' 	=> __( 'Preliminary Period Starts on', 'promobar' ),
			'start_date_of_the_main_period' 		=> __( 'Discount Starts on', 'promobar' ),
			'end_date_of_the_main_period' 			=> __( 'Discount Ends on', 'promobar' )
		); ?>        
		<table class="form-table prmbr-form-table">
			<tbody>
				<tr valign="top">
					<th>
                        <?php _e( 'Show Countdown Timer', 'promobar' ); ?>
                    </th>
                    <td>
                        <label>
                            <input disabled="disabled" type="checkbox" value="1" /> <span class="bws_info"><?php _e( 'Enable to display a countdown timer (min display width is 450px).', 'promobar' ); ?></span>
                        </label>
                    </td>
				<tr>
                <tr valign="top">
                    <th>
                        <label for="">
                            <?php _e( 'Close Icon', 'promobar' ); ?>
                        </label>
                    </th>
                    <td>
                        <input disabled="disabled" type="checkbox" value="1" name="prmbr_show_countdown_dismiss_button" id="prmbr_show_countdown_dismiss_button">
                        <span class="bws_info"><?php _e( 'Enable to display a close/dismiss icon on the promo bar.', 'promobar' ); ?></span>
                    </td>
                </tr>
				<?php foreach ( $dates as $date_slug => $date ) { ?>
					<tr valign="top">
						<th scope="row"><div class="inside"><?php echo $date ?></div></th>
						<td>
							<input disabled="disabled" class="prmbr_inputs_date prmbr_date" id="<?php echo "prmbr_{$date_slug}";?>" type="text" disabled="disabled" />
						</td>
					</tr>
				<?php } ?>
                <tr>
                    <th scope="row"><?php _e( 'Background', 'promobar' ); ?></th>
                    <td>
                        <fieldset>
                            <div>
                                <label for="prmbr_background_transparant_cntdwn">
                                    <input disabled="disabled" type="radio" name="prmbr_background_cntdwn" id="prmbr_background_transparant_cntdwn" value="transparant" class="prmbr_background_transparant_cntdwn" checked /> <?php _e( 'Transparent', 'promobar' ); ?>
                                </label>
                            </div>
                            <div class="background_color_cntdwn wrapper">
                                <label for="prmbr_background_color_cntdwn">
                                    <input disabled="disabled" type="radio" name="prmbr_background_cntdwn" id="prmbr_background_color_cntdwn" value="color" class="prmbr_color_cntdwn" /> <?php _e( 'Color', 'promobar' ); ?>
                                </label>
                            </div>
                            <div class="wrapper topp">
                                <label for="prmbr_background_image_cntdwn">
                                    <input disabled="disabled" type="radio" name="prmbr_background_cntdwn" id="prmbr_background_image_cntdwn" value="image" /> <?php _e( 'Image', 'promobar' ); ?>
                                </label>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Text Color', 'promobar' ); ?></th>
                    <td>
                        <label for="prmbr_text_color_field_cntdwn">
                            <input disabled="disabled" type="text" id="prmbr_text_color_field" value="#4c4c4c" name="prmbr_text_color_field_cntdwn" class="prmbr_color_field" data-default-color="#4c4c4c" />
                        </label>
                    </td>
                </tr>
                <tr>
				<tr>
					<th>
						<div class="inside"><?php _e( 'Parent Selector Class/ID', 'promobar' ); ?></div>
					</th>
					<td>
						<input disabled="disabled" type="text" name="prmbr_selector" value="" />
						<span class="description"><?php _e( 'Enter the class/id of the parent selector where you want to insert the countdown.', 'promobar' ); ?></span>
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
                <input disabled="disabled" type="radio" name="prmbr_background" id="prmbr_background_image" value="image" <?php checked( 'image' == $prmbr_options['background'] ); ?> /> <?php _e( 'Image', 'promobar' ); ?>
            </label>
            <fieldset>
                <div class="upload-image">
                    <input disabled="disabled" class="prmbr-image-url" type="text" name="prmbr_url" id="prmbr_url" />
                    <input disabled="disabled" class="button-secondary prmbr-upload-image hide-if-no-js" id="prmbr_url" type="button" value="<?php _e( 'Add Image', 'promobar' ); ?>"/>
                </div>
            </fieldset>
        </div>
    <?php }
}

if ( ! function_exists( 'prmbr_shortcode' ) ) {
    function prmbr_shortcode() {
        _e( 'Add Countdown to your page or post </br> using the following shortcode:', 'promobar' );
        bws_shortcode_output( '[bws_countdown]' );
    }
}