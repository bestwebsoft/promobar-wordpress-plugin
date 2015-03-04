( function( $ ) {
	$( 'document' ).ready( function() {
		/* checks whether there is an adminpanel */
		var admin_element = $( '#wpadminbar' ).length;
		/* check whether there is a class for shortcode */
		var is_shortcode = $( '.prmbr_position_for_shortcode' ).length;
		/* if there is no class shortcode then execute the following items: */
		if ( is_shortcode == 0 ) {
			/* remove the class for case of lack of js */
			$( '.prmbr_main' ).removeClass( 'prmbr_no_js' );
			if ( $( '.prmbr_main' ).hasClass( 'prmbr_top' ) ) {
				var height_prmbr_main = $( '.prmbr_main' ).css( 'height' );
				/* shift the main content of a site if its location is top of the site and add padding if there adminpanel */
				$( 'body' ).css({ 'margin-top': height_prmbr_main });
				/* add padding if there adminpanel */
				if ( admin_element != 0 ) {
					$( '.prmbr_main' ).css({ 'padding-top': '32px' });
				}
				/*for theme 2015 */
				var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;
				if ( is_twentyfifteen != 0 ) { 
					$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
				}
			}
			/* shift the main content of a site if its location is left */
			if ( $( '.prmbr_main' ).hasClass( 'prmbr_left' ) ) {
				var width_prmbr_main = parseInt( $( '.prmbr_main' ).css( 'width' ) );
				var is_twentythirteen = $( '#twentythirteen-style-css' ).length;
				var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;
				$( window ).load( function() {
					if ( ( is_twentyfifteen != 0 ) || ( is_twentythirteen != 0 ) ) { 
						var height_prmbr_main = $( 'html' ).outerHeight( true );
					} else {
						var height_prmbr_main = $( 'html' ).outerHeight( true ) - 32 ;
					}
					$( '.prmbr_main' ).css({ 'height': height_prmbr_main });
				});
				$( 'body' ).css({ 'margin-left': width_prmbr_main }); 
				/* stretch the block on all height of the site of the site */
				
				/* add padding if there adminpanel */
				if ( admin_element != 0 ) {
					$( '.prmbr_main' ).css({ 'padding-top': '32px' });
				}
				/*for theme 2015 */
				
				if ( is_twentyfifteen != 0 ) {
					$( window ).load( function() {
						if ( admin_element != 0 ) {
							var height_prmbr_main = $( 'html' ).outerHeight( true ) - 32;
						} else {
							var height_prmbr_main = $( 'html' ).outerHeight( true );
						}
						$( '.add_background' ).css({ 'height': height_prmbr_main });
					});
					var width_body = parseInt( $( 'body' ).css( 'width' ) );
					var width_page = parseInt( $( '.site' ).css( 'width' ) );
					var add_to_width_sidebar = ( width_body - width_page ) / 2;
					var width_sidebar = parseInt( $( '#sidebar' ).css( 'width' ) );
					var resalt_sidebar = width_sidebar + add_to_width_sidebar;
					$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
					$( 'body' ).addClass( 'prmbr_for_sidebar' );
					$( '<div class="add_background"></div>' ).prependTo( $( 'body' ) );
					$( '.add_background' ).css({ 'width': resalt_sidebar });
				}
			}
			/* shift the main content of a site if its location is right */
			if ( $( '.prmbr_main' ).hasClass( 'prmbr_right' ) ) {
				var width_prmbr_main = parseInt( $( '.prmbr_main' ).css( 'width' ) );
				var is_twentythirteen = $( '#twentythirteen-style-css' ).length;
				var is_twentyfifteen = $( '#twentyfifteen-style-css' ).length;
				$( window ).load( function() {
					if ( ( is_twentyfifteen != 0 ) || ( is_twentythirteen != 0 ) ) { 
						var height_prmbr_main = $( 'html' ).outerHeight( true );
					} else {
						var height_prmbr_main = $( 'html' ).outerHeight( true ) - 32 ;
					}
					$( '.prmbr_main' ).css({ 'height': height_prmbr_main });
				});
				$( 'body' ).css({ 'margin-right': width_prmbr_main });
				$( '.prmbr_main' ).css({ 'right': '0' });
				/* stretch the block on all height of the site of the site */
				$( '.prmbr_main' ).css({ 'height': height_prmbr_main });
				/* add padding if there adminpanel */
				if ( admin_element != 0 ) {
					$( '.prmbr_main' ).css({ 'padding-top': '32px' });
				}
				/*for theme 2015 */
				if ( is_twentyfifteen != 0 ) {
					$( window ).load( function() {
						if ( admin_element != 0 ) {
							var height_prmbr_main = $( 'html' ).outerHeight( true ) - 32;
						} else {
							var height_prmbr_main = $( 'html' ).outerHeight( true );
						}
						$( '.add_background' ).css({ 'height': height_prmbr_main });
					});
					var width_body = parseInt( $( 'body' ).css( 'width' ) );
					var width_page = parseInt( $( '.site' ).css( 'width' ) );
					var add_to_width_sidebar = ( width_body - width_page ) / 2;
					var width_sidebar = parseInt( $( '#sidebar' ).css( 'width' ) );
					var resalt_sidebar = width_sidebar + add_to_width_sidebar;
					$( '#sidebar' ).addClass( 'prmbr_for_sidebar_background' );
					$( 'body' ).addClass( 'prmbr_for_sidebar' );
					$( '<div class="add_background"></div>' ).prependTo( $( 'body' ) );
					$( '.add_background' ).css({ 'width': resalt_sidebar });
				}
			}
		}
	});
})( jQuery );