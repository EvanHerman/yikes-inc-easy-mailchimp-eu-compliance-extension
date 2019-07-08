<?php
/**
 * Plugin Name: GDPR Compliance for Mailchimp
 * Plugin URI:  http://www.yikesinc.com
 * Description: This extends Easy Forms for Mailchimp to help make forms comply with The EU General Data Protection Regulation (GDPR).
 * Version:     1.3.2
 * Author:      YIKES, Inc.
 * Author URI:  http://www.yikesinc.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: eu-opt-in-compliance-for-mailchimp
 * Domain Path: /languages
 *
 * EU Opt-In Compliance for Mailchimp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * EU Opt-In Compliance for Mailchimp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Forms for Mailchimp. If not, see <http://www.gnu.org/licenses/>.
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
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! is_plugin_active( 'yikes-inc-easy-mailchimp-extender/yikes-inc-easy-mailchimp-extender.php' ) ) {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	add_action( 'admin_notices', 'yikes_inc_mailchimp_eu_law_compliance_display_activation_error' );
	return;
}

/**
 * Make sure Easy Forms is installed.
 */
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
			<p><?php esc_html_e( 'GDPR Compliance for Mailchimp could not be activated because the base plugin is not installed and active.', 'eu-opt-in-compliance-for-mailchimp' ); ?></p>
			<p>
			<?php
			/* translators: the placeholder is a link. */
			printf( esc_html__( 'Please install and activate %s before activating this extension.', 'eu-opt-in-compliance-for-mailchimp' ), '<a href="' . esc_url_raw( admin_url( 'plugin-install.php?tab=search&type=term&s=Yikes+Inc.+Easy+Mailchimp+Forms' ) ) . '" title="Easy Forms for Mailchimp">Easy Forms for Mailchimp</a>' );
			?>
			</p>
		</div>
	<?php
}
/* end check */

/********* Begin Main Plugin ***********/
class Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Define our constants.
		$this->define_constants();

		// Add our incentives link to the edit form page.
		add_action( 'yikes-mailchimp-edit-form-section-links', array( $this, 'add_eu_law_compliance_section_links' ) );

		// Add the incentives section to the edit form page.
		add_action( 'yikes-mailchimp-edit-form-sections', array( $this, 'render_eu_compliance_section' ) );

		// Include our scripts & styles on the admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_yikes_mailchimp_eu_compliance_admin_styles_scripts' ) );

		// Include our scripts & styles on the frontend.
		add_action( 'yikes-mailchimp-shortcode-enqueue-scripts-styles', array( $this, 'enqueue_yikes_mailchimp_eu_compliance_frontend_assets' ) );

		// Render our checkbox after the form.
		add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'render_frontend_compliance_checkbox' ), 10, 1 );

		// Add the WYSIWYG text as a note on the user's form submission.
		add_filter( 'yikes-mailchimp-form-submission', array( $this, 'submit_checkbox_compliance_merge_field' ), 10, 4 );

		// Add the opt-in value as a MERGE field on the subscriber's submission.
		add_filter( 'yikes-mailchimp-filter-before-submission', array( $this, 'add_checkbox_optin_merge_field' ), 10, 1 );

		// Server side logic to make sure the checkbox was checked.
		add_filter( 'yikes-mailchimp-filter-before-submission', array( $this, 'maybe_fail_if_checkbox_not_checked' ), 1, 1 );

		// Set the locale.
		$this->set_locale();
	}

	/**
	 * Define plugin constants.
	 */
	private function define_constants() {

		if ( ! defined( 'YIKES_MAILCHIMP_GDPR_ADDON_PATH' ) ) {
			define( 'YIKES_MAILCHIMP_GDPR_ADDON_PATH', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'YIKES_MAILCHIMP_GDPR_ADDON_URL' ) ) {
			define( 'YIKES_MAILCHIMP_GDPR_ADDON_URL', plugin_dir_url( __FILE__ ) );
		}

		if ( ! defined( 'YIKES_MAILCHIMP_GDPR_ADDON_VERSION' ) ) {
			define( 'YIKES_MAILCHIMP_GDPR_ADDON_VERSION', '1.3.2' );
		}
	}

	/**
	 * Server-side check that our checkbox was checked.
	 *
	 * @param array $merge_variables The subscriber's details.
	 *
	 * @return array $merge_variables The subscriber's details.
	 */
	public function maybe_fail_if_checkbox_not_checked( $merge_variables ) {

		// Non-AJAX.
		if ( ! isset( $_POST['form_data'] ) ) {
			$checkbox_checked = isset( $_POST['eu-laws'] ) && ! empty( $_POST['eu-laws'] );
			$form_id          = ! empty( $_POST['yikes-mailchimp-submitted-form'] ) ? $_POST['yikes-mailchimp-submitted-form'] : 0;
		} else {

			// AJAX.
			parse_str( wp_unslash( $_POST['form_data'] ), $data );
			$checkbox_checked = isset( $data['eu-laws'] ) && ! empty( $data['eu-laws'] );
			$form_id          = ! empty( $data['yikes-mailchimp-submitted-form'] ) ? $data['yikes-mailchimp-submitted-form'] : 0;
		}

		$checkbox_required = $this->is_checkbox_required( $form_id );
		$checkbox_enabled  = $this->is_checkbox_enabled_wrapper( $form_id );

		if ( ! $checkbox_checked && $checkbox_enabled && ! empty( $checkbox_required ) ) {
			$merge_variables['error']   = true;
			$merge_variables['message'] = apply_filters( 'yikes_mailchimp_eu_compliance_checkbox_required_message', __( 'Please give your consent to subscribe to this list by checking the checkbox.', 'eu-opt-in-compliance-for-mailchimp' ), $merge_variables );
		}

		return $merge_variables;
	}

	/**
	 * Is the checkbox enabled?
	 *
	 * @param int $form_id The ID of the form associated with this checkbox.
	 *
	 * @return bool Whether the checkbox is enabled.
	 */
	private function is_checkbox_enabled_wrapper( $form_id ) {
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form_id );

		// Get this form's custom fields.
		$custom_fields = $this->get_custom_field_data( $form_data );

		return $this->is_checkbox_enabled( $custom_fields );
	}

	private function is_checkbox_enabled( $custom_field_data ) {
		return ! ( isset( $custom_field_data['eu-compliance-law-checkbox-disabled'] ) && (string) $custom_field_data['eu-compliance-law-checkbox-disabled'] === '1' );
	}

	/**
	 * Is the checkbox required?
	 *
	 * @param int $form_id The ID of the form associated with this checkbox.
	 *
	 * @return bool Whether the checkbox is required.
	 */
	private function is_checkbox_required( $form_id ) {
		return apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-required', true, $form_id );
	}

	/**
	 * Populate a new merge field with the value of the opt-in field specified in the admin.
	 *
	 * @param array $merge_variables The subscriber's details.
	 *
	 * @return array $merge_variables The subscriber's details, potentially with our field added.
	 */
	public function add_checkbox_optin_merge_field( $merge_variables ) {

		$form_id = isset( $_POST['form_id'] ) ? filter_var( wp_unslash( $_POST['form_id'] ), FILTER_SANITIZE_NUMBER_INT ) : '';

		if ( empty( $form_id ) ) {
			return $merge_variables;
		}

		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form_id );

		// Get this form's custom fields.
		$custom_fields = $this->get_custom_field_data( $form_data );

		// Make sure the checkbox isn't disabled.
		if ( ! $this->is_checkbox_enabled( $custom_fields ) ) {
			return $merge_variables;
		}

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
	 *
	 * @param string $email           The subscriber's email.
	 * @param array  $merge_variables The subscriber's details.
	 * @param int    $form_id         Easy Form's form ID.
	 * @param array  $notifications   Array of notifications returned to the user.
	 */
	public function submit_checkbox_compliance_merge_field( $email, $merge_variables, $form_id, $notifications ) {

		// Get the list ID from the form ID.
		$interface = yikes_easy_mailchimp_extender_get_form_interface();
		$form_data = $interface->get_form( $form_id );

		// Get this form's custom fields.
		$custom_fields = $this->get_custom_field_data( $form_data );

		// Make sure the checkbox isn't disabled.
		if ( ! $this->is_checkbox_enabled( $custom_fields ) ) {
			return;
		}

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
	 * If SCRIPT_DEBUG is defined, return nothing. Otherwise return .min.
	 *
	 * @return string If SCRIPT_DEBUG is defined, return nothing. Otherwise return .min.
	 */
	private function get_assets_minified() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	/**
	 * Enqueue our admin assets.
	 *
	 * @param string $hook The page slug.
	 */
	public function enqueue_yikes_mailchimp_eu_compliance_admin_styles_scripts( $hook ) {
		$min = $this->get_assets_minified();

		if ( 'admin_page_yikes-mailchimp-edit-form' === $hook ) {
			wp_enqueue_script( 'yikes-mailchimp-character-counter-script', YIKES_MAILCHIMP_GDPR_ADDON_URL . "includes/js/yikes-mailchimp-eu-admin-functions{$min}.js", array( 'jquery' ), YIKES_MAILCHIMP_GDPR_ADDON_VERSION, true );
			wp_enqueue_style( 'yikes-mailchimp-eu-compliance-styles', YIKES_MAILCHIMP_GDPR_ADDON_URL . "includes/css/yikes-mailchimp-eu-law-icons{$min}.css", array(), YIKES_MAILCHIMP_GDPR_ADDON_VERSION, 'all' );
		}
	}

	/**
	 * Enqueue our frontend assets.
	 */
	public function enqueue_yikes_mailchimp_eu_compliance_frontend_assets() {
		$min = $this->get_assets_minified();

		wp_enqueue_style( 'yikes-mailchimp-eu-frontend-compliance-styles', YIKES_MAILCHIMP_GDPR_ADDON_URL . "includes/css/yikes-mailchimp-eu-law-extension-frontend{$min}.css", array(), YIKES_MAILCHIMP_GDPR_ADDON_VERSION, 'all' );
		wp_enqueue_script( 'yikes-mailchimp-eu-frontend-compliance-scripts', YIKES_MAILCHIMP_GDPR_ADDON_URL . "includes/js/yikes-mailchimp-front-end-form-functions{$min}.js", array( 'jquery' ), YIKES_MAILCHIMP_GDPR_ADDON_VERSION, true );
	}

	/**
	 * Get the custom field data from an Easy Form's form data.
	 *
	 * @param mixed $form_data Easy Form's form data.
	 *
	 * @return array $form_data Easy Form's form data.
	 */
	private function get_custom_field_data( $form_data ) {

		if ( is_string( $form_data['custom_fields'] ) ) {
			return json_decode( $form_data['custom_fields'], true );
		} elseif ( is_array( $form_data['custom_fields'] ) ) {
			return $form_data['custom_fields'];
		}

		return array();
	}

	/**
	 * Render the custom checkbox on the front end of the site.
	 *
	 * @param mixed $form_data Easy Form's form data.
	 */
	public function render_frontend_compliance_checkbox( $form_data ) {

		$custom_field_data = $this->get_custom_field_data( $form_data );

		// Make sure the checkbox isn't disabled.
		if ( ! $this->is_checkbox_enabled( $custom_field_data ) ) {
			return;
		}

		$prechecked    = isset( $custom_field_data['eu-compliance-law-checkbox-precheck'] ) ? $custom_field_data['eu-compliance-law-checkbox-precheck'] : 0;
		$checkbox_text = isset( $custom_field_data['eu-compliance-law-checkbox-text'] ) ? $custom_field_data['eu-compliance-law-checkbox-text'] : '';

		// Run the text through a custom content filter.
		$checkbox_text = $this->custom_the_content( $checkbox_text );

		// A general filter for the checkbox text.
		$checkbox_text = apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-text', $checkbox_text, $form_data );

		// Filter whether the checkbox is required to continue.
		$checkbox_required = $this->is_checkbox_required( $form_data['id'] ) ? 'required="required"' : '';

		$checked = $prechecked === '1' ? 'checked="checked"' : '';
		$html    = '<label class="yikes-mailchimp-eu-compliance-label"><input type="checkbox" ' . $checkbox_required . ' name="eu-laws" value="1" ' . $checked . '> <div class="yikes-mailchimp-eu-compliance-text">' . $checkbox_text . '</div></label>';

		$html = apply_filters( 'yikes-mailchimp-eu-compliance-checkbox-text-html', $html, $form_data );

		echo $html;
	}

	/**
	 * Create an array of MERGE fields like MERGE Tag => Field Label.
	 */
	public function get_merge_fields_dropdown() {
		$field_data = $this->get_merge_fields();
		$field_dd   = array( '' => 'Select...' );

		if ( ! empty( $field_data ) ) {
			foreach ( $field_data as $key => $field ) {
				if ( $field['tag'] === 'EMAIL' ) {
					continue;
				}
				$field_dd[ $field['tag'] ] = $field['name'];
			}
		}

		return $field_dd;
	}

	/**
	 * Get the merge fields for this list.
	 */
	public function get_merge_fields() {

		// Get the list ID from the URL.
		$form_id = isset( $_GET['id'] ) ? filter_var( wp_unslash( $_GET['id'] ), FILTER_SANITIZE_NUMBER_INT ) : '';

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

	/**
	 * Add custom link to the form builder.
	 */
	public function add_eu_law_compliance_section_links() {

		// Creating a new link on the edit form page.
		Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section_link(
			array(
				'id'          => 'eu-law-compliance-section',
				'text'        => __( 'EU Law Compliance', 'eu-opt-in-compliance-for-mailchimp' ),
				'icon_family' => 'custom',
				'icon'        => 'yikes-mailchimp-eu-law',
			)
		);
	}

	/**
	 * Custom form builder section content.
	 */
	public function render_eu_compliance_section() {

		// Defining a new section, associated with the link above.
		Yikes_Inc_Easy_Mailchimp_Extender_Helper::add_edit_form_section(
			array(
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
						'options'     => $this->get_merge_fields_dropdown(),
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
						'description' => __( 'This text will display alongside the checkbox. The request for consent must be given with the purpose for data processing included. Consent must be in plain language. Note: Mailchimp limits this field to 1,000 characters and does not allow HTML.', 'eu-opt-in-compliance-for-mailchimp' ), // field description.
					),
					array(
						'label'       => __( 'Save Checkbox Compliance Text', 'eu-opt-in-compliance-for-mailchimp' ),
						'type'        => 'checkbox',
						'id'          => 'eu-compliance-law-checkbox-save-text',
						'description' => __( 'Should the content of the Compliance Checkbox Text be saved as a note on the subscriber?', 'eu-opt-in-compliance-for-mailchimp' ),
					),
					array(
						'label'       => __( 'Disable Checkbox for this Form', 'eu-opt-in-compliance-for-mailchimp' ),
						'type'        => 'checkbox',
						'id'          => 'eu-compliance-law-checkbox-disabled',
						'description' => __( 'Should the checkbox be displayed for this form?', 'eu-opt-in-compliance-for-mailchimp' ),
					),
				),
			)
		);
	}

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
		require_once YIKES_MAILCHIMP_GDPR_ADDON_PATH . '/includes/class-yikes-inc-easy-mailchimp-eu-compliance-i18n.php';
		$eu_compliance_i18n = new Yikes_Inc_Easy_Mailchimp_EU_Compliance_i18n();
		$eu_compliance_i18n->set_domain( 'eu-opt-in-compliance-for-mailchimp' );
		add_action( 'plugins_loaded', array( $eu_compliance_i18n, 'load_eu_compliance_text_domain' ) );
	}

	/**
	 * A function that works like `the_content` filter but doesn't bring all of the issues associated with using `the_content`.
	 *
	 * @param string $content HTML content.
	 *
	 * @return string $content The content, filtered.
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

			// Deal with URLs.
			$embed   = new WP_Embed();
			$content = method_exists( $embed, 'autoembed' ) ? $embed->autoembed( $content ) : $content;
		}

		return $content;
	}
}


add_action( 'plugins_loaded', 'load_eu_compliance_law_extension' );
/**
 * Run our class. Ensure base class is loaded first.
 */
function load_eu_compliance_law_extension() {
	if ( class_exists( 'Yikes_Inc_Easy_Mailchimp_Forms_Admin' ) ) {
		new Yikes_Inc_Easy_Mailchimp_EU_Law_Compliance_Extension();
	}
}
