
( function( $ ) {

	// Document.ready
	$( document ).ready( function() {

		// Add some HTML to the page to hold our character counter
		if ( jQuery( '#yikes-mailchimp-character-count' ).length === 0 ) {
			let character_count_html = ' Character Count: <span id="yikes-mailchimp-character-count"></span>';
			jQuery( '#wp-eu-compliance-law-checkbox-text-wrap' ).siblings( '.description' ).html( jQuery( '#wp-eu-compliance-law-checkbox-text-wrap' ).siblings( '.description' ).html() + character_count_html );

			// Show the initial character count
			add_character_count( get_initial_character_count() ); 
		}


		// Character count listener for "text" tab
		jQuery( 'body' ).on( 'keyup', '#eu-compliance-law-checkbox-text', function() {
			get_character_count_no_tags( this.value );
		});

		// Don't init our TinyMCE counter until the section is clicked.
		// This should prevent us trying to hook into the editor before it's initialized.
		jQuery( 'body' ).on( 'click', '.eu-law-compliance-section', function() {

			// Character count listener for "visual" tab
			if ( typeof tinymce.get( 'eu-compliance-law-checkbox-text' ) === 'object' && tinymce.get( 'eu-compliance-law-checkbox-text' ) ) {

				// Bind a key up handler
				tinymce.get( 'eu-compliance-law-checkbox-text' ).onKeyUp.addToTop( function() {
					console.log( tinymce.get( 'eu-compliance-law-checkbox-text' ).getContent().length );
					get_character_count_no_tags( tinymce.get( 'eu-compliance-law-checkbox-text' ).getContent() );
				});
			}
		});

		show_notification_for_opt_in_merge_field();
		jQuery( 'select[name="custom-field[eu-compliance-law-save-opt-in-field]"]' ).change( function() {
			show_notification_for_opt_in_merge_field();
		});
	});

	function show_notification_for_opt_in_merge_field() {

		jQuery( '.field-used-by-eu-icon' ).remove();

		let field = jQuery( 'select[name="custom-field[eu-compliance-law-save-opt-in-field]"]' ).val();
		if ( field && jQuery( '.available-form-field[alt="' + field + '"]' ).length === 1 ) {
			jQuery( '.available-form-field[alt="' + field + '"]' ).append( '<span class="dashicons dashicons-info field-used-by-eu-icon" title="This field is being used to store opt-in compliance."></span>' );
		}
	}

	function get_initial_character_count() {
		return jQuery( '#eu-compliance-law-checkbox-text' ).val().length;
	}

	function get_character_count_no_tags( content ) {

		let div  = document.createElement( "div" );
		div.innerHTML = content;
		let text = div.textContent || div.innerText || "";
		add_character_count( text.length );
	}

	function add_character_count( char_count ) {

		jQuery( '#yikes-mailchimp-character-count' ).text( char_count );

		char_count > 1000 ? jQuery( '#yikes-mailchimp-character-count' ).css( 'color', 'red' ) : jQuery( '#yikes-mailchimp-character-count' ).removeAttr( 'style' );
	}

})( jQuery );