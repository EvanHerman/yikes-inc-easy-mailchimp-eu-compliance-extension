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
		self::_uninstall_yikes_easy_mailchimp( $wpdb );
	}
	
	/**
	 * Short Description. Plugin Uninstall.
	 *
	 * Long Description. Removes our EU compliance data from the mc forms tables
	 *
	 * @since    0.1
	 */
	static function _uninstall_yikes_easy_mailchimp( $wpdb ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		/* Clean up and delete our custom table from the databse */
		$mc_forms = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );
		foreach( $mc_forms as $mailchimp_form ) {
			$custom_fields = json_decode( $mailchimp_form['custom_fields'], true );
			// delete the two eu-compliance fields out of our custom_fields array
			unset( $custom_fields['eu-compliance-law-checkbox-text'], $custom_fields['eu-compliance-law-checkbox-precheck'] );
		}
		return;
	}
	
}
?>