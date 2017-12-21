( function( $ ) {
	$( window ).load( function() {
		$( window ).resize( function() {
			if ( $( '.prmbr_main' ).length > 0 ) {
				/* remove the class for case of lack of js */
				$( '.prmbr_main' ).removeClass( 'prmbr_no_js' );

				var is_twentyfourteen = $( '#twentyfourteen-style-css' ).length;
				var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;

				/* for theme 2014 */
				if ( 0 != is_twentyfourteen ) {
					/* when promobar is located in the top or bottom of the page it can fall out of the page. fixed in this way */
					if ( $( '.prmbr_block' ).hasClass( 'prmbr_top' ) || $( '.prmbr_block' ).hasClass( 'prmbr_bottom' ) ) {
						$( '.prmbr_top' ).width( $( '#main' ).width() );
						$( '.prmbr_bottom' ).width( $( '#main' ).width() );
					}

					/* adding correct margins to avoid overlapping of the menu, promobar and main content */
					if ( $( '.prmbr_block' ).hasClass( 'prmbr_top' ) ) {
						$( '.prmbr_top' ).offset( { top: $( '#wpadminbar' ).outerHeight( true ) + $( '.header-main' ).outerHeight( true ) } );
						$( '.prmbr_top' ).css( 'z-index', '3' );
						$( '#secondary' ).offset( { top: $( '#wpadminbar' ).outerHeight( true ) + $( '.header-main' ).outerHeight( true ) + $( '#search-container' ).outerHeight( true ) } );
					}
				}

				if ( $( '.prmbr_main' ).hasClass( 'prmbr_top' ) ) {
					var height_prmbr_main = $( '.prmbr_main' ).css( 'height' );

					/* shift the main content of a site if its location is top of the site and add padding if there adminpanel */
					$( 'body' ).css( { 'margin-top': height_prmbr_main } );

					/*for theme 2015 */
					if ( 0 != is_twentyfifteen ) {
						$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
					}
				} else if ( $( '.prmbr_main' ).hasClass( 'prmbr_left' ) || $( '.prmbr_main' ).hasClass( 'prmbr_right' ) ) {
					/* checks whether there is an adminpanel */
					var admin_element = $( '#wpadminbar' ).outerHeight( true );

					var width_prmbr_main = parseInt( $( '.prmbr_main' ).css( 'width' ) );

					if ( $( '.prmbr_main' ).hasClass( 'prmbr_left' ) ) {
						$( 'body' ).css( { 'margin-left': width_prmbr_main } );
					} else {
						$( 'body' ).css( { 'margin-right': width_prmbr_main } );
					}

					if ( 0 != is_twentyfourteen ) {
						var height_prmbr_main = $( document ).outerHeight( true ) - admin_element;
					} else {
						var height_prmbr_main = $( document ).outerHeight( true );
					}

					$( '.prmbr_main' ).css( { 'height': height_prmbr_main } );

					/* add padding if there adminpanel */
					if ( admin_element > 0 ) {
						$( '.prmbr_main' ).css( { 'padding-top': ( admin_element + 10 ) + 'px' } );
					}

					/*for theme 2015 */
					if ( 0 != is_twentyfifteen ) {
						var height_prmbr_main = $( 'html' ).outerHeight( true );
						var width_body = parseInt( $( 'body' ).css( 'width' ) );
						var width_page = parseInt( $( '.site' ).css( 'width' ) );
						var add_to_width_sidebar = ( width_body - width_page ) / 2;
						var width_sidebar = parseInt( $( '#sidebar' ).css( 'width' ) );
						var resalt_sidebar = width_sidebar + add_to_width_sidebar;
						$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
						$( 'body' ).addClass( 'prmbr_for_sidebar' );
						if ( $( '.prmbr_add_background' ).length < 1 ) {
							$( '<div class="prmbr_add_background"></div>' ).prependTo( $( 'body' ) );
						}
						$( '.prmbr_add_background' ).css( { 'width': resalt_sidebar, 'height': height_prmbr_main } );
					}
				}
			}
		} ).trigger( 'resize' );
	} );
})( jQuery );
