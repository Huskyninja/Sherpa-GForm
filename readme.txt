=== Plugin Name ===
Plugin Name: Sherpa Gravity Forms
Description: Send form data to the Sherpa CRM using Gravity Form's Add-on Framework
version: 0.7
Author: Husky Ninja
Author URI: https://www.husky.ninja
License: GPLv3 or later
Text Domain: sherpa_gform
Domain Path: /languages

For sending Wordpress Gravityforms data to Sherpa's add lead RESTful endpoint.

== Description ==

For use only with Gravity Forms v1.9 or greater.

Main Settings
Main for settings can be found under admin -> Forms -> Settings -> Sherpa GForm. You will need a the Company ID supplied by Sherpa. The sandbox Company ID is set by default. You will need the correct Company ID supplied by Sherpa.

The endpoint url is also set to the sandbox environment by default. The production endpoint url is https://members.sherpacrm.com/api/lead/create.

If you wish to send a debug email select the "Send a debug email" checkbox. The debug email contains the json query and the response from sherpa, and is useful in debugging without the presence of a log file.

Enter a single valid email into the "Debug email address" field. Invalid email addresses will not send a debug email.

If necessary, set the Sherpa Autoset Values for the fields that are required by Sherpa to process the request. This is a global setting, but it can be overwritten by mapping the appropriate fields at the form level (see Field Mapping, below).

The default values for the Sherpa Autoset Values should be correct for 99% of requests sent.

Form Settings
Individual form settings can be found under admin -> Forms -> Forms -> {form name} -> Settings -> Sherpa GForm.

Select the "Send this form to Sherpa" checkbox to attach the form. The Community ID is set to the sandbox Community ID by default. You will need a Community ID supplied by Sherpa.

By default this plugin uses Remote Post (wp_remote_post) to send form data. This can be changed to to use cURL. If you have cURL installed and wish to use this method, select this checkbox.

Field Mapping
To map the form fields, select the relevant Field (to be mapped for Sherpa) to the Form Field (from the Gravity Form). Some field values are set by the Sherpa Autoset Values (above), but may be overwritten here.

The form field must be of the correct type. The mapping is as follows:

First Name -> name, text or hidden
Last Name -> name, text or hidden
Email Address -> email or hidden
Phone -> phone or hidden
Vendor Name -> hidden
Source Name -> hidden
Referral Type -> hidden
Advisor First Name -> hidden
Advisor Last Name -> hidden
Advisor Email -> hidden
Advisor Referral Note -> text, select or textarea
Resident First Name -> text or hidden
Resident Last Name -> text or hidden
Resident Relationship -> select or hidden

So make sure when creating your form that you use the correct form field types for the Sherpa field mapping. For more information on field mapping, see the Sherpa Gform within the plugin.

== Changelog ==

= 0.7 =
* updated author since sage age no longer supporting plugin
* added placeholder to languages directory
* finally fixed scripts and styles

= 0.6 =
* changed default method for posting to Wordpress Remote Post with cURL as a selectable alternative

= 0.5 =
* fixed incorrect header info in readme file
* fixed formatting issue in quick set drop down config entry for PrimaryContactResidentRelationship = "self"

= 0.4 =
* corrected Author URI
* fixed typo in label field
* fixed bug with form email mapping

= 0.3 =
* moved autofill values to head of method to facilitate overwriting
* added additional fields that can be mapped, including all required fields (to overwrite using hidden fields)
* update help doc

= 0.2 =
* fix Uninitialized string offset: 0 and Illegal string offset 'send_form' issue when loading send_form for a form that has not been configured to use sherpa
* add help info & page

= 0.1 =
* First buildout.

== Upgrade Notice ==

= 0.0 =
Placeholder.

== Arbitrary section ==

This is arbitrary.