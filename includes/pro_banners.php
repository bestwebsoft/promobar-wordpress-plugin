<?php
/**
 * Banners on plugin settings page
 * @package Promobar by BestWebSoft
 * @since 0.1
 */

if ( ! function_exists( 'prmbr_countdown_block' ) ) {
	function prmbr_countdown_block() { ?>   
		<table class="form-table prmbr-form-table">
			<tbody>
	            <tr>
	                <th scope="row"><?php _e( 'Countdown Ends on', 'promobar' ); ?></th>
	                <td>
	                    <input class="prmbr_inputs_date prmbr_date" type="text" name="prmbr_end_date" value="" />
	                    <div class="bws_info"><?php _e( 'Enter end date for countdown shortcode.', 'promobar' ); ?></div>
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

if ( ! function_exists( 'prmbr_title' ) ) {
    function prmbr_title( $prmbr_options ) { ?>
        <div id="titlediv">
			<div id="titlewrap">
				<input disabled="disabled" name="prmbr_items_title" size="30" value="Promobar" id="title" spellcheck="true" autocomplete="off" type="text" placeholder="<?php _e( 'Enter title here', 'promobar' ); ?>" />
			</div>
			<div class="inside"></div>
		</div>
    <?php }
}

if ( ! function_exists( 'prmbr_date' ) ) {
    function prmbr_date( $prmbr_options ) { ?>
    	<table class="form-table prmbr-form-table">
			<tbody>
				<tr>
			        <th scope="row"><?php _e( 'Promobar Starts on', 'promobar' ); ?></th>
			        <td>
			            <input disabled="disabled" class="prmbr_inputs_date prmbr_date" id="prmbr_start_date" type="text" name="" value="24-08-2021" />
			            <div class="bws_info"><?php _e( 'Enter to display a promo bar after this date', 'promobar' ); ?>.</div>
			        </td>
			    </tr>
			    <tr>
			        <th scope="row"><?php _e( 'Promobar Ends on', 'promobar' ); ?></th>
			        <td>
			            <input disabled="disabled" class="prmbr_inputs_date prmbr_date" id="prmbr_end_date" type="text" name="" value="23-09-2021" />
			            <div class="bws_info"><?php _e( 'Enter to hide a promo bar after this date', 'promobar' ); ?>.</div>
			        </td>
			    </tr> 
			</tbody>
		</table>
    <?php }
}

if ( ! function_exists( 'prmbr_popup' ) ) {
    function prmbr_popup( $prmbr_options ) { ?>
		<label for="prmbr_popup">
            <input disabled="disabled" type="radio" id="prmbr_popup" name="" value="popup" class="bws_option_affect" data-affect-show=".prmbr_time_delay" data-affect-hide=".prmbr_alignment"  /> <?php _e( 'popup', 'promobar' ); ?>                             
        </label>
    <?php }
}

if ( ! function_exists( 'prmbr_shortcode' ) ) {
    function prmbr_shortcode() {
        _e( 'Add Countdown to your page or post </br> using the following shortcode:', 'promobar' );
        bws_shortcode_output( '[bws_countdown]' );
    }
}