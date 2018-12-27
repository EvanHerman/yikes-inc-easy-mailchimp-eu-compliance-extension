<?php
/**
 * Plugin Name: GDPR Compliance for MailChimp
 * Plugin URI:  http://www.yikesinc.com
 * Description: This extends Easy Forms for MailChimp to help make forms comply with The EU General Data Protection Regulation (GDPR).
 * Version:     1.2.3
 * Author:      YIKES, Inc.
 * Author URI:  http://www.yikesinc.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: eu-opt-in-compliance-for-mailchimp
 * Domain Path: /languages
 *
 * EU Opt-In Compliance for MailChimp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * EU Opt-In Compliance for MailChimp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Forms for MailChimp. If not, see <http://www.gnu.org/licenses/>.
 *
 * We at YIKES Inc. embrace the open source philosophy on a daily basis. We donate company time back to the WordPress project,
 * and constantly strive to improve the WordPress project and community as a whole. We eat, sleep and breath WordPress.
 *
 * "'Free software' is a matter of liberty, not price. To understand the concept, you should think of 'free' as in 'free speech,' not as in 'free beer'."
 * - Richard Stallman
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// must include plugin.php to use is_plugin_active().
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! is_plugin_active( 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php' ) ) {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	add_action( 'admin_notices', 'yikes_inc_mailchimp_eu_law_compliance_display_activation_error' );
	return;
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
			<p><?php _e( 'GDPR Compliance for MailChimp could not be activated because the base plugin is not installed and active.', 'eu-opt-in-compliance-for-mailchimp' ); ?></p>
			<p><?php printf( __( 'Please install and activate %s before activating this extension.', 'eu-opt-in-compliance-for-mailchimp' ), '<a href="' . esc_url_raw( admin_url( 'plugin-install.php?tab=search&type=term&s=Yikes+Inc.+Easy+MailChimp+Forms' ) ) . '" title="Easy Forms for MailChimp">Easy Forms for MailChimp</a>' ); ?></p>
		</div>
	<?php
}
/* end check */

/********* Begin Main Plugin ***********/
class Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension {

	/* Construction of our main class */
	public function __construct() {

		// add our incentives link to the edit form page.
		add_action( 'yikes-mailchimp-edit-form-section-links', array( $this, 'add_eu_law_compliance_section_links' ) );

		// add the incentives section to the edit form page.
		add_action( 'yikes-mailchimp-edit-form-sections', array( $this, 'render_eu_compliance_section' ) );

		// include our scripts & styles on the admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_yikes_mailchimp_eu_compliance_admin_styles_scripts' ) );

		// include our scripts & styles on the frontend.
		add_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles', array( $this, 'enqueue_yikes_mailchimp_eu_compliance_frontend_assets' ) );

		// render our checkbox after the form.
		add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'render_frontend_compliance_checkbox' ), 10, 1 );

		// Add the WYSIWYG text as a note on the user's form submission.
		add_filter( 'yikes-mailchimp-form-submission', array( $this, 'submit_checkbox_compliance_merge_field' ), 10, 4 );

		// Add the opt-in value as a MERGE field on the subscriber's submission.
		add_filter( 'yikes-mailchimp-filter-before-submission', array( $this, 'add_checkbox_optin_merge_field' ), 10, 1 );

		// Server side logic to make sure the checkbox was checked.
		add_filter( 'yikes-mailchimp-filter-before-submission', array( $this, 'maybe_fail_if_checkbox_not_checked' ), 1, 1 );

		// set the locale.
		$this->set_locale();
	}

	public function maybe_fail_if_checkbox_not_checked( $merge_variables ) {
		parse_str( $_POST['form_data'], $data );
		$checkbox_required = $this->is_checkbox_required();
		if ( ! isset( $data['eu-laws'] ) && ! empty( $is_checkbox_required ) ) {
			$merge_variables['error']   = true;
			$merge_variables['message'] = apply_filters( 'yikes_mailchimp_eu_compliance_checkbox_required_message', __( 'Please give your consent to subscribe to this list by checking the checkbox.', 'eu-opt-in-compliance-for-mailchimp' ), $merge_variables );	
		}
		
		return $merge_variables;
	}

	private function is_checkbox_required() {
		return apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-required', true );
	}

	/**
	 * Populate a new merge field with the value of the opt-in field specified in the admin
	 */
	public function add_checkbox_optin_merge_field( $merge_variables ) {

		$form_id = isset( $_POST['form_id'] ) ? $_POST['form_id'] : '';

		if ( empty( $form_id ) ) {
			return $merge_variables;
		}

		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form_id );

		// Get this form's custom fields.
		$custom_fields = $this->get_custom_field_data( $form_data );

		$opt_in_field = isset( $custom_fields['eu-compliance-law-save-opt-in-field'] ) ? $custom_fields['eu-compliance-law-save-opt-in-field'] : false;
		$opt_in_value = isset( $custom_fields['eu-compliance-law-save-opt-in-value'] ) ? $custom_fields['eu-compliance-law-save-opt-in-value'] : false;

		// Make sure we have data...
		if ( empty( $opt_in_field ) || empty( $opt_in_value ) ) {
			return $merge_variables;
		}

		$merge_variables[ $opt_in_field ] = $opt_in_value;

		return $merge_variables;
	}

	/**
	 * Add a new note containing the Checkbox Compliance language to a user's profile when they subscribe. 
	 */
	public function submit_checkbox_compliance_merge_field( $email, $merge_variables, $form_id, $notifications ) {

		// Get the list ID from the form ID.
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form_id );

		// Get this form's custom fields.
		$custom_fields = $this->get_custom_field_data( $form_data );

		$save_notes = isset( $custom_fields['eu-compliance-law-checkbox-save-text'] ) ? $custom_fields['eu-compliance-law-checkbox-save-text'] : false;

		// Convert the email.
		$email = md5( strtolower( $email ) );

		// Get the list id.
		$list_id = isset( $form_data['list_id'] ) ? $form_data['list_id'] : null;

		// Get the list handler.
		$list_handler = yikes_get_mc_api_manager()->get_list_handler();

		// Make sure we have data...
		if ( empty( $email ) || empty( $list_id ) || empty( $save_notes ) ) {
			return;
		}

		// Get the notes value.
		$notes_text = isset( $custom_fields['eu-compliance-law-checkbox-text'] ) ? strip_tags( $custom_fields['eu-compliance-law-checkbox-text'] ) : '';
		$notes_data = array(
			'note' => $notes_text,
		);

		// Make sure our create_member_note method exists.
		// It was added in Easy Forms 6.4.2.
		if ( ! method_exists( $list_handler, 'create_member_note' ) ) {
			if ( class_exists( 'Yikes_Inc_Easy_Mailchimp_Error_Logging' ) ) {
				$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
				if ( method_exists( $error_logging, 'maybe_write_to_log' ) ) {
					$error_logging->maybe_write_to_log( 'Method does not exist. Are you using at least v6.4.2 of Easy Forms?', __( 'Create member note', 'eu-opt-in-compliance-for-mailchimp' ), 'yikes-inc-easy-mailchimp-eu-law-compliance-extension.php' );
				}
			}
			return;
		}

		// Create a note.
		$note_response = $list_handler->create_member_note( $list_id, $email, $notes_data );

		// If there's an error, log it using the base plugin's logging class.
		if ( is_wp_error( $note_response ) && class_exists( 'Yikes_Inc_Easy_Mailchimp_Error_Logging' ) ) {
			$error_logging = new Yikes_Inc_Easy_Mailchimp_Error_Logging();
			if ( method_exists( $error_logging, 'maybe_write_to_log' ) ) {
				$error_logging->maybe_write_to_log( $note_response->get_error_code(), __( 'Create member note', 'eu-opt-in-compliance-for-mailchimp' ), 'yikes-inc-easy-mailchimp-eu-law-compliance-extension.php' );
			}
		}
	}

	/**
	 * Enqueue our styles on the dashboard
	 * @since 0.1
	 */

	public function enqueue_yikes_mailchimp_eu_compliance_admin_styles_scripts( $hook ) {

		if ( 'admin_page_yikes-mailchimp-edit-form' == $hook ) {
			wp_enqueue_script( 'yikes-mailchimp-character-counter-script', plugin_dir_url( __FILE__ ) . 'includes/js/yikes-mailchimp-eu-admin-functions.js', array( 'jquery' ) );
			wp_enqueue_style( 'yikes-mailchimp-eu-compliance-styles', plugin_dir_url( __FILE__ ) . 'includes/css/yikes-mailchimp-eu-law-icons.css' );
		}
	}

	/**
	 * Enqueue our styles on the frontend
	 * @since 0.1
	 */

	public function enqueue_yikes_mailchimp_eu_compliance_frontend_assets() {
		wp_enqueue_style( 'yikes-mailchimp-eu-frontend-compliance-styles', plugin_dir_url(__FILE__) . 'includes/css/yikes-mailchimp-eu-law-extension-frontend.css' );
		wp_enqueue_script( 'yikes-mailchimp-eu-frontend-compliance-scripts', plugin_dir_url(__FILE__) . 'includes/js/yikes-mailchimp-front-end-form-functions.js', array( 'jquery' ), false, true );
	}

	/**
	 * Get the custom field data from an Easy Form's form data
	 */
	private function get_custom_field_data( $form_data ) {

		if ( is_string( $form_data['custom_fields'] ) ) {
			return json_decode( $form_data['custom_fields'], true );
		} else if ( is_array( $form_data['custom_fields'] ) ) {
			return $form_data['custom_fields'];
		}

		return array();
	}

	/**
	 *	Render the custom checkbox on the front end of the site
	 *	hook into yikes-mailchimp-additional-form-fields
	 *	@since 0.1
	 */
	public function render_frontend_compliance_checkbox( $form_data ) {

		$custom_field_data = $this->get_custom_field_data( $form_data );

		$prechecked    = isset( $custom_field_data['eu-compliance-law-checkbox-precheck'] ) ? $custom_field_data['eu-compliance-law-checkbox-precheck'] : 0;
		$checkbox_text = isset( $custom_field_data['eu-compliance-law-checkbox-text'] ) ? $custom_field_data['eu-compliance-law-checkbox-text'] : '';

		// Run the text through a custom content filter
		$checkbox_text = $this->custom_the_content( $checkbox_text );

		// A general filter for the checkbox text.
		$checkbox_text = apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-text', $checkbox_text );

		// Filter whether the checkbox is required to continue.
		$checkbox_required = $this->is_checkbox_required() ? 'required="required"' : '';

		$checked = $prechecked === '1' ? 'checked="checked"' : '';
		$html    = '<label class="yikes-mailchimp-eu-compliance-label"><input type="checkbox" ' . $checkbox_required . ' name="eu-laws" value="1" ' . $checked . '> <div class="yikes-mailchimp-eu-compliance-text">' . $checkbox_text . '</div></label>';

		$html = apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-text-html', $html );

		echo $html;
	}

	// Create an array of MERGE fields like MERGE Tag => Field Label.
	public static function get_merge_fields_dropdown() {
		$field_data = Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension::get_merge_fields();

		$field_dd = array( '' => 'Select...' );

		if ( ! empty( $field_data ) ) {
			foreach ( $field_data as $key => $field ) {
				$field_dd[ $field['tag'] ] = $field['name'];
			}
		}

		return $field_dd;
	}

	// Fetch merge fields for this list.
	public static function get_merge_fields() {

		// Get the list ID from the URL.
		$form_id     = isset( $_GET['id'] ) ? $_GET['id'] : '';

		if ( empty( $form_id ) ) {
			return array();
		}

		$interface    = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data    = $interface->get_form( $form_id );
		$list_id      = isset( $form_data['list_id'] ) ? $form_data['list_id'] : '';
		$list_handler = yikes_get_mc_api_manager()->get_list_handler();
		$form_fields  = $list_handler->get_merge_fields( $list_id );
		$field_data   = isset( $form_fields['merge_fields'] ) ? $form_fields['merge_fields'] : array();

		return $field_data;
	}

	// Add custom link to the links.
	public function add_eu_law_compliance_section_links() {

		// Creating a new link on the edit form page.
		Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section_link( array(
			'id'          => 'eu-law-compliance-section', // section id.
			'text'        => __( 'EU Law Compliance', 'eu-opt-in-compliance-for-mailchimp' ), // the text that will display in the link.
			'icon_family' => 'custom',
			'icon'        => 'yikes-mailchimp-eu-law', // dashicon icon class.
		) );
	}

	/** Add custom section associated with link above. */
	public static function render_eu_compliance_section() {

		// Defining a new section, associated with the link above.
		Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section( array(

			// Section id (must match link id above).
			// Title of the main block of this custom section.
			'id'               => 'eu-law-compliance-section',
			'main_title'       => __( 'Compliance Checkbox', 'eu-opt-in-compliance-for-mailchimp' ),
			'main_description' => __( 'A checkbox will display below your form. Users cannot be added to your mailing list unless the checkbox is clicked.', 'eu-opt-in-compliance-for-mailchimp' ),
			'main_fields'      => array(

				// Label text for this field
				// Type of field (text, select, checkbox, radio)
				// Field id - determines how data is saved in database
				// Field description.
				array(
					'label'       => __( 'Save Checkbox Opt-In as a Merge Field', 'eu-opt-in-compliance-for-mailchimp' ),
					'type'        => 'select',
					'options'     => Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension::get_merge_fields_dropdown(),
					'id'          => 'eu-compliance-law-save-opt-in-field',
					'description' => __( 'Choose a merge field to store the opt-in compliance.', 'eu-opt-in-compliance-for-mailchimp' ),
				),
				array(
					'label'       => __( 'Merge Field Opt-In Value', 'eu-opt-in-compliance-for-mailchimp' ),
					'type'        => 'text',
					'placeholder' => 'Enter a value, e.g. opted in',
					'id'          => 'eu-compliance-law-save-opt-in-value',
					'description' => __( 'Enter what should be saved in the merge field chosen above.', 'eu-opt-in-compliance-for-mailchimp' ),
				),
				array(
					'label'       => __( 'Pre-check Compliance Checkbox', 'eu-opt-in-compliance-for-mailchimp' ),
					'type'        => 'select',
					'options'     => array(
						'2' => __( 'No', 'eu-opt-in-compliance-for-mailchimp' ),
						'1' => __( 'Yes', 'eu-opt-in-compliance-for-mailchimp' ),
					),
					'id'          => 'eu-compliance-law-checkbox-precheck',
					'description' => __( 'Should this checkbox be pre-checked on initial page load? (Note: pre-checking checkboxes may make your form noncompliant.)', 'eu-opt-in-compliance-for-mailchimp' ),
				),
				array(
					'label'       => __( 'Compliance Checkbox Consent Text', 'eu-opt-in-compliance-for-mailchimp' ),
					'type'        => 'wysiwyg',
					'id'          => 'eu-compliance-law-checkbox-text',
					'default'     => __( 'By checking this box I consent to the use of my information provided for email marketing purposes.', 'eu-opt-in-compliance-for-mailchimp' ),
					'description' => __( 'This text will display alongside the checkbox. The request for consent must be given with the purpose for data processing included. Consent must be in plain language. Note: MailChimp limits this field to 1,000 characters and does not allow HTML.', 'eu-opt-in-compliance-for-mailchimp' ), // field description.
				),
				array(
					'label'       => __( 'Save Checkbox Compliance Text', 'eu-opt-in-compliance-for-mailchimp' ),
					'type'        => 'checkbox',
					'id'          => 'eu-compliance-law-checkbox-save-text',
					'description' => __( 'Should the content of the Compliance Checkbox Text be saved as a note on the subscriber?', 'eu-opt-in-compliance-for-mailchimp' ),
				),
			),
		) );
	}

	/**
	 * End additional sections & fields
	 */

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Yikes_Inc_Easy_Mailchimp_Extender_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once dirname( __FILE__ ) . '/includes/class-yikes-inc-easy-mailchimp-eu-compliance-i18n.php';
		$eu_compliance_i18n = new Yikes_Inc_Easy_Mailchimp_EU_Compliance_i18n();
		$eu_compliance_i18n->set_domain( 'eu-opt-in-compliance-for-mailchimp' );
		add_action( 'plugins_loaded', array( $eu_compliance_i18n, 'load_eu_compliance_text_domain' ) );
	}

	/**
	* A function that works like `the_content` filter but doesn't bring all of the issues associated with using `the_content`
	*/
	private function custom_the_content( $content ) {

		$content = function_exists( 'capital_P_dangit' ) ? capital_P_dangit( $content ) : $content;
		$content = function_exists( 'wptexturize' ) ? wptexturize( $content ) : $content;
		$content = function_exists( 'convert_smilies' ) ? convert_smilies( $content ) : $content;
		$content = function_exists( 'wpautop' ) ? wpautop( $content ) : $content;
		$content = function_exists( 'shortcode_unautop' ) ? shortcode_unautop( $content ) : $content;
		$content = function_exists( 'prepend_attachment' ) ? prepend_attachment( $content ) : $content;
		$content = function_exists( 'wp_make_content_images_responsive' ) ? wp_make_content_images_responsive( $content ) : $content;
		$content = function_exists( 'do_shortcode' ) ? do_shortcode( $content ) : $content;

		if ( class_exists( 'WP_Embed' ) ) {

			// Deal with URLs
			$embed = new WP_Embed;
			$content = method_exists( $embed, 'autoembed' ) ? $embed->autoembed( $content ) : $content;
		}	

		return $content;
	}
}

/*
*	Ensure base class is loaded first
*/
add_action( 'plugins_loaded', 'load_eu_compliance_law_extension' );
function load_eu_compliance_law_extension() {
	if ( class_exists( 'Yikes_Inc_Easy_Mailchimp_Forms_Admin' ) ) {
		$Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension = new Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension;
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
