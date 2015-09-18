<?php
/**
 * 		Plugin Name:       	Easy Forms for MailChimp EU Law Compliance Extension by YIKES
 * 		Plugin URI:       		http://www.yikesinc.com
 * 		Description:       	This extension adds a checkbox to all of your MailChimp forms to ensure you are following the EU laws.
 * 		Version:          	 	0.1
 * 		Author:            		Yikes Inc.
 * 		Author URI:        	http://www.yikesinc.com
 * 		License:          	 	GPL-2.0+
 * 		License URI:       	http://www.gnu.org/licenses/gpl-2.0.txt
 * 		Text Domain:       	yikes-inc-easy-mailchimp-eu-law-compliance
 * 		Domain Path:       	/languages
 *		
 * 		YIKES Easy Forms for MailChimp EU Law Compliance Extension is free software: you can redistribute it and/or modify
 * 		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation, either version 2 of the License, or
 * 		any later version.
 *
 * 		YIKES Easy Forms for MailChimp EU Law Compliance Extension  is distributed in the hope that it will be useful,
 * 		but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * 		GNU General Public License for more details.
 *
 * 		You should have received a copy of the GNU General Public License
 *		along with Easy Forms for MailChimp. If not, see <http://www.gnu.org/licenses/>.
 *
 *		We at Yikes Inc. embrace the open source philosophy on a daily basis. We donate company time back to the WordPress project,
 *		and constantly strive to improve the WordPress project and community as a whole. We eat, sleep and breath WordPress.
 *
 *		"'Free software' is a matter of liberty, not price. To understand the concept, you should think of 'free' as in 'free speech,' not as in 'free beer'."
 *		- Richard Stallman
 *
**/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// must include plugin.php to use is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! is_plugin_active( 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php' ) ) {
	deactivate_plugins( '/yikes-inc-easy-mailchimp-eu-law-compliance/yikes-inc-easy-mailchimp-eu-law-compliance.php' );
	add_action( 'admin_notices' , 'yikes_inc_mailchimp_eu_law_compliance_display_activation_error' );
}

function yikes_inc_mailchimp_eu_law_compliance_display_activation_error() {
	?>	
		<!-- hide the 'Plugin Activated' default message -->
		<style>
		#message.updated {
			display: none;
		}
		</style>
		<!-- display our error message -->
		<div class="error">
			<p><?php _e( 'Easy MailChimp EU Law Compliance Extension by Yikes Inc. could not be activated because the base plugin is not installed and active.', 'yikes-inc-easy-mailchimp-customizer-extension' ); ?></p>
			<p><?php printf( __( 'Please install and activate %s before activating this extension.', 'yikes-inc-easy-mailchimp-customizer-extension' ) , '<a href="' . esc_url_raw( admin_url( 'plugin-install.php?tab=search&type=term&s=Yikes+Inc.+Easy+MailChimp+Forms' ) ) . '" title="Yikes Inc. Easy MailChimp Forms">Yikes Inc. Easy MailChimp Forms</a>' ); ?></p>
		</div>
	<?php
}	
/* end check */

/********* Begin Main Plugin ***********/
class Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension {

	/* Construction of our main class */
	public function __construct() {
		// add our incentives link to the edit form page
		add_action( 'yikes-mailchimp-edit-form-section-links' , array( $this , 'add_eu_law_compliance_section_links' ) );
		// add the incentives section to the edit form page
		add_action( 'yikes-mailchimp-edit-form-sections' , array( $this , 'render_eu_compliance_section' ) );
		// include our scripts & styles on the admin
		add_action( 'admin_enqueue_scripts' , array( $this, 'enqueue_yikes_mailchimp_eu_compliance_admin_styles' ) );
		// include our scripts & styles on the frontend
		add_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles' , array( $this, 'enqueue_yikes_mailchimp_eu_compliance_frontend_styles' ) );
		// render our checkbox after the form
		add_action( 'yikes-mailchimp-additional-form-fields' , array( $this, 'render_frontend_compliance_checkbox' ) );
	}		
	
	/* 
	*	Enqueue our styles on the dashboard
	*	@since 0.1
	*/
	public function enqueue_yikes_mailchimp_eu_compliance_admin_styles( $hook ) {
		if( 'admin_page_yikes-mailchimp-edit-form' == $hook ) {
			wp_enqueue_style( 'yikes-mailchimp-eu-compliance-styles' , plugin_dir_url(__FILE__) . 'includes/css/yikes-mailchimp-eu-law-icons.css' );
		}
	}
	
	/* 
	*	Enqueue our styles on the frontend
	*	@since 0.1
	*/
	public function enqueue_yikes_mailchimp_eu_compliance_frontend_styles( $hook ) {
		wp_enqueue_style( 'yikes-mailchimp-eu-frontend-compliance-styles' , plugin_dir_url(__FILE__) . 'includes/css/yikes-mailchimp-eu-law-extension-frontend.css' );
	}
	
	/*
	*	Render the custom checkbox on the front end of the site
	*	hook into yikes-mailchimp-additional-form-fields
	*	@since 0.1
	*/
	public function render_frontend_compliance_checkbox( $form_data ) {
		$custom_field_data = json_decode( $form_data['custom_fields'], true );
		echo '<label class="yikes-mailchimp-eu-compliance-label">' . apply_filters( 'the_content', '<input type="checkbox" required="required" name="eu-laws" value="1" '. checked( $custom_field_data['eu-compliance-law-checkbox-precheck'], 1, false ) .'> ' . $custom_field_data['eu-compliance-law-checkbox-text'] ) . '</label>';
	}
	
	/*
	*	Adding additional sections & fields to the edit form screen
	*	@since 0.1
	*/
		
		/* Add custom link to the links */
		public function add_eu_law_compliance_section_links() {
			// creating a new link on the edit form page
			Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section_link( array(
				'id' => 'eu-law-compliance-section', // section id
				'text' => __( 'EU Law Compliance', 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // the text that will display in the link
				'icon_family' => 'custom',
				'icon' => 'yikes-mailchimp-eu-law' // dashicon icon class
			) );
		}
		
		/* Add custom section associated with link above */
		public static function render_eu_compliance_section() {	
		
			// defining a new section, associated with the link above
			Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section( array(
				'id' => 'eu-law-compliance-section',  // section id (must match link id above)
				'main_title' => __( 'EU Law Compliance', 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // title of the main block of this custom section
				'main_description' => __( 'A check box will display below your form asking new users to confirm addition to your mailing list.' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ),
				'main_fields' => array(
					array(
						'label' => __( 'Pre-check Compliance Checkbox' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // label text for this field
						'type' => 'select', // type of field (text,select,checkbox,radio)
						'options' => array(
							'1' => __( 'Yes', 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ),
							'2' => __( 'No',  'yikes-inc-easy-mailchimp-eu-law-compliance-extension' )
						),
						'id' => 'eu-compliance-law-checkbox-precheck', // field id - determines how data is saved in database
						'description' => __( 'Should this check box be pre-checked on initial page load?.' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // field description  
					),
					array(
						'label' => __( 'Compliance Checkbox Text' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // label text for this field
						'type' => 'wysiwyg', // type of field (text,select,checkbox,radio)
						'id' => 'eu-compliance-law-checkbox-text', // field id - determines how data is saved in database
						'default' => sprintf( __( 'Please check the checkbox to ensure that you comply with the %s.' ), '<a href="' . esc_url( 'http://www.lsoft.com/resources/optinlaws.asp' ). '" title="' . __( 'Europen Optin Laws' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ) . '" target="_blank">' . __( 'EU Laws' , 'yikes-inc-easy-mailchimp-eu-law-compliance/yikes-inc-easy-mailchimp-eu-law-compliance' ) . '</a>' ),
						'description' => __( 'Add custom label text for this check box.' , 'yikes-inc-easy-mailchimp-eu-law-compliance-extension' ), // field description  
					),
				),
			) );
		}
					
	/*
	*	End additional sections & fields
	*/
	
}

/*
*	Ensure base class is loaded first
*/
add_action( 'plugins_loaded' , 'load_eu_compliance_law_extension' );
function load_eu_compliance_law_extension() {	
	if( class_exists( 'Yikes_Inc_Easy_Mailchimp_Forms_Admin' ) ) {
		new Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension;
	}
}
						
/**
 * The code that runs during plugin uninstallation.
 */
register_uninstall_hook( __FILE__, 'uninstall_yikes_inc_easy_mailchimp_eu_compliance_extension' );
function uninstall_yikes_inc_easy_mailchimp_eu_compliance_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yikes-inc-easy-mailchimp-eu-compliance-extension-uninstall.php';
	Yikes_Inc_Easy_Mailchimp_EU_Compliance_Extension_Uninstaller::uninstall();
}	