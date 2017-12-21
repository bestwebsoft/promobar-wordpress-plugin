( function( $ ) {
	$( 'document' ).ready( function() {
		/*All about admin part*/
		/* include color-picker */
		$( '.prmbr_color_field' ).wpColorPicker();
		var myOptions = {
			/* you can declare a default color here, or in the data-default-color attribute on the input*/
			/* defaultColor: false,*/
			/* a callback to fire whenever the color changes to a valid color*/
			change: function( event, ui ) {},
			/* a callback to fire when the input is emptied or an invalid color*/
			clear: function() {},
			/* hide the color picker controls on load*/
			hide: true,
			/* show a group of common colors beneath the square or, supply an array of colors to customize further*/
			palettes: true
		};
		$( '.prmbr_color_field' ).wpColorPicker( myOptions );

		$( '.wp-picker-container' ).bind( 'change click select', function() {
			$( '#prmbr_settings_notice' ).css( 'display', 'block' );
		} );

		/* display input fields for left and right promobar */
		$( '[name=prmbr_position]' ).on( 'change', function() {
			var parent = $( this ).closest( 'label' );
			parent.closest('fieldset').find( 'input:not(:checked) + span' ).hide().find('input:text').attr( 'disabled', true );
			if ( $( this ).is( ':checked' ) ) {
				parent.find( 'span' ).show().find( 'input' ).removeAttr( 'disabled' );
			}
		} ).trigger( 'change' );
	} );
})( jQuery );
