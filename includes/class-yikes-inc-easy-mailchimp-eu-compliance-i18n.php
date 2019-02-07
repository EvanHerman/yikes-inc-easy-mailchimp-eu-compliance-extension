<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.yikesinc.com/
 * @since      1.0.0
 *
 * @package    Yikes_Inc_Easy_Mailchimp_Extender
 * @subpackage Yikes_Inc_Easy_Mailchimp_Extender/includes
 */
class Yikes_Inc_Easy_Mailchimp_EU_Compliance_i18n {
	/**
	 * The domain specified for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $domain    The domain identifier for this plugin.
	 */
	private $domain;
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_eu_compliance_text_domain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			YIKES_MAILCHIMP_GDPR_ADDON_PATH . '/languages/'
		);
	}
	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @param string $domain The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}
}
