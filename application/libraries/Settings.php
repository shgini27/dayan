<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Settings 
{

	var $info=array();

	var $version = "1.3";

	public function __construct() 
	{
		$CI =& get_instance();
		$site = $CI->db->select("site_name,site_desc,site_email,
			upload_path_relative, upload_path, site_logo, register,
			 disable_captcha, date_format, avatar_upload, file_types,
			 twitter_consumer_key, twitter_consumer_secret, disable_social_login
			 ,facebook_app_id, facebook_app_secret, google_client_id,
			 google_client_secret, file_size, paypal_email, paypal_currency,
			 payment_enabled, payment_symbol, global_premium, install,
			 login_protect, activate_account, default_user_role,
			 secure_login, stripe_secret_key, stripe_publish_key,
			 google_recaptcha, google_recaptcha_secret, google_recaptcha_key,
			 logo_option, layout, front_layout, student_view_groups, teacher_group_manage,
			 teacher_class_manage, teacher_class, date_picker_format,
			 calendar_picker_format, reserve_books, fp_currency_symbol,
			 cache_time, announcements_section, classes_section, subjects_section,
			 students_section, files_section, finance_section, invoices_section,
			 behaviour_section, library_section, reports_section, parent_section,
			 hostel_section, documents_section, allow_student_profile")
		->where("ID", 1)
		->get("site_settings");
		
		if($site->num_rows() == 0) {
			$CI->template->error(
				"You are missing the site settings database row."
			);
		} else {
			$this->info = $site->row();
		}
	}

}

?>