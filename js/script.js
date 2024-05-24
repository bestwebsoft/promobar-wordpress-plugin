( function( $ ) {
	$( window ).load( function() {
		var promo_block = $( '.prmbr_main' ),
			prmbr_close_button_main = $( '#prmbr_close_button_main' );

		prmbr_close_button_main.click( function() {
			promo_block.remove();
			prmbr_close_button_main.remove();
			$( 'body' ).css( 'margin-left', '0' ).css( 'margin-right', '0' ).css( 'margin-top', '0' );
		} );
		$( window ).resize( function() {
			/* remove the class for case of lack of js */
			promo_block.removeClass( 'prmbr_no_js prmbr_no_js_logged' );

			var window_width = window.innerWidth;
			if ( promo_block.length ) {
				promo_block.each( function() {
				/* check using the date attributes of the location of the promobar. */
					/* if the prombar is located at the top, then add margin-top for <body> */
					var height_margin,
						desktop = promo_block.data( 'prmbr-position_desktop' ),
						tablet = promo_block.data( 'prmbr-position_tablet' ),
						mobile = promo_block.data( 'prmbr-position_mobile' ),
						all_resolutions = [ desktop, tablet, mobile ];

					if ( $.inArray( 'top', all_resolutions ) !== -1 ) {
						if ( ( 'top' === desktop && window_width > 768 ) ||
							( 'top' === tablet && window_width < 769 && window_width > 425 ) ||
							( 'top' === mobile && window_width < 426 )
						) {
							height_margin = promo_block.css( 'height' );
							$( 'body' ).css( { 'padding-top': height_margin } );
						}
					}

					if ( $.inArray( 'bottom', all_resolutions ) !== -1 ) {
						if ( ( 'bottom' === desktop && window_width > 768 ) ||
							( 'bottom' === tablet && window_width < 769 && window_width > 425 ) ||
							( 'bottom' === mobile && window_width < 426 )
						) {
							height_margin = promo_block.css( 'height' );
							$( 'body' ).css( { 'padding-bottom': height_margin } );
						}
					}

					/* if the prombar is located on the left or on the right, then add height for the promobar */
					if ( $.inArray( 'side', all_resolutions ) !== -1 ) {
						var height_prmbr_main,
							page_height = $( 'body' ).css( 'height' );
						if ( ( 'side' === desktop && window_width > 768 ) ||
							( 'side' === tablet && window_width < 769 && window_width > 425 ) ||
							( 'side' === mobile && window_width < 426 )
						) {
							height_prmbr_main = page_height;
						} else {
							height_prmbr_main = '';
						}
						promo_block.css( { 'height': height_prmbr_main } );
					}

					/*for theme 2015 */
					var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;
					if ( is_twentyfifteen && 'side' === desktop && window_width > 768 ) {
						var width_before = $( '.site-content' ).css( 'margin-left' );
						$( '<style>@media screen and (min-width: 59.6875em) { body:before { width:' + width_before + '; left: auto; } }</style>' ).appendTo( 'head' );
					}
				} );
			}
		} ).trigger( 'resize' );
	} );
} )( jQuery );





