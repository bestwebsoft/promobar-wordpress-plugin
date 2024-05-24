( function( $ ) {
	$( 'document' ).ready( function() {
		$( function() {
			$( '#prmbr_tabs' ).tabs();
		} );
		function prmbr_datepicker() {
			if ( $( '.prmbr_inputs_date:visible' ).length ) {
				$( '.prmbr_inputs_date:visible' ).datepicker( {
					dateFormat : 'dd-mm-yy'
				} );
			}
		}
		$( '#prmbr_show_promobar_dismiss_button' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.prmbr_enable_icon_color, .prmbr_enable_icon_size, .prmbr_enable_icon_position ' ).show();
			} else {
				$( '.prmbr_enable_icon_color, .prmbr_enable_icon_size, .prmbr_enable_icon_position ' ).hide();
			}
		});

		$( '.prmbr_file_add_button' ).on( 'click', function( event ) {
			event.preventDefault();
			var prmbr_count_of_files = $( '.prmbr_file_fields_place .prmbr-form-table:not( .prmbr_file_fields_template )' ).length,
				new_row = $( '.prmbr_file_fields_template' ).clone().removeClass( 'prmbr_file_fields_template' );

			new_row.html( new_row.html().replace( /NUMB/g, prmbr_count_of_files ) );
			new_row.find( 'input' ).removeAttr( 'disabled' ).filter( 'not:checkbox' ).attr( 'required', 'required' );

			$( '.prmbr_file_fields_place' ).append( new_row.show() );
			prmbr_datepicker();
		} );
		$( '.prmbr_file_delete_button' ).on( 'click', function( event ) {
			event.preventDefault();
			$( this ).closest( 'table' ).hide( 'slow', function() {
				$( this ).remove();
			} );
		} );
		prmbr_datepicker();

		/*All about admin part*/
		/* include color-picker */
		var prmbr_color_field = $( '.prmbr_color_field' );

		prmbr_color_field.wpColorPicker();
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
		prmbr_color_field.wpColorPicker( color_options );

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
				} );
			} );
		}
		
		$( '.prmbr_option_affect_columns' ).each( function() {
			var element = $( this );
			if ( element.is( ':checked' ) ) {
				$( element.data( 'affect-show' ) ).show();
				$( element.data( 'affect-hide' ) ).hide();
			} else {
				$( element.data( 'affect-show' ) ).hide();
				$( element.data( 'affect-hide' ) ).show();
			}
			if ($( '.prmbr_position_column_desktop' ).is( ':hidden' ) && $( '.prmbr_position_column_tablet' ).is( ':hidden' ) && $( '.prmbr_position_column_mobile' ).is( ':hidden' ) )  {
				$( '.prmbr_header_alignment' ).hide();
			} else if ($( '.prmbr_position_column_desktop' ).is( ':visible' ) || $( '.prmbr_position_column_tablet' ).is( ':visible' ) || $( '.prmbr_position_column_mobile' ).is( ':visible' ) ) {
				$( '.prmbr_header_alignment' ).show();
			}
			element.on( 'change', function() {
					var affect_hide = element.data( 'affect-hide' ),
						affect_show = element.data( 'affect-show' );
					if ( element.is( ':checked' ) ) {
						$( affect_show ).show();
						$( affect_hide ).hide();
					} else {
						$( affect_show ).hide();
						$( affect_hide ).show();
					}
					if ($( '.prmbr_position_column_desktop' ).is( ':hidden' ) && $( '.prmbr_position_column_tablet' ).is( ':hidden' ) && $( '.prmbr_position_column_mobile' ).is( ':hidden' ) )  {
						$( '.prmbr_header_alignment' ).hide();
					} else if ($( '.prmbr_position_column_desktop' ).is( ':visible' ) || $( '.prmbr_position_column_tablet' ).is( ':visible' ) || $( '.prmbr_position_column_mobile' ).is( ':visible' ) ) {
						$( '.prmbr_header_alignment' ).show();
					}
				});
			})
		/* Checking width of the position */
		$( '.prmbr_emerging_options select' ).on( 'change', function() {
			var $input = $( this ).prev( 'input' );
			if ( '%' === $( this ).val() ) {
				$input.attr( 'max', '100' );
			} else {
				$input.removeAttr( 'max' );
			}
		} ).trigger( 'change' );

		$( '.prmbr_emerging_options' ).on( 'change', function() {
			var widthValues = $( this ).children( 'input' );
			if ( '%' === $( this ).children( 'select' ).val() ) {
				if ( widthValues.val() > 100 ) {
					widthValues.val( 100 );
				} else if ( widthValues.val() < 0 ) {
					widthValues.val( 0 );
				}
			}
		} ).trigger( 'change' );

		$( 'input[name="prmbr_background"]' ).on( 'change', function() {
			var background_color_div = $('.background_color > div'),
				upload_image = $('.upload-image');
			if ( $( this ).is( ':checked' ) ) {
				switch( $( this ).val() ) {
					case 'transparent':
						background_color_div.hide();
						upload_image.hide();
						break;
					case 'color':
						background_color_div.show();
						upload_image.hide();
						break;
					case 'image':
						background_color_div.hide();
						upload_image.show();
						break;
				}
			}
		} ).trigger( 'change' );
		if ( $( '.prmbr-upload-image' ).length > 0 ) {

			/**
			 * include WordPress media uploader for images
			 */
			var file_frame,
				wp_media_post_id = wp.media.model.settings.post.id, /* Store the old id */
				set_to_post_id   = 0; /* Set this */
			$( '.prmbr-upload-image' ).on( 'click', function( event ) {
				var imageUrl = $( this ).parent().find( 'input.prmbr-image-url' );

				event.preventDefault();

				/* If the media frame already exists, reopen it. */
				if ( file_frame ) {
					/* Set the post ID to what we want */
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					/* Open frame */
					file_frame.open();
					return;
				} else {
					/* Set the wp.media post id so the uploader grabs the ID we want when initialised */
					wp.media.model.settings.post.id = set_to_post_id;
				}

				/* Create the media frame. */
				file_frame = wp.media.frames.file_frame = wp.media( {
					title:    $( this ).data( 'uploader_title' ),
					library:  {
						type: 'image'
					},
					button:   {
						text: $( this ).data( 'uploader_button_text' )
					},
					multiple: false /* Set to true to allow multiple files to be selected */
				} );

				/* When an image is selected, run a callback. */
				file_frame.on( 'select', function() {
					/* We set multiple to false so only get one image from the uploader */
					var attachment = file_frame.state().get( 'selection' ).first().toJSON();

					/* Do something with attachment.id and/or attachment.url here */
					imageUrl.val( attachment.url ).trigger( 'change' );

					/* Restore the main post ID */
					wp.media.model.settings.post.id = wp_media_post_id;
				} );

				/* Finally, open the modal */
				file_frame.open();
			} );
		}
	} );
} )( jQuery );