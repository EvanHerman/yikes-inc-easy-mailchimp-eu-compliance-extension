=== GDPR Compliance for Mailchimp ===
Contributors: yikesinc, eherman24, liljimmi, yikesitskevin, jpowersdev
Donate link: http://www.yikesinc.com
Tags: GDPR, Mailchimp, Yikes Mailchimp, EU, Compliance, Law, Opt-in
Requires at least: 3.5
Tested up to: 6.0
Stable tag: 1.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This extends Easy Forms for Mailchimp to help forms comply with The EU General Data Protection Regulation (GDPR).

== Description == 

This addon creates an additional section on the Easy Forms for Mailchimp form builder called 'EU Law Compliance.' There you can manage settings assisting forms to be compliant with The EU General Data Protection Regulation (GDPR). The law applies to the processing of personal user data in the European Union (EU) regardless of whether the processing takes place in the EU or not.

All Mailchimp forms will have a checkbox above the submit button accompanied by text you can customize to confirm the user consents to their data being submitted.

There is an option to save your checkbox confirmation text as a note on the subscriber's profile. This is to help you demonstrate consent as required by GDPR.

There is another option to save a value of your choice to a specific MERGE field on your list. Along with demonstrating consent, this can help you manage your lists by showing you who has opted-in post-GDPR.

> Note: This add-on plugin requires [Easy Forms for Mailchimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/).

== Installation ==

1. Download the plugin .zip file and make note of where on your computer you downloaded it to.
2. In the WordPress admin (yourdomain.com/wp-admin) go to Plugins > Add New or click the "Add New" button on the main plugins screen.
3. On the following screen, click the "Upload Plugin" button.
4. Browse your computer to where you downloaded the plugin .zip file, select it and click the "Install Now" button.
5. After the plugin has successfully installed, click "Activate Plugin" and enjoy!

== Frequently Asked Questions ==

**All documentation can be found in [our Knowledge Base](https://yikesplugins.com/support/knowledge-base/eu-compliance-for-easy-forms-for-mailchimp/).**

= Do I need a another plugin for this to work? =
Yes, this plugin is an add-on to [Easy Forms for Mailchimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/).

= How do I change the opt-in text? =
After installing and activating the plugin, you will find a new section on the Form Builder screen called "EU Law Compliance." There you can customize the opt-in checkbox text.

= Will this plugin make my forms compliant with GDPR regulations? =
This plugin is aimed to help you make your forms compliant but *its installation will not automatically guarantee compliance.* You need to review and follow the [guidelines established by the GDPR](https://www.eugdpr.org/) to ensure compliance.

== Screenshots ==

1. Admin: A new tab is added to your Easy Forms form editor where you can customize your compliance checkbox text along with other options for storing consent in Mailchimp.
2. Front-End: A checkbox with text is added to the front-end view of your Mailchimp form

== Changelog ==

= 1.3.7 =
* Housekeeping

= 1.3.5 =
* Remove unnecessary files

= 1.3.4 =
* Replace deprecated function wp_make_content_images_responsive

= 1.3.3 =
* Security Update

= 1.3.2 =
* Fixing a syntax error for older versions of PHP.

= 1.3.1 = 
* Updating all instances of MailChimp to Mailchimp.
* Fixing an issue with static functions calling non static functions.
* Removing email address from the list of potential fields to save opt-in values to.

= 1.3.0 =
* Checkboxes can now be disabled on a per-form basis.
* Enhanced the checkbox required filter to include the current form's ID.
* Cleaned and code sniffed up all code.
* Minified assets.

= 1.2.3 = 
* Unchecking the checkbox after a successful form submission. This requires Easy Forms v6.4.12.
* Fixing a syntax error for older versions of PHP.

= 1.2.2 = 
* Adding a server side check that makes sure the checkbox was checked. The default message returned when the checkbox isn't checked is "Please give your consent to subscribe to this list by checking the checkbox." This can be filtered via `yikes_mailchimp_eu_compliance_checkbox_required_message`.

= 1.2.1 = 
* Removed the use of the `the_content` filter (also removed the filter `yikes-mailchimp-eu-compliance-use-the-content` that controlled the use of `the_content`)
* Changed the placeholder text for the Merge Field Opt-In Value field to help avoid confusion

= 1.2.0 =
* Added a new checkbox that controls whether your checkbox confirmation language is sent to Mailchimp
* Added functionality for adding your checkbox confirmation language as a note on a subscriber's profile
* Added a new dropdown for saving an opt-in flag as a merge field
* Added a new text field for entering your opt-in flag value
* Added a character counter to the checkbox confirmation language field because Mailchimp will limit each note to 1,000 characters.
* Changed default checkbox confirmation language
* Changed the checkbox so it is no longer pre-checked by default
* Some copy changes

= 1.1.2 =
* Updating text domain and i18n-related functions to official WP plugin repo name: eu-opt-in-compliance-for-mailchimp

= 1.1.1 =
* Fixed i18n errors in prior commit
* Updated the text domain to yikes-inc-easy-mailchimp-eu-law-compliance-extension
* Generated new .pot file
* Renamed main plugin file to yikes-inc-easy-mailchimp-eu-law-compliance-extension.php for consistency with other Yikes Mailchimp add-on plugins
* Refactored the uninstall method so it's using the options array instead of custom DB table
* Added assets folder and screenshots
* Incremented version number

= 1.1 =
* Changes to keep this add-on in sync with the base Yikes Mailchimp plugin
* Changes to put this plugin on the official WordPress.org plugin repo

= 1.0 =
* Initial Release

