<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Settings extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("invoices_model");

		if(!$this->user->loggedin) $this->template->error(lang("error_1"));
		
		$this->template->loadData("activeLink", 
			array("settings" => array("general" => 1)));
	}

	public function index() 
	{
		$fields = $this->user_model->get_custom_fields_answers(array(
			"edit" => 1
			), $this->user->info->ID);
		$this->template->loadContent("user_settings/index.php", array(
			"fields" => $fields
			)
		);
	}

	public function pro() 
	{
		$this->load->model("register_model");
		$fields = $this->user_model->get_custom_fields_answers(array(
			"edit" => 1
			), $this->user->info->ID);
		
		$this->load->helper('email');
		$this->load->library("upload");
		$email = $this->common->nohtml($this->input->post("email"));
		$first_name = $this->common->nohtml($this->input->post("first_name"));
		$last_name = $this->common->nohtml($this->input->post("last_name"));
		$aboutme = $this->common->nohtml($this->input->post("aboutme"));

		$address_1 = $this->common->nohtml($this->input->post("address_1"));
		$address_2 = $this->common->nohtml($this->input->post("address_2"));
		$city = $this->common->nohtml($this->input->post("city"));
		$state = $this->common->nohtml($this->input->post("state"));
		$zipcode = $this->common->nohtml($this->input->post("zipcode"));
		$country = $this->common->nohtml($this->input->post("country"));

		$this->load->helper('email');

		if (empty($email)) $this->template->error(lang("error_18"));

		if (!valid_email($email)) {
			$this->template->error(lang("error_47"));
		}

		if($email != $this->user->info->email) {
			if (!$this->register_model->checkEmailIsFree($email)) {
				$this->template->error(lang("error_20"));
			}
		}

		$enable_email_notification = 
			intval($this->input->post("enable_email_notification"));
		if($enable_email_notification > 1 || $enable_email_notification < 0) 
			$enable_email_notification = 0;

		if ($this->settings->info->avatar_upload) {
			if (isset($_FILES['userfile']) && $_FILES['userfile']['size'] > 0) {
				$this->upload->initialize(array( 
			       "upload_path" => $this->settings->info->upload_path,
			       "overwrite" => FALSE,
			       "max_filename" => 300,
			       "encrypt_name" => TRUE,
			       "remove_spaces" => TRUE,
			       "allowed_types" => "gif|png|jpg|jpeg",
			       "max_size" => $this->settings->info->file_size
			    ));

			    if (!$this->upload->do_upload()) {
			    	$this->template->error(lang("error_21")
			    	.$this->upload->display_errors());
			    }

			    $data = $this->upload->data();

			    $image = $data['file_name'];
			} else {
				$image= $this->user->info->avatar;
			}
		} else {
			$image= $this->user->info->avatar;
		}

		// Custom Fields
			// Process fields
			$answers = array();
			foreach($fields->result() as $r) {
				$answer = "";
				if($r->type == 0) {
					// Look for simple text entry
					$answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

					if($r->required && empty($answer)) {
						$this->template->error(lang("error_78") . $r->name);
					}
					// Add
					$answers[] = array(
						"fieldid" => $r->ID,
						"answer" => $answer
					);
				} elseif($r->type == 1) {
					// HTML
					$answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

					if($r->required && empty($answer)) {
						$this->template->error(lang("error_78") . $r->name);
					}
					// Add
					$answers[] = array(
						"fieldid" => $r->ID,
						"answer" => $answer
					);
				} elseif($r->type == 2) {
					// Checkbox
					$options = explode(",", $r->options);
					foreach($options as $k=>$v) {
						// Look for checked checkbox and add it to the answer if it's value is 1
						$ans = $this->common->nohtml($this->input->post("cf_cb_" . $r->ID . "_" . $k));
						if($ans) {
							if(empty($answer)) {
								$answer .= $v;
							} else {
								$answer .= ", " . $v;
							}
						}
					}

					if($r->required && empty($answer)) {
						$this->template->error(lang("error_78") . $r->name);
					}
					$answers[] = array(
						"fieldid" => $r->ID,
						"answer" => $answer
					);

				} elseif($r->type == 3) {
					// radio
					$options = explode(",", $r->options);
					if(isset($_POST['cf_radio_' . $r->ID])) {
						$answer = intval($this->common->nohtml($this->input->post("cf_radio_" . $r->ID)));
						
						$flag = false;
						foreach($options as $k=>$v) {
							if($k == $answer) {
								$flag = true;
								$answer = $v;
							}
						}
						if($r->required && !$flag) {
							$this->template->error(lang("error_78") . $r->name);
						}
						if($flag) {
							$answers[] = array(
								"fieldid" => $r->ID,
								"answer" => $answer
							);
						}
					}

				} elseif($r->type == 4) {
					// Dropdown menu
					$options = explode(",", $r->options);
					$answer = intval($this->common->nohtml($this->input->post("cf_" . $r->ID)));
					$flag = false;
					foreach($options as $k=>$v) {
						if($k == $answer) {
							$flag = true;
							$answer = $v;
						}
					}
					if($r->required && !$flag) {
						$this->template->error(lang("error_78") . $r->name);
					}
					if($flag) {
						$answers[] = array(
							"fieldid" => $r->ID,
							"answer" => $answer
						);
					}
				} elseif($r->type == 5) {
					$answer = $this->common->nohtml($this->input->post("cf_" . $r->ID));

					if($r->required && empty($answer)) {
						$this->template->error(lang("error_78") . $r->name);
					}
					if(!empty($answer)) {
						$sd = DateTime::createFromFormat($this->settings->info->date_picker_format, $answer);
						$start_date = $sd->format('Y-m-d H:i:s');
						$start_date_timestamp = $sd->getTimestamp();
					} else {
						$start_date_timestamp = 0;
					}

					// Add
					$answers[] = array(
						"fieldid" => $r->ID,
						"answer" => $start_date_timestamp
					);
				}
			}

			$address_1 = $this->common->nohtml($this->input->post("address_1"));
		$address_2 = $this->common->nohtml($this->input->post("address_2"));
		$city = $this->common->nohtml($this->input->post("city"));
		$state = $this->common->nohtml($this->input->post("state"));
		$zipcode = $this->common->nohtml($this->input->post("zipcode"));
		$country = $this->common->nohtml($this->input->post("country"));


		$this->user_model->update_user($this->user->info->ID, array(
			"email" => $email, 
			"first_name" => $first_name, 
			"last_name" => $last_name,
			"email_notification" => $enable_email_notification,
			"avatar" => $image,
			"aboutme" => $aboutme,
			"address_line_1" => $address_1,
			"address_line_2" => $address_2,
			"city" => $city,
			"state" => $state,
			"zip" => $zipcode,
			"country" => $country
			)
		);

		// Update CF
		// Add Custom Fields data
		foreach($answers as $answer) {
			// Check if field exists
			$field = $this->user_model->get_user_cf($answer['fieldid'], $this->user->info->ID);
			if($field->num_rows() == 0) {
				$this->user_model->add_custom_field(array(
					"userid" => $this->user->info->ID,
					"fieldid" => $answer['fieldid'],
					"value" => $answer['answer']
					)
				);
			} else {
				$this->user_model->update_custom_field($answer['fieldid'], 
					$this->user->info->ID, $answer['answer']);
			}
		}

		

		$this->session->set_flashdata("globalmsg", lang("success_22"));
		redirect(site_url("user_settings"));
	}


	public function change_password() 
	{
		$this->template->loadContent("user_settings/change_password.php", array(
			)
		);
	}

	public function change_password_pro() 
	{
		$current_password = 
			$this->common->nohtml($this->input->post("current_password"));
		$new_pass1 = $this->common->nohtml($this->input->post("new_pass1"));
		$new_pass2 = $this->common->nohtml($this->input->post("new_pass2"));

		if(empty($new_pass1)) $this->template->error(lang("error_45"));
		if($new_pass1 != $new_pass2) $this->template->error(lang("error_22"));

		$phpass = new PasswordHash(12, false);
    	if (!$phpass->CheckPassword($current_password, $this->user->getPassword())) {
    		$this->template->error(lang("error_59"));
    	}

    	$pass = $this->common->encrypt($new_pass1);
    	$this->user_model->update_user($this->user->info->ID, 
    		array("password" => $pass));

    	$this->session->set_flashdata("globalmsg", lang("success_23"));
    	redirect(site_url("user_settings/change_password"));
	}

	public function paying_account() 
	{
		$account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
		if($account->num_rows() == 0) {
			// create
			$this->invoices_model->add_paying_account(array(
				"userid" => $this->user->info->ID
				)
			);

			$account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
			$account = $account->row();
		} else {
			$account = $account->row();
		}

		$this->template->loadContent("user_settings/paying_account.php", array(
			"account" => $account
			)
		);
	}

	public function paying_account_pro() 
	{
		$account = $this->invoices_model->get_user_paying_account($this->user->info->ID);
		if($account->num_rows() == 0) {
			$this->template->error(lang("error_182"));
		}
		$account = $account->row();

		$name = $this->common->nohtml($this->input->post("name"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$paypal_email = $this->common->nohtml($this->input->post("paypal_email"));
		$stripe_secret_key = $this->common->nohtml($this->input->post("stripe_secret_key"));
		$stripe_publishable_key = $this->common->nohtml($this->input->post("stripe_publishable_key"));
		$checkout2_account_number = $this->common->nohtml($this->input->post("checkout2_account_number"));
		$checkout2_secret_key = $this->common->nohtml($this->input->post("checkout2_secret_key"));
		$email = $this->common->nohtml($this->input->post("email"));

		$address_line_1 = $this->common->nohtml($this->input->post("address_line_1"));
		$address_line_2 = $this->common->nohtml($this->input->post("address_line_2"));
		$city = $this->common->nohtml($this->input->post("city"));
		$state = $this->common->nohtml($this->input->post("state"));
		$zip = $this->common->nohtml($this->input->post("zip"));
		$country = $this->common->nohtml($this->input->post("country"));

		$first_name = $this->common->nohtml($this->input->post("first_name"));
		$last_name = $this->common->nohtml($this->input->post("last_name"));

		$this->invoices_model->update_paying_account($account->ID, array(
			"name" => $name,
			"email" => $email,
			"paypal_email" => $paypal_email,
			"stripe_secret_key" => $stripe_secret_key,
			"stripe_publishable_key" => $stripe_publishable_key,
			"checkout2_account_number" => $checkout2_account_number,
			"checkout2_secret_key" => $checkout2_secret_key,
			"address_line_1" => $address_line_1,
			"address_line_2" => $address_line_2,
			"city" => $city,
			"state" => $state,
			"zip" => $zip,
			"country" => $country,
			"first_name" => $first_name,
			"last_name" => $last_name
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_155"));
		redirect(site_url("user_settings/paying_account"));
	}

}

?>