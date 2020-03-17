<?php

GFForms::include_addon_framework();
 
class SherpaGform extends GFAddOn {
 
    protected $_version = SHERPA_GFORM_VERSION;
    protected $_min_gravityforms_version = '1.9';
    protected $_slug = 'sherpa_gform';
    protected $_path = 'sherpa_gform/sherpa_gform.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Sherpa Add-On for Gravity Forms';
    protected $_short_title = 'Sherpa GForm';
 
    private static $_instance = null;
	
	/**
	 * Get an instance of this class.
	 *
	 * @return SherpaGform
	 */
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new SherpaGform();
        }
 
        return self::$_instance;
    }
	
	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 */
    public function init() {
        parent::init();
        add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2 );
		add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
    }
	
	/**
	 * This function maps the fields and then sends the data to the endpoint.
	 *
	 * @param array $entry The entry currently being processed.
	 * @param array $form The form currently being processed.
	 */		
	public function after_submission( $entry, $form ) {

		if (!function_exists('write_log')) {

			function write_log($log) {
				if (true === WP_DEBUG) {
					if (is_array($log) || is_object($log)) {
						error_log(print_r($log, true));
					} else {
						error_log($log);
					}
				}
			}

		}
		
		$active_form = $form['id'];
		
		$settings = $this->get_form_settings($form);
		$plugin_settings = $this->get_plugin_settings();
		
		$send_form = '';
		if (isset($settings['send_form'])) {
			$send_form = $settings['send_form'];
		}
		
		if ($send_form == '1') {
			
			write_log('Sherpa is sending form ' . $active_form);
		
			/*
			$json_entry = json_encode($entry);
			write_log($json_entry);
			*/
			
			// $json_settings = json_encode($settings);
			// write_log($json_settings);
			
			// set the global "required" field values
			
			if (isset($plugin_settings['vendor_name']) && !empty($plugin_settings['vendor_name'])) {
				$quick_query['VendorName'] = $plugin_settings['vendor_name'];
			} else {
				$quick_query['VendorName'] = 'Company Website';
			}
			
			if (isset($plugin_settings['source_name']) && !empty($plugin_settings['source_name'])) {
				$quick_query['SourceName'] = $plugin_settings['source_name'];
			} else {
				$quick_query['SourceName'] = 'Company Website';
			}
			
			if (isset($plugin_settings['referral_type']) && !empty($plugin_settings['referral_type'])) {
				$quick_query['ReferralType'] = $plugin_settings['referral_type'];
			} else {
				$quick_query['ReferralType'] = '1';
			}
			
			if (isset($plugin_settings['advisor_first_name']) && !empty($plugin_settings['advisor_first_name'])) {
				$quick_query['AdvisorFirstName'] = $plugin_settings['advisor_first_name'];
			} else {
				$quick_query['AdvisorFirstName'] = '';
			}
			
			if (isset($plugin_settings['advisor_first_name']) && !empty($plugin_settings['advisor_first_name'])) {
				$quick_query['AdvisorFirstName'] = $plugin_settings['advisor_first_name'];
			} else {
				$quick_query['AdvisorFirstName'] = '';
			}
			
			if (isset($plugin_settings['advisor_last_name']) && !empty($plugin_settings['advisor_last_name'])) {
				$quick_query['AdvisorLastName'] = $plugin_settings['advisor_last_name'];
			} else {
				$quick_query['AdvisorLastName'] = '';
			}
			
			if (isset($plugin_settings['advisor_email']) && !empty($plugin_settings['advisor_email'])) {
				$quick_query['AdvisorEmail'] = $plugin_settings['advisor_email'];
			} else {
				$quick_query['AdvisorEmail'] = '';
			}
			
			if (isset($plugin_settings['resident_contact_first_name']) && !empty($plugin_settings['resident_contact_first_name'])) {
				$quick_query['ResidentContactFirstName'] = $plugin_settings['resident_contact_first_name'];
			} else {
				$quick_query['ResidentContactFirstName'] = '';
			}
			
			if (isset($plugin_settings['resident_contact_last_name']) && !empty($plugin_settings['resident_contact_last_name'])) {
				$quick_query['ResidentContactLastName'] = $plugin_settings['resident_contact_last_name'];
			} else {
				$quick_query['ResidentContactLastName'] = '';
			}
			
			if (isset($plugin_settings['primary_contact_resident_relationship']) && !empty($plugin_settings['primary_contact_resident_relationship'])) {
				$quick_query['PrimaryContactResidentRelationship'] = $plugin_settings['primary_contact_resident_relationship'];
			} else {
				$quick_query['PrimaryContactResidentRelationship'] = 'self';
			}
			
			// field mapping			
			
			if (isset($settings['sherpa_fields_first_name']) && !empty($settings['sherpa_fields_first_name'])) {
				$map_first_name = $settings['sherpa_fields_first_name'];
				$quick_query['PrimaryContactFirstName'] = $entry[$map_first_name];
			}
			
			if (isset($settings['sherpa_fields_last_name']) && !empty($settings['sherpa_fields_last_name'])) {
				$map_last_name = $settings['sherpa_fields_last_name'];
				$quick_query['PrimaryContactLastName'] = $entry[$map_last_name];
			}
			
			if (isset($settings['sherpa_fields_email_address']) && !empty($settings['sherpa_fields_email_address'])) {
				$map_email = $settings['sherpa_fields_email_address'];
				$quick_query['PrimaryContactEmail'] = $entry[$map_email];
			}
			
			if (isset($settings['sherpa_fields_phone']) && !empty($settings['sherpa_fields_phone'])) {
				$map_phone = $settings['sherpa_fields_phone'];
				$quick_query['PrimaryContactHomePhone'] = $entry[$map_phone];
			}
			
			// "required" fields set by field mapping

			if (isset($settings['sherpa_fields_form_vendor_name']) && !empty($settings['sherpa_fields_form_vendor_name'])) {
				$map_vendor_name = $settings['sherpa_fields_form_vendor_name'];
				$quick_query['VendorName'] = $entry[$map_vendor_name];
			}

			if (isset($settings['sherpa_fields_form_source_name']) && !empty($settings['sherpa_fields_form_source_name'])) {
				$map_source_name = $settings['sherpa_fields_form_source_name'];
				$quick_query['SourceName'] = $entry[$map_source_name];
			}

			if (isset($settings['sherpa_fields_form_referral_type']) && !empty($settings['sherpa_fields_form_referral_type'])) {
				$map_referral_type = $settings['sherpa_fields_form_referral_type'];
				$quick_query['ReferralType'] = $entry[$map_referral_type];
			}

			if (isset($settings['sherpa_fields_form_advisor_first_name']) && !empty($settings['sherpa_fields_form_advisor_first_name'])) {
				$map_advisor_first_name = $settings['sherpa_fields_form_advisor_first_name'];
				$quick_query['AdvisorFirstName'] = $entry[$map_advisor_first_name];
			}

			if (isset($settings['sherpa_fields_form_advisor_last_name']) && !empty($settings['sherpa_fields_form_advisor_last_name'])) {
				$map_advisor_last_name = $settings['sherpa_fields_form_advisor_last_name'];
				$quick_query['AdvisorLastName'] = $entry[$map_advisor_last_name];
			}

			if (isset($settings['sherpa_fields_form_advisor_email']) && !empty($settings['sherpa_fields_form_advisor_email'])) {
				$map_advisor_email = $settings['sherpa_fields_form_advisor_email'];
				$quick_query['AdvisorEmail'] = $entry[$map_advisor_email];
			}

			// advisor referral note is not a required field but is down here due to legacy sorting
			if (isset($settings['sherpa_fields_referral_note']) && !empty($settings['sherpa_fields_referral_note'])) {
				$map_referral_note = $settings['sherpa_fields_referral_note'];
				$quick_query['AdvisorReferralNote'] = $entry[$map_referral_note];
			}

			if (isset($settings['sherpa_fields_form_resident_first_name']) && !empty($settings['sherpa_fields_form_resident_first_name'])) {
				$map_resident_first_name = $settings['sherpa_fields_form_resident_first_name'];
				$quick_query['ResidentContactFirstName'] = $entry[$map_resident_first_name];
			}

			if (isset($settings['sherpa_fields_form_resident_last_name']) && !empty($settings['sherpa_fields_form_resident_last_name'])) {
				$map_resident_last_name = $settings['sherpa_fields_form_resident_last_name'];
				$quick_query['ResidentContactLastName'] = $entry[$map_resident_last_name];
			}

			// resident relationship is not a required field but is down here due to legacy sorting
			if (isset($settings['sherpa_fields_form_resident_relationship']) && !empty($settings['sherpa_fields_form_resident_relationship'])) {
				$map_resident_relationship = $settings['sherpa_fields_form_resident_relationship'];
				$quick_query['PrimaryContactResidentRelationship'] = $entry[$map_resident_relationship];
			}
			
			$quick_query['ReferralDate'] = date('Y-m-d');
			$quick_query['ReferralDateTime'] = date('Y-m-d\zH:i:s');
			$lead_query = array('lead' => $quick_query);

			$json_query = json_encode($lead_query);
			// write_log($json_query);
			
			$company_id = $plugin_settings['company_id'];
			$community_id = $settings['community_id'];
			
			$post_url = $plugin_settings['target_url'];
			
			$send_mail = false;
			$send_debug_email = $plugin_settings['send_debug_email'];
			if ($send_debug_email == '1') {
				if (isset($plugin_settings['debug_email']) && !empty($plugin_settings['debug_email']) && is_email($plugin_settings['debug_email'])) {
					$send_mail = true;
					$target_email = $plugin_settings['debug_email'];
				} else {
					write_log('There is an issue with the debug email attached to the Sherpa Gform configuration. Please check the configuration.');
				}
			}
			
			$send_w_curl = false;
			
			if ($settings['use_curl']) {
				$send_w_curl = true;
			}

			if ( $send_w_curl ) {
			
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $post_url);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_query);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				// curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json_query),
					'company: ' . $company_id,
					'community: ' . $community_id,	
				));
				
				$result = curl_exec($ch);
				$error = curl_error($ch);
				
				$info = curl_getinfo($ch);
				$response = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
				
				// write_log('Settings: ' . $json_settings . ' Query Sent: ' . $json_query . ' Resonse: ' . $response . ' Result: ' . $result);
				
				write_log('cURL used to post to Sherpa. Query Sent: ' . $json_query . ' Resonse: ' . $response . ' Result: ' . $result);
				
				if ($send_mail) {
					$message = 'cURL used to post to Sherpa. Query Sent: ' . $json_query . ' Resonse: ' . $response . ' Result: ' . $result;
					wp_mail($target_email, 'Debug mail for form id ' . $active_form, $message);
				}

			} else {
			
				$headers = array (
					'Content-type' => 'application/json',
					'Content-Length' => strlen($json_query),
					'company' => $company_id,
					'community' => $community_id
				);

				$em_connect = array (
					'method' => 'POST',
					'timeout' => 15,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => $headers,
					'body' => $json_query,
					'cookies' => array()
				);

				$response = wp_remote_post( $post_url, $em_connect );

				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					write_log($error_message);
					
					if ($send_mail) {
						$message = 'There has been an error sending data to Sherpa using Remote Post. Error Details: ' . $error_message;
						wp_mail($target_email, 'Debug mail for form id ' . $active_form, $message);
					}
					
				} else {
					write_log( 'Wordpress Remote Post used to post to Sherpa. Query Sent: ' . $json_query . ' Response: ' . wp_remote_retrieve_response_code($response) . ' - ' . wp_remote_retrieve_response_message($response). ' Result: ' . wp_remote_retrieve_body($response) );
					
					if ($send_mail) {
						$message = 'Wordpress Remote Post used to post to Sherpa. Query Sent: ' . $json_query . ' Response: ' . wp_remote_retrieve_response_code($response) . ' - ' . wp_remote_retrieve_response_message($response). ' Result: ' . wp_remote_retrieve_body($response);
						wp_mail($target_email, 'Debug mail for form id ' . $active_form, $message);
					}
				}
			
			}		
			
		} else {
			write_log('Sherpa is not sending form ' . $active_form);
		}

	}
 
	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
    public function scripts() {
        $scripts = array(
            array(
                'handle'  => 'my_script_js',
                'src'     => $this->get_base_url() . '/js/my_script.js',
                'version' => $this->_version,
                'deps'    => array( 'jquery' ),
                'strings' => array(
                    'first'  => esc_html__( 'First Choice', 'sherpa_gform' ),
                    'second' => esc_html__( 'Second Choice', 'sherpa_gform' ),
                    'third'  => esc_html__( 'Third Choice', 'sherpa_gform' )
                ),
                'enqueue' => array(
                    array(
                        'admin_page' => array( 'form_settings' ),
                        'tab'        => 'sherpa_gform'
                    )
                )
            ),
 
        );
 
        return array_merge( parent::scripts(), $scripts );
    }

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
    public function styles() {
        $styles = array(
            array(
                'handle'  => 'my_styles_css',
                'src'     => $this->get_base_url() . '/css/my_styles.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array( 'field_types' => array( 'poll' ) )
                )
            )
        );
 
        return array_merge( parent::styles(), $styles );
    }
 
	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */
    function form_submit_button( $button, $form ) {
        $settings = $this->get_form_settings( $form );
        if ( isset( $settings['enabled'] ) && true == $settings['enabled'] ) {
            $text   = $this->get_plugin_setting( 'mytextbox' );
            $button = "<div>{$text}</div>" . $button;
        }
 
        return $button;
    }

	/**
	 * Creates a custom page for this add-on.
	 */	
    public function plugin_page() {
        $instructions = '';
		
		$instructions .= '<style>.maps, .maps td, .maps th {border: 1px solid black;} .maps td, .maps th {padding: 3px;}</style>';
		$instructions .= '<p>For use only with Gravity Forms v1.9 or greater.</p>';
		$instructions .= '<h2>Main Settings</h2>';
		$instructions .= '<p>Main for settings can be found under admin -> Forms -> Settings -> Sherpa GForm. You will need a the Company ID supplied by Sherpa. The sandbox Company ID is set by default. You will need the correct Company ID supplied by Sherpa.</p>';
		$instructions .= '<p>The endpoint url is also set to the sandbox environment by default. The production endpoint url is https://members.sherpacrm.com/api/lead/create.</p>';
		$instructions .= '<p>If you wish to send a debug email select the "Send a debug email" checkbox. The debug email contains the json query and the response from sherpa, and is useful in debugging without the presence of a log file.</p>';
		$instructions .= '<p>Enter a single valid email into the "Debug email address" field. Invalid email addresses will not send a debug email.</p>';
		$instructions .= '<p>If necessary, set the Sherpa Autoset Values for the fields that are required by Sherpa to process the request. This is a global setting, but it can be overwritten by mapping the appropriate fields at the form level (see Field Mapping, below).</p>';
		$instructions .= '<p>The default values for the Sherpa Autoset Values should be correct for 99% of requests sent.</p>';
		$instructions .= '<h2>Form Settings</h2>';
		$instructions .= '<p>Individual form settings can be found under admin -> Forms -> Forms -> {form name} -> Settings -> Sherpa GForm.</p>';
		$instructions .= '<p>Select the "Send this form to Sherpa" checkbox to attach the form. The Community ID is set to the sandbox Community ID by default. You will need a Community ID supplied by Sherpa.</p>';
		$instructions .= '<p>By default this plugin uses Remote Post (wp_remote_post) to send form data. This can be changed to to use cURL. If you have cURL installed and wish to use this method, select this checkbox.</p>';
		$instructions .= '<h3>Field Mapping</h3>';
		$instructions .= '<p>To map the form fields, select the relevant Field (to be mapped for Sherpa) to the Form Field (from the Gravity Form). Some field values are set by the Sherpa Autoset Values (above), but may be overwritten here.</p>';
		$instructions .= '<p>The form field must be of the correct type. The mapping is as follows:</p>';
		$instructions .= '<ul>';
		$instructions .= '<li>First Name -> textfield</li>';
		$instructions .= '<li>Last Name -> textfield</li>';
		$instructions .= '<li>Email Address -> email</li>';
		$instructions .= '<li>Phone -> phone</li>';
		$instructions .= '<li>Vendor Name -> hidden</li>';
		$instructions .= '<li>Source Name -> hidden</li>';
		$instructions .= '<li>Referral Type -> hidden</li>';
		$instructions .= '<li>Advisor First Name -> hidden</li>';
		$instructions .= '<li>Advisor Last Name -> hidden</li>';
		$instructions .= '<li>Advisor Email -> hidden</li>';
		$instructions .= '<li>Advisor Referral Note -> hidden, textarea or select</li>';
		$instructions .= '<li>Resident First Name -> hidden or textfield</li>';
		$instructions .= '<li>Resident Last Name -> hidden or textfield</li>';
		$instructions .= '<li>Resident Relationship -> hidden or select</li>';
		$instructions .= '</ul>';
		$instructions .= '<p>So make sure when creating your form that you use the correct form field types for the Sherpa field mapping.</p>';
		$instructions .= '<h2>Sherpa Field Mapping Information</h2>';
		$instructions .= '<p>Below is a table of how fields map with this add on. Please refer to your Sherpa documentation for more information.</p>';
		$instructions .= '<table class="maps">';
		$instructions .= '<tr><th>Addon Field Name</th><th>Sherpa Field Name</th><th>Required By Sherpa</th><th>Main Settings Autofill</th></tr>';
		$instructions .= '<tr><td>First Name</td><td>PrimaryContactFirstName</td><td>Yes</td><td>No</td></tr>';
		$instructions .= '<tr><td>Last Name</td><td>PrimaryContactLastName</td><td>Yes</td><td>No</td></tr>';
		$instructions .= '<tr><td>Email Address</td><td>PrimaryContactEmail</td><td>No</td><td>No</td></tr>';
		$instructions .= '<tr><td>Phone</td><td>PrimaryContactHomePhone</td><td>No</td><td>No</td></tr>';
		$instructions .= '<tr><td>Vendor Name</td><td>VendorName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Source Name</td><td>SourceName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Referral Type</td><td>ReferralType</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Advisor First Name</td><td>AdvisorFirstName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Advisor Last Name</td><td>AdvisorLastName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Advisor Email</td><td>AdvisorEmail</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Advisor Referral Note</td><td>AdvisorReferralNote</td><td>No</td><td>No</td></tr>';
		$instructions .= '<tr><td>Resident First Name</td><td>ResidentContactFirstName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Resident Last Name</td><td>ResidentContactLastName</td><td>Yes</td><td>Yes</td></tr>';
		$instructions .= '<tr><td>Resident Relationship</td><td>PrimaryContactResidentRelationship</td><td>No</td><td>Yes</td></tr>';
		
		$instructions .= '</table>';
		
		echo $instructions;		
    }
	
	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
    public function plugin_settings_fields() {
        return array(
            array(
                'title'  => esc_html__( 'Sherpa GForm Settings', 'sherpa_gform' ),
                'fields' => array(
                    array(
                        'name'              => 'company_id',
                        'tooltip'           => esc_html__( 'Sherpa Company ID', 'sherpa_gform' ),
                        'label'             => esc_html__( 'Company ID', 'sherpa_gform' ),
                        'type'              => 'text',
                        'style'             => 'width: 400px;',
						'required' => true,
						'default_value' => 'd74ad6c5-5d2b-11e9-abad-22000ab436c5',
                    ),
					array(
						'label' => esc_html__('The Sherpa Endpoint URL', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'target_url',
						'tooltip' => esc_html__('The endpoint url.'),
						'required' => true,
						'default_value' => 'https://sandbox.sherpacrm.com/api/lead/create',
						'style' => 'width: 400px;',
					),
					array(
						'label' => esc_html__('Send a debug email', 'sherpa_gform'),
						'type' => 'checkbox',
						'name' => 'send_debug_email',
						'choices' => array(
							array(
								'label' => esc_html__('Yes', 'sherpa_gform'),
								'name' => 'send_debug_email'
							),
						),
					),
					array(
						'label' => esc_html__('Debug email address', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'debug_email',
						'default_value' => 'someone@something.com',
						'tooltip' => 'Please be a valid email.',
						'style' => 'width: 300px;',
					),
                ),
            ),
			
			array(
				'title' => esc_html__('Sherpa Autoset Values', 'sherpa_gform'),
				'tooltip' => esc_html__('Default required values to send to Sherpa. Values mapped at the form level will overwrite these values.', 'sherpa_gform'),
				'fields' => array(
					array(
						'label' => esc_html__('Vendor Name', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'vendor_name',
						'required' => true,
						'default_value' => 'Company Website',
					),
					array(
						'label' => esc_html__('Source Name', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'source_name',
						'required' => true,
						'default_value' => 'Company Website',
					),
					array(
						'label' => esc_html__('Referral Type', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'referral_type',
						'required' => true,
						'default_value' => '1',
					),
					array(
						'label' => esc_html__('Advisor First Name', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'advisor_first_name',
						'required' => false,
						'default_value' => '',
					),
					array(
						'label' => esc_html__('Advisor Last Name', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'advisor_last_name',
						'required' => false,
						'default_value' => '',
					),
					array(
						'label' => esc_html__('Advisor Email', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'advisor_email',
						'required' => false,
					),
					array(
						'label' => esc_html__('Resident First Name', 'sherpa_gform'), 
						'type' => 'text',
						'name' => 'resident_contact_first_name',
						'required' => false,
						'default_value' => '',
					),
					array(
						'label' => esc_html__('Resident Last Name', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'resident_contact_last_name',
						'required' => false,
						'default_value' => '',
					),
					array(
						'label' => esc_html__('Resident Relationship', 'sherpa_gform'),
						'type' => 'select',
						'name' => 'primary_contact_resident_relationship',
						'choices' => array(
							array(
								'label' => 'self',
								'value' => 'self'
							),
							array(
								'label' => 'parent',
								'value' => 'parent'
							),
							array(
								'label' => 'grandparent',
								'value' => 'grandparent'
							),
							array(
								'label' => 'friend',
								'value' => 'friend'
							),
							array(
								'label' => 'spouse',
								'value' => 'spouse'
							),
							array(
								'label' => 'other',
								'value' => 'other'
							),
						),
					),
				),
			),
			
        );
    }

	
	
 	/**
	 * Configures the settings which should be rendered on the feed edit page in the Form Settings > You've Got Leads GForm area.
	 *
	 * @return array
	 */
    public function form_settings_fields($form) {
		
        return array(
			array(
				'title' => esc_html__('Sherpa GForm Settings', 'sherpa_gform'),
				'fields' => array(
					array(
						'label' => esc_html__('Send this form to Sherpa'),
						'type' => 'checkbox',
						'name' => 'send_form',
						'tooltip' => esc_html__('Select to send form submissions to Sherpa.', 'sherpa_gform'),
						'choices' => array(
							array(
								'label' => esc_html__('Yes', 'sherpa_gform'),
								'name' => 'send_form'
							),
						),
					),
					array(
						'label' => esc_html__('Community ID', 'sherpa_gform'),
						'type' => 'text',
						'name' => 'community_id',
						'tooltip' => esc_html__('The Sherpa Community ID.', 'sherpa_gform'),
						'required' => true,
						'default_value' => '03907313-5d2c-11e9-abad-22000ab436c5',
						'style' => 'width: 400px;',
					),
					array(
						'label' => esc_html__('Use cURL', 'sherpa_gform'),
						'type' => 'checkbox',
						'name' => 'use_curl',
						'tooltip' => esc_html__('Send form data using cURL. If unselected, Wordpress Remote Post will be used.', 'sherpa_gform'),
						'choices' => array(
							array(
								'label' => esc_html__('Yes', 'sherpa_gform'),
								'name' => 'use_curl',
							),
						),
					),					
				),
				
			),
			array(
				'title'  => esc_html__( 'Map Sherpa Fields', 'sherpa_gform' ),
				'fields' => array(
					array(
						'name'      => 'sherpa_fields',
						'label'     => esc_html__( 'Map Fields', 'sherpa_gform' ),
						'type'      => 'field_map',
						'field_map' => $this->sherpa_fields_for_feed_mapping(),
						'tooltip'   => '<h6>' . esc_html__('Map Fields', 'sherpa_gform' ) . '</h6>' . esc_html__( 'Select which Gravity Form fields pair with their respective third-party service fields.', 'sherpa_gform'),
					),
				),
			),
        );
    }
	
 	/**
	 * Configures the mapping fiels on the GForm config page.
	 *
	 * @return array
	 */
	public function sherpa_fields_for_feed_mapping() {
		return array(
			array(
				'name'          => 'first_name',
				'label'         => esc_html__( 'First Name', 'sherpa_gform' ),
				'required'      => true,
				'field_type'    => array( 'name', 'text', 'hidden' ),
				'tooltip' => esc_html__('Must be a text field type', 'sherpa_gform'),
				'default_value' => $this->get_first_field_by_type( 'name', 3 ),
			),
			array(
				'name'          => 'last_name',
				'label'         => esc_html__( 'Last Name', 'sherpa_gform' ),
				'required'      => true,
				'field_type'    => array( 'name', 'text', 'hidden' ),
				'tooltip' => esc_html__('Must be a text field type', 'sherpa_gform'),
				'default_value' => $this->get_first_field_by_type( 'name', 6 ),
			),
			array(
				'name'          => 'email_address',
				'label'         => esc_html__( 'Email Address', 'sherpa_gform' ),
				'required'      => true,
				'field_type'    => array( 'email', 'hidden' ),
				'tooltip' => esc_html__('Must be an email field type', 'sherpa_gform'),
				'default_value' => $this->get_first_field_by_type( 'email' ),
			),
			array(
				'name' => 'phone',
				'label' => esc_html__('Phone', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('phone', 'hidden'),
				'tooltip' => esc_html__('Must be a phone field type', 'sherpa_gform'),
				'default_value' => $this->get_first_field_by_type( 'phone' ),
			),
			array(
				'name' => 'form_vendor_name',
				'label' => esc_html__('Vendor Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html__('Must be a hidden field type', 'sherpa_gform'),				
			),
			array(
				'name' => 'form_source_name',
				'label' => esc_html__('Source Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html__('Must be a hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_referral_type',
				'label' => esc_html__('Referral Type', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html__('Must be a hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_advisor_first_name',
				'label' => esc_html__('Advisor First Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html('Must be a hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_advisor_last_name',
				'label' => esc_html('Advisor Last Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html('Must be a hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_advisor_email',
				'label' => esc_html('Advisor Email', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden'),
				'tooltip' => esc_html__('Must be a hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'referral_note',
				'label' => esc_html__('Advisor Referral Note', 'sherpa_webform'),
				'required' => false,
				'field_type' => array('text', 'select', 'textarea'),
				'tooltip' => esc_html__('Must be a text, select or textarea field type', 'sherpa_gform'),
				'default_value' => $this->get_first_field_by_type('text', 9),
			),
			array(
				'name' => 'form_resident_first_name',
				'label' => esc_html__('Resident First Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('text', 'hidden'),
				'tooltip' => esc_html__('Must be a text or hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_resident_last_name',
				'label' => esc_html__('Resident Last Name', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('text', 'hidden'),
				'tooltip' => esc_html__('Must be a text or hidden field type', 'sherpa_gform'),
			),
			array(
				'name' => 'form_resident_relationship',
				'label' => esc_html__('Resident Relationship', 'sherpa_gform'),
				'required' => false,
				'field_type' => array('hidden', 'select'),
				'tooltip' => esc_html__('Must be a select or hidden field type', 'sherpa_gform'),
			),
		);
	}
 
}
