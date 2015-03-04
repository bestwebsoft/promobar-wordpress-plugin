( function( $ ) {
	$( 'document' ).ready( function() {
		/*All about admin part*/
		/* include color-picker */
		$( '.prmbr_color_field' ).wpColorPicker();
		var myOptions = {
			/* you can declare a default color here, or in the data-default-color attribute on the input*/
			/* defaultColor: false,*/
			/* a callback to fire whenever the color changes to a valid color*/
			change: function(event, ui){},
			/* a callback to fire when the input is emptied or an invalid color*/
			clear: function() {},
			/* hide the color picker controls on load*/
			hide: true,
			/* show a group of common colors beneath the square or, supply an array of colors to customize further*/
			palettes: true
		};
		$( '.prmbr_color_field' ).wpColorPicker(myOptions);

		/* show notice if was change */
		$( '#prmbr_settings_form input' ).bind( 'change click select', function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#prmbr_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '.wp-picker-container' ).bind( 'change click select', function() {
			$( '#prmbr_settings_notice' ).css( 'display', 'block' );
		});
		/* display input for width */
		$( '#prmbr_position4' ).bind( 'change click select', function() {
			$( '#prmbr_width_position4' ).removeClass( 'prmbr_width_disabled' );
			$( '#prmbr_width_position3' ).addClass( 'prmbr_width_disabled' );
		});
		$( '#prmbr_position3' ).bind( 'change click select', function() {
			$( '#prmbr_width_position3' ).removeClass( 'prmbr_width_disabled' );
			$( '#prmbr_width_position4' ).addClass( 'prmbr_width_disabled' );
		});
		$( '#prmbr_position2, #prmbr_position1' ).bind( 'change click select', function() {
			$( '#prmbr_width_position3' ).addClass( 'prmbr_width_disabled' );
			$( '#prmbr_width_position4' ).addClass( 'prmbr_width_disabled' );
		});		
	});
})( jQuery );