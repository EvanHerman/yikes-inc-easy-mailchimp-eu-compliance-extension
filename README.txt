=== EU Opt-In Compliance for MailChimp ===
Contributors: yikesinc, eherman24, liljimmi, yikesitskevin
Donate link: http://www.yikesinc.com
Tags: MailChimp, Yikes MailChimp, EU, Compliance, Law, Opt-in
Requires at least: 3.5
Tested up to: 4.9.5
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This add-on extends Easy Forms for MailChimp to allow MailChimp forms to comply with the EU Opt-In Directive

== Description == 

This addon extends Easy Forms for MailChimp by creating an additional section on the form builder called 'EU Law Compliance.' There you can manage a checkbox that will assist with making your form compliant with The EU Opt-In Directive (Directive 2002/58/EC, Directive 2003/58/EC) which covers all direct email marketing messages, including charitable and political messages.

Below all of the MailChimp forms on your site there will be a check box accompanied by some customizable text. 

> Note: This add-on plugin requires [Easy Forms for MailChimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) to work.

== Installation ==

1. Download the plugin .zip file and make note of where on your computer you downloaded it to.
2. In the WordPress admin (yourdomain.com/wp-admin) go to Plugins > Add New or click the "Add New" button on the main plugins screen.
3. On the following screen, click the "Upload Plugin" button.
4. Browse your computer to where you downloaded the plugin .zip file, select it and click the "Install Now" button.
5. After the plugin has successfully installed, click "Activate Plugin" and enjoy!

== Frequently Asked Questions ==

**All documentation can be found in [our Knowledge Base](https://yikesplugins.com/support/knowledge-base/eu-compliance-for-easy-forms-for-mailchimp/).**

= Do I need a another plugin for this to work? =
Yes, this plugin is an add-on to [Easy Forms for MailChimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/).

= How do I change the opt-in text? =
After installing and activating the plugin, you will find a new section on the Form Builder screen called "EU Law Compliance." There you can customize the opt-in checkbox text.

== Screenshots ==

1. Admin: A new tab is added to your MailChimp form editor where you can customize your compliance checkbox text
2. Front-End: A checkbox with text is added to the front-end view of your MailChimp form

== Changelog ==

= 1.2.0 =
* Added a new checkbox that controls whether your checkbox confirmation language is sent to MailChimp
* Added functionality for adding your checkbox confirmation language as a note on a subscriber's profile
* Added a new dropdown for saving an opt-in flag as a merge field
* Added a new text field for entering your opt-in flag value
* Added a character counter to the checkbox confirmation language field because MailChimp will limit each note to 1,000 characters.
* Changed default checkbox confirmation language
* Changed the checkbox so it is no longer pre-checked by default
* Some copy changes

= 1.1.2 =
* Updating text domain and i18n-related functions to official WP plugin repo name: eu-opt-in-compliance-for-mailchimp

= 1.1.1 =
* Fixed i18n errors in prior commit
* Updated the text domain to yikes-inc-easy-mailchimp-eu-law-compliance-extension
* Generated new .pot file
* Renamed main plugin file to yikes-inc-easy-mailchimp-eu-law-compliance-extension.php for consistency with other Yikes MailChimp add-on plugins
* Refactored the uninstall method so it's using the options array instead of custom DB table
* Added assets folder and screenshots
* Incremented version number

= 1.1 =
* Changes to keep this add-on in sync with the base Yikes MailChimp plugin
* Changes to put this plugin on the official WordPress.org plugin repo

= 1.0 =
* Initial Release

