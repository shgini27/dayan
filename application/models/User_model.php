<?php

class User_Model extends CI_Model 
{

	public function getUser($email, $pass) 
	{
		return $this->db->select("ID")
		->where("email", $email)->where("password", $pass)->get("users");
	}

	public function get_user_by_id($userid) 
	{
		return $this->db->where("ID", $userid)->get("users");
	}

	public function get_user($userid) 
	{
		return $this->db->where("ID", $userid)->get("users");
	}

	public function get_user_by_username($username) 
	{
		return $this->db->where("username", $username)->get("users");
	}

	public function delete_user($id) 
	{
		$this->db->where("ID", $id)->delete("users");
	}

	public function get_new_members($limit) 
	{
		return $this->db->select("email, username, joined, oauth_provider, 
			avatar")
		->order_by("ID", "DESC")->limit($limit)->get("users");
	}

	public function get_registered_users_date($month, $year) 
	{
		$s= $this->db->where("joined_date", $month . "-" . $year)->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_oauth_count($provider) 
	{
		$s= $this->db->where("oauth_provider", $provider)->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_total_members_count() 
	{
		$s= $this->db->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_active_today_count() 
	{
		$s= $this->db->where("online_timestamp >", time() - 3600*24)->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_new_today_count() 
	{
		$s= $this->db->where("joined >", time() - 3600*24)->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_online_count() 
	{
		$s= $this->db->where("online_timestamp >", time() - 60*15)->select("COUNT(*) as num")->get("users");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_members($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.first_name",
			"users.last_name",
			"user_roles.name"
			)
		);

		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, users.online_timestamp, users.avatar,
			user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit($datatable->length, $datatable->start)
		->get("users");
	}

	public function get_members_admin($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.first_name",
			"users.last_name",
			"user_roles.name",
			"users.email"
			)
		);

		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, users.online_timestamp, users.avatar,
			user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit($datatable->length, $datatable->start)
		->get("users");
	}

	public function get_members_by_search($search) 
	{
		return $this->db->select("users.username, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit(20)
		->like("users.username", $search)
		->get("users");
	}

	public function search_by_username($search) 
	{
		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit(20)
		->like("users.username", $search)
		->get("users");
	}

	public function search_by_email($search) 
	{
		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit(20)
		->like("users.email", $search)
		->get("users");
	}

	public function search_by_first_name($search) 
	{
		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit(20)
		->like("users.first_name", $search)
		->get("users");
	}

	public function search_by_last_name($search) 
	{
		return $this->db->select("users.username, users.email, users.first_name, 
			users.last_name, users.ID, users.joined, users.oauth_provider,
			users.user_role, user_roles.name as user_role_name")
		->join("user_roles", "user_roles.ID = users.user_role", 
				 	"left outer")
		->limit(20)
		->like("users.last_name", $search)
		->get("users");
	}

	public function update_user($userid, $data) {
		$this->db->where("ID", $userid)->update("users", $data);
	}

	public function check_block_ip() 
	{
		$s = $this->db->where("IP", $_SERVER['REMOTE_ADDR'])->get("ip_block");
		if($s->num_rows() == 0) return false;
		return true;
	}

	public function get_user_groups($userid) 
	{
		return $this->db->where("user_group_users.userid", $userid)
			->select("user_groups.name,user_groups.ID as groupid")
			->join("user_groups", "user_groups.ID = user_group_users.groupid")
			->get("user_group_users");
	}

	public function check_user_in_group($userid, $groupid) 
	{
		$s = $this->db->where("userid", $userid)->where("groupid", $groupid)
			->get("user_group_users");
		if($s->num_rows() == 0) return 0;
		return 1;
	}

	public function get_default_groups() 
	{
		return $this->db->where("default", 1)->get("user_groups");
	}

	public function add_user_to_group($userid, $groupid) 
	{
		$this->db->insert("user_group_users", array(
			"userid" => $userid, 
			"groupid" => $groupid
			)
		);
	}

	public function add_points($userid, $points) 
	{
        $this->db->where("ID", $userid)
        	->set("points", "points+$points", FALSE)->update("users");
    }

    public function get_verify_user($code, $username) 
    {
    	return $this->db
    		->where("activate_code", $code)
    		->where("username", $username)
    		->get("users");
    }

    public function get_user_event($request) 
    {
    	return $this->db->where("IP", $_SERVER['REMOTE_ADDR'])
    		->where("event", $request)
    		->order_by("ID", "DESC")
    		->get("user_events");
    }

    public function add_user_event($data) 
    {
    	$this->db->insert("user_events", $data);
    }

    public function get_custom_fields($data) 
	{
		if(isset($data['register'])) {
			$this->db->where("register", 1);
		}
		return $this->db->get("custom_fields");
	}

	public function add_custom_field($data) 
	{
		$this->db->insert("user_custom_fields", $data);
	}

	public function get_custom_fields_answers($data, $userid) 
	{
		if(isset($data['edit'])) {
			$this->db->where("custom_fields.edit", 1);
		}

		if(isset($data['report'])) {
			$this->db->where("custom_fields.report", 1);
		}
		return $this->db
			->select("custom_fields.ID, custom_fields.name, custom_fields.type,
				custom_fields.required, custom_fields.help_text,
				custom_fields.options,
				user_custom_fields.value")
			->join("user_custom_fields", "user_custom_fields.fieldid = custom_fields.ID
			 AND user_custom_fields.userid = " . $userid, "LEFT OUTER")
			->get("custom_fields");

	}

	public function get_user_cf($fieldid, $userid)
	{
		return $this->db
			->where("fieldid", $fieldid)
			->where("userid", $userid)
			->get("user_custom_fields");
	}

	public function update_custom_field($fieldid, $userid, $value) 
	{
		$this->db->where("fieldid", $fieldid)
			->where("userid", $userid)
			->update("user_custom_fields", array("value" => $value));
	}

	public function get_payment_logs($userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"payment_logs.email"
			)
		);
		return $this->db
			->where("payment_logs.userid", $userid)
			->select("users.ID as userid, users.username, users.email,
			users.avatar, users.online_timestamp,
			payment_logs.email, payment_logs.amount, payment_logs.timestamp, 
			payment_logs.ID, payment_logs.processor")
			->join("users", "users.ID = payment_logs.userid")
			->limit($datatable->length, $datatable->start)
			->get("payment_logs");
	}

	public function get_total_payment_logs_count($userid) 
	{
		$s= $this->db
			->where("userid", $userid)
			->select("COUNT(*) as num")->get("payment_logs");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_usernames_by_user_role($username, $role) 
    {
    	return $this->db
    		->like("users.username", $username)
    		->join("user_roles", "user_roles.ID = users.user_role")
    		->where("user_roles." . $role, 1)
    		->limit(10)
    		->get("users");
    }

    public function get_user_role_count($role) 
    {
    	$s = $this->db
    		->select("COUNT(*) as num")
    		->join("user_roles", "user_roles.ID = users.user_role")
    		->where("user_roles." . $role, 1)
    		->get("users");
    	$r = $s->row();
    	if(isset($r->num)) return $r->num;
    	return 0;
    }

    public function get_users_user_role($roleid) 
    {
    	return $this->db->where("ID", $roleid)->get("user_roles");
    }

    public function get_all_users() 
    {
    	return $this->db->get("users");
    }

    public function get_notifications($userid) 
    {
    	return $this->db
    		->where("user_notifications.userid", $userid)
    		->select("users.ID as userid, users.username, users.avatar,
    			user_notifications.timestamp, user_notifications.message,
    			user_notifications.url, user_notifications.ID, 
    			user_notifications.status")
    		->join("users", "users.ID = user_notifications.fromid")
    		->limit(5)
    		->order_By("user_notifications.ID", "DESC")
    		->get("user_notifications");
    }

    public function get_notifications_unread($userid) 
    {
    	return $this->db
    		->where("user_notifications.userid", $userid)
    		->select("users.ID as userid, users.username, users.avatar,
    			user_notifications.timestamp, user_notifications.message,
    			user_notifications.url, user_notifications.ID, 
    			user_notifications.status")
    		->join("users", "users.ID = user_notifications.fromid")
    		->limit(5)
    		->where("user_notifications.status", 0)
    		->order_By("user_notifications.ID", "DESC")
    		->get("user_notifications");
    }

    public function get_notification($id, $userid) 
    {
    	return $this->db
    		->where("user_notifications.userid", $userid)
    		->where("user_notifications.ID", $id)
    		->select("users.ID as userid, users.username, users.avatar,
    			user_notifications.timestamp, user_notifications.message,
    			user_notifications.url, user_notifications.ID, 
    			user_notifications.status")
    		->join("users", "users.ID = user_notifications.fromid")
    		->order_By("user_notifications.ID", "DESC")
    		->get("user_notifications");
    }

    public function get_notifications_all($userid, $datatable) 
    {
    	$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"user_notifications.message",
			)
		);

    	return $this->db
    		->where("user_notifications.userid", $userid)
    		->select("users.ID as userid, users.username, users.avatar,
    			users.online_timestamp,
    			user_notifications.timestamp, user_notifications.message,
    			user_notifications.url, user_notifications.ID, 
    			user_notifications.status")
    		->join("users", "users.ID = user_notifications.fromid")
    		->limit($datatable->length, $datatable->start)
    		->order_By("user_notifications.ID", "DESC")
    		->get("user_notifications");
    }

    public function get_notifications_all_fp($userid, $page, $max=10) 
    {
    	return $this->db
    		->where("user_notifications.userid", $userid)
    		->select("users.ID as userid, users.username, users.avatar,
    			users.online_timestamp,
    			user_notifications.timestamp, user_notifications.message,
    			user_notifications.url, user_notifications.ID, 
    			user_notifications.status")
    		->join("users", "users.ID = user_notifications.fromid")
    		->limit($max, $page)
    		->order_By("user_notifications.ID", "DESC")
    		->get("user_notifications");
    }

    public function get_notifications_all_total($userid) 
    {
    	$s = $this->db
    		->where("user_notifications.userid", $userid)
    		->select("COUNT(*) as num")
    		->get("user_notifications");
    	$r = $s->row();
    	if(isset($r->num)) return $r->num;
    	return 0;
    }

    public function add_notification($data) 
    {
    	if(isset($data['email']) && isset($data['email_notification']) 
    		&& $data['email_notification']) {
	    	// Send Email
	    	$subject = $this->settings->info->site_name . lang("ctn_642");
	    	
	    	if(isset($data['username'])) {
				$username = $data['username'] . ",";
			} else {
				$username = lang("ctn_916");
			}

			if(!isset($_COOKIE['language'])) {
				// Get first language in list as default
				$lang = $this->config->item("language");
			} else {
				$lang = $this->common->nohtml($_COOKIE["language"]);
			}

			// Send Email
			$this->load->model("home_model");
			$email_template = $this->home_model->get_email_template_hook("new_notification", $lang);
			if($email_template->num_rows() == 0) {
				$this->template->error(lang("error_48"));
			}
			$email_template = $email_template->row();

			$email_template->message = $this->common->replace_keywords(array(
				"[NAME]" => $username,
				"[SITE_URL]" => site_url(),
				"[SITE_NAME]" =>  $this->settings->info->site_name
				),
			$email_template->message);

			$this->common->send_email($subject,
				 $email_template->message, $data['email']);
		}
		unset($data['email']);
		unset($data['email_notification']);
		unset($data['username']);
    	$this->db->insert("user_notifications", $data);
    }

    public function update_notification($id, $data) 
    {
    	$this->db->where("ID", $id)->update("user_notifications", $data);
    }

    public function increment_field($userid, $field, $amount) 
    {
    	$this->db->where("ID", $userid)
    		->set($field, $field . '+' . $amount, FALSE)->update("users");
    }

    public function decrement_field($userid, $field, $amount) 
    {
    	$this->db->where("ID", $userid)
    		->set($field, $field . '-' . $amount, FALSE)->update("users");
    }

    public function get_usernames($username) 
    {
    	return $this->db->like("username", $username)->limit(10)->get("users");
    }

    public function get_total_parents() 
    {
    	$s = $this->db
    		->select("COUNT(*) as num")
    		->join("user_roles", "users.user_role = user_roles.ID")
    		->where("user_roles.parent", 1)
    		->get("users");
    	$r = $s->row();
    	if(isset($r->num)) return $r->num;
    	return 0;
    }

    public function get_parents($datatable) 
    {
    	$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.first_name",
			"users.last_name",
			"users.email"
			)
		);

		return $this->db
			->where("user_roles.parent", 1)
			->select("users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name, users.ID, users.email")
			->join("user_roles", "users.user_role = user_roles.ID")
			->limit($datatable->length, $datatable->start)
			->get("users");
    }

    public function get_parent_roles() 
    {
    	return $this->db->where("user_roles.parent", 1)->get("user_roles");
    }

    public function get_student_roles() 
    {
    	return $this->db->where("user_roles.student", 1)->get("user_roles");
    }


    public function get_parent($id) 
    {
    	return $this->db
			->where("user_roles.parent", 1)
			->where("users.ID", $id)
			->select("users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name, users.ID, users.email,
				users.points, users.aboutme, users.joined, users.active,
				users.IP, users.password, users.address_line_1,
				users.address_line_2, users.state, users.city, users.country,
				users.zip")
			->join("user_roles", "users.user_role = user_roles.ID")
			->get("users");
    }

    public function check_child($parentid, $studentid) 
    {
    	return $this->db->where("parentid", $parentid)
    		->where("studentid", $studentid)->get("parent_children");
    }

    public function add_child($data) 
    {
    	$this->db->insert("parent_children", $data);
    }

    public function get_parent_children($parentid) 
    {
    	return $this->db
    		->where("parent_children.parentid", $parentid)
    		->select("parent_children.ID, parent_children.studentid,
    			users.username, users.avatar, users.first_name,
    			users.last_name, users.online_timestamp")
    		->join("users", "users.ID = parent_children.studentid")
    		->get("parent_children");
    }

    public function get_child($id) 
    {
    	return $this->db->where("ID", $id)->get("parent_children");
    }

    public function delete_child($id) 
    {
    	$this->db->where("ID", $id)->delete("parent_children");
    }


}

?>