#  GDPR Compliance for MailChimp

This addon extends the base plugin, [Easy Forms for MailChimp by YIKES](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/tree/staging), by creating an additional section to the manage forms page called 'EU Law Compliance'. 

All of the MailChimp forms on your site will then contain a check box below them which will assist you in making your forms compliant with EU laws.


## Installation

1. Ensure you have the base plugin installed, [Easy MailChimp by Yikes Inc](https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender/tree/staging)
2. Download the latest release of the GDPR Compliance for MailChimp extension
3. Go to Plugins > Add New > Upload Plugin and upload the .zip
4. Either begin using with the default settings or head into the 'Edit Form' page and select the 'EU Law Compliance' tab at the top to customize the checkbox text and precheck state. 
5. Profit

#### Changelog

<strong>1.3.4</strong>
* Replace deprecated function wp_make_content_images_responsive

<strong>1.3.3</strong>
* Security Update

<strong>1.3.2</strong>
* Fixing a syntax error for older versions of PHP.

<strong>1.3.1</strong>
* Updating all instances of MailChimp to Mailchimp.
* Fixing an issue with static functions calling non static functions.
* Removing email address from the list of potential fields to save opt-in values to.

<strong>1.3.0</strong>
* Checkboxes can now be disabled on a per-form basis.
* Enhanced the checkbox required filter to include the current form's ID.
* Cleaned and code sniffed up all code.
* Minified assets.

<strong>1.2.3</strong>
* Unchecking the checkbox after a successful form submission. This requires Easy Forms v6.4.12.
* Fixing a syntax error for older versions of PHP.

<strong>1.2.2</strong>
* Adding a server side check that makes sure the checkbox was checked. The default message returned when the checkbox isn't checked is "Please give your consent to subscribe to this list by checking the checkbox." This can be filtered via `yikes_mailchimp_eu_compliance_checkbox_required_message`.

<strong>1.2.1</strong>
* Removed the use of the `the_content` filter (also removed the filter `yikes-mailchimp-eu-compliance-use-the-content` that controlled the use of `the_content`)
* Changed the placeholder text for the Merge Field Opt-In Value field to help avoid confusion

<strong>v1.2.0</strong>
* Added a new checkbox that controls whether your checkbox confirmation language is sent to MailChimp
* Added functionality for adding your checkbox confirmation language as a note on a subscriber's profile
* Added a new dropdown for saving an opt-in flag as a merge field
* Added a new text field for entering your opt-in flag value
* Added a character counter to the checkbox confirmation language field because MailChimp will limit each note to 1,000 characters.
* Changed default checkbox confirmation language
* Changed the checkbox so it is no longer pre-checked by default
* Some copy changes

<strong>v1.1</strong>
* Updated the plugin to stay in sync with the base MailChimp plugin's latest release
* The pre-checked checkbox functionality now works as expected
* Incremented the version number

<strong>v1.0</strong>
* Initial release