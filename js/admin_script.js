( function( $ ) {
	$( 'document' ).ready( function() {
		/*All about admin part*/
		/* include color-picker */
		$( '.prmbr_color_field' ).wpColorPicker();
		var color_options = {
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
		$( '.prmbr_color_field' ).wpColorPicker( color_options );

		$( '.wp-picker-container' ).bind( 'change click select', function() {
			$( '#prmbr_settings_notice' ).css( 'display', 'block' );
		} );

		/* Display input fields for left and right promobar */
		var options = $( '.prmbr_option_affect' );
		if ( options.length ) {
			options.each( function() {
				var element = $( this );
				if ( element.is( ':checked' ) ) {
					$( element.data( 'affect-show' ) ).show();
					$( element.data( 'affect-hide' ) ).hide();
				} else {
					$( element.data( 'affect-show' ) ).hide();
					$( element.data( 'affect-hide' ) ).show();
				}
				element.closest( 'fieldset' ).on( 'change', function() {
					var affect_hide = element.data( 'affect-hide' ),
						affect_show = element.data( 'affect-show' );
					if ( element.is( ':checked' ) ) {
						$( affect_show ).show();
						$( affect_hide ).hide();
					} else {
						$( affect_show ).hide();
						$( affect_hide ).show();
					}
				});
			});
		}

		/* Checking width of the position */
		$( '.prmbr_emerging_options select' ).on( 'change', function() {
			var $input = $( this ).prev( 'input' );
			if ( '%' == $( this ).val() ) {
				$input.attr( 'max', '100' );
			} else {
				$input.removeAttr( 'max' );
			}
		} ).trigger( 'change' );

		$( '.prmbr_emerging_options' ).on( 'change', function() {
			var width_values = $( this ).children( 'input' );
			if ( '%' == $( this ).children( 'select' ).val() ) {
				if ( width_values.val() > 100 ) {
					width_values.val( 100 );
				} else if ( width_values.val() < 0 ) {
					width_values.val( 0 );
				}
			}
		} );
	} );
})( jQuery );
