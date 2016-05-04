( function( $ ) {
	$( window ).load( function() {
		$( window ).resize( function() {
			if ( $( '.prmbr_main' ).length > 0 ) {				
				/* remove the class for case of lack of js */
				$( '.prmbr_main' ).removeClass( 'prmbr_no_js' );

				if ( $( '.prmbr_main' ).hasClass( 'prmbr_top' ) ) {
					var height_prmbr_main = $( '.prmbr_main' ).css( 'height' );
					var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;					
					if ( is_twentyfourteen != 0 ) { 
						$( 'body' ).addClass( 'twentyfourteen_fix_head' );
					}

					/* shift the main content of a site if its location is top of the site and add padding if there adminpanel */
					$( 'body' ).css({ 'margin-top': height_prmbr_main });		

					/*for theme 2015 */				
					var is_twentyfourteen = $( '#twentyfourteen-style-css' ).length;
					if ( is_twentyfifteen != 0 ) { 
						$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
					}				
				} else if ( $( '.prmbr_main' ).hasClass( 'prmbr_left' ) || $( '.prmbr_main' ).hasClass( 'prmbr_right' ) ) {
					/* checks whether there is an adminpanel */
					var admin_element = $( '#wpadminbar' ).outerHeight( true );

					var width_prmbr_main = parseInt( $( '.prmbr_main' ).css( 'width' ) );
					var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;
					var is_twentyfourteen = $( '#twentyfourteen-style-css' ).length;

					if ( is_twentyfourteen != 0 ) { 
						var height_prmbr_main = $( 'html' ).outerHeight( true ) - admin_element;						
					} else {
						var height_prmbr_main = $( 'html' ).outerHeight( true );
					}			

					$( '.prmbr_main' ).css({ 'height': height_prmbr_main });
					
					if ( $( '.prmbr_main' ).hasClass( 'prmbr_left' ) ) {
						$( 'body' ).css({ 'margin-left': width_prmbr_main }); 
					} else {
						$( 'body' ).css({ 'margin-right': width_prmbr_main });
					}

					/* add padding if there adminpanel */
					if ( admin_element > 0 ) {
						$( '.prmbr_main' ).css({ 'padding-top': admin_element + 'px' });
					}
					
					/*for theme 2015 */				
					if ( is_twentyfifteen != 0 ) {
						var height_prmbr_main = $( 'html' ).outerHeight( true ) ;					
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
						$( '.prmbr_add_background' ).css({ 'width': resalt_sidebar, 'height': height_prmbr_main });
					}
				}
			}
		}).trigger( 'resize' );
	});
})( jQuery );