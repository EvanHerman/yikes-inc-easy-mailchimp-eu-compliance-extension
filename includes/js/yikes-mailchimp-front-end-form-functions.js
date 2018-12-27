( function( $ ) {

	$( 'body' ).on( 'submit' , '.yikes-easy-mc-form' , function() {
		const form     = $( this );
		const checkbox = form.find( 'input[name="eu-laws"]' );
		form.on( 'yikes_clear_input_fields_after_successful_submission', function() {
			checkbox.prop( 'checked', false );
		});
	});

})( jQuery );