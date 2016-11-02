<?php
class Yikes_Inc_Easy_Mailchimp_EU_Compliance_Extension_Uninstaller {

	public static function uninstall() {
		global $wpdb;
		// define global switched (required for switch_to_blog())
		global $switched;		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// users can only unisntall a plugin from the network dashboard page
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::_uninstall_yikes_easy_mailchimp( $wpdb );
				restore_current_blog();
			}
			switch_to_blog( $old_blog );
			return;
		}
		self::_uninstall_yikes_easy_mailchimp( );
	}
	
	/**
	 * Short Description. Plugin Uninstall.
	 *
	 * Long Description. Removes our EU compliance data from the mc forms tables
	 *
	 * @since    0.1
	 */
	static function _uninstall_yikes_easy_mailchimp( ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		/* Clean up and delete our eu compliance fields from the yikes_easy_mailchimp_extender_forms options array */
		$mc_forms = get_option( 'yikes_easy_mailchimp_extender_forms' );

		foreach( $mc_forms as $form_id => $mailchimp_form ) {
			//unset the two eu-compliance related custom fields
			unset(
				$mc_forms[$form_id]['custom_fields']['eu-compliance-law-checkbox-text'], 
				$mc_forms[$form_id]['custom_fields']['eu-compliance-law-checkbox-precheck']
			);
		}
		//update options
		update_option( 'yikes_easy_mailchimp_extender_forms', $mc_forms );

		return;
	}
	
}
?>