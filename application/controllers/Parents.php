<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parents extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("students_model");
		$this->load->model("classes_model");
		$this->load->model("admin_model");
		if(!$this->user->loggedin) {
			redirect(site_url("login"));
		}
		
		// If the user does not have premium. 
		// -1 means they have unlimited premium
		if($this->settings->info->global_premium && 
			($this->user->info->premium_time != -1 && 
				$this->user->info->premium_time < time()) ) {
			$this->session->set_flashdata("globalmsg", lang("success_29"));
			redirect(site_url("funds/plans"));
		}

		$this->template->loadData("activeLink", 
			array("parent" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "parent_manager"
			), $this->user)) {
			$this->template->error(lang("error_2"));
		}
	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("parent" => array("general" => 1)));

		$this->template->loadContent("parents/index.php", array(
			)
		);
	}

	public function view($id) 
	{
		$this->template->loadData("activeLink", 
			array("parent" => array("general" => 1)));
		$id = intval($id);
		$parent = $this->user_model->get_user($id);
		if($parent->num_rows() == 0) {
			$this->template->error(lang("error_52"));
		}

		$parent = $parent->row();

		// Check user is a parent user role
		$role = $this->user_model->get_users_user_role($parent->user_role);
		if($role->num_rows() == 0) {
			$this->template->error(lang("error_162"));
		}
		$role = $role->row();
		if($role->parent != 1) {
			$this->template->error(lang("error_162"));
		}

		$fields = $this->user_model->get_custom_fields_answers(array(
			), $id);

		$children = $this->user_model->get_parent_children($id);

		$this->template->loadContent("parents/view.php", array(
			"student" => $parent,
			"fields" => $fields,
			"children" => $children
			)
		);
	}

	public function add_child($id) 
	{
		$this->template->loadData("activeLink", 
			array("parent" => array("general" => 1)));
		$id = intval($id);
		$parent = $this->user_model->get_user($id);
		if($parent->num_rows() == 0) {
			$this->template->error(lang("error_52"));
		}

		$parent = $parent->row();

		// Check user is a parent user role
		$role = $this->user_model->get_users_user_role($parent->user_role);
		if($role->num_rows() == 0) {
			$this->template->error(lang("error_162"));
		}
		$role = $role->row();
		if($role->parent != 1) {
			$this->template->error(lang("error_162"));
		}

		$username = $this->common->nohtml($this->input->post("username"));

		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_111"));
		}
		$user = $user->row();
		$userid = $user->ID;

		// Check user role
		$user_role = $this->user_model->get_users_user_role($user->user_role);
		if($user_role->num_rows() == 0) {
			$this->template->error(lang("error_88"));
			$user_role = $user_role->row();
			if(!$user_role->student) {
				$this->template->error(lang("error_114"));
			}
		}

		// Check parent doesn't already have this child
		$child = $this->user_model->check_child($parent->ID, $userid);
		if($child->num_rows() > 0) {
			$this->template->error(lang("error_165"));
		}

		$this->user_model->add_child(array(
			"parentid" => $parent->ID,
			"studentid" => $userid
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_133"));
		redirect(site_url("parents/view/" . $parent->ID));
	}

	public function parent_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("users.username", "asc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"users.username" => 0
				 ),
				 1 => array(
				 	"users.email" => 0
				 ),
			)
		);

		$this->datatables->set_total_rows(
			$this->user_model
				->get_total_parents()
		);
		$users = $this->user_model->get_parents($this->datatables);
		

		foreach($users->result() as $r) {

			$options = '<a href="'.site_url("parents/view/" . $r->ID) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "parent_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("parents/edit_parent/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("parents/delete_parent/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				$r->email,
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function delete_child($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}

		$id = intval($id);
		$child = $this->user_model->get_child($id);
		if($child->num_rows() == 0) {
			$this->template->error(lang("error_166"));
		}
		$child = $child->row();

		$this->user_model->delete_child($id);
		$this->session->set_flashdata("globalmsg", lang("success_134"));
		redirect(site_url("parents/view/" . $child->parentid));
	}

	public function add_parent() 
	{
		$this->template->loadData("activeLink", 
			array("parent" => array("general" => 1)));

		$user_roles = $this->user_model->get_parent_roles();
		$fields = $this->user_model->get_custom_fields(array());

		$this->template->loadContent("parents/add.php", array(
			"form" => $this->common->get_user_registration_fields(
				"add_parent", // Hook
				"parents", // Redirect
				null,  // user object
				$user_roles, // user roles
				$fields, // custom fields
				array(
					"new_user" => true,
					"credits" => false,
					"activate_user" => false
				) // Flags
			)
			)
		);
	}

	public function edit_parent($id) 
	{
		$id = intval($id);
		$parent = $this->user_model->get_user($id);
		if($parent->num_rows() == 0) {
			$this->template->error(lang("error_52"));
		}

		$parent = $parent->row();

		// Check user is a parent user role
		$role = $this->user_model->get_users_user_role($parent->user_role);
		if($role->num_rows() == 0) {
			$this->template->error(lang("error_162"));
		}
		$role = $role->row();
		if($role->parent != 1) {
			$this->template->error(lang("error_162"));
		}

		$user_roles = $this->user_model->get_parent_roles();

		$fields = $this->user_model->get_custom_fields_answers(array(
			), $id);

		$this->template->loadContent("parents/edit_parent.php", array(
			"form" => $this->common->get_user_registration_fields(
				"edit_parent",
				"parents",
				$parent, 
				$user_roles, 
				$fields, // custom fields
				array(
					"credits" => false,
					"activate_user" => false
				) // Flags
			)
			)
		);
	}

	public function add_parent_pro() 
	{
		$this->load->model("register_model");
		$email = $this->input->post("email", true);
		$first_name = $this->common->nohtml(
			$this->input->post("first_name", true));
		$last_name = $this->common->nohtml(
			$this->input->post("last_name", true));
		$pass = $this->common->nohtml(
			$this->input->post("password", true));
		$pass2 = $this->common->nohtml(
			$this->input->post("password2", true));
		$captcha = $this->input->post("captcha", true);
		$username = $this->common->nohtml(
			$this->input->post("username", true));
		$user_role = intval($this->input->post("user_role"));

		if($user_role > 0) {
			$role = $this->admin_model->get_user_role($user_role);
			if($role->num_rows() == 0) $this->template->error(lang("error_65"));
			$role = $role->row();
			if($role->parent != 1) {
				$this->template->error("Invalid user role!");
			}
		}


		if (strlen($username) < 3) $this->template->error(lang("error_14"));

		if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
			$this->template->error(lang("error_15"));
		}

		if (!$this->register_model->check_username_is_free($username)) {
			 $this->template->error(lang("error_16"));
		}

		if ($pass != $pass2) $this->template->error(lang("error_22"));

		if (strlen($pass) <= 5) {
			 $this->template->error(lang("error_17"));
		}

		$this->load->helper('email');

		if (empty($email)) {
				$this->template->error(lang("error_18"));
		}

		if (!valid_email($email)) {
			$this->template->error(lang("error_19"));
		}

		if (!$this->register_model->checkEmailIsFree($email)) {
			 $this->template->error(lang("error_20"));
		}

		$pass = $this->common->encrypt($pass);
		$this->register_model->add_user(array(
			"username" => $username,
			"email" => $email,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"password" => $pass,
			"user_role" => $user_role,
			"IP" => $_SERVER['REMOTE_ADDR'],
			"joined" => time(),
			"joined_date" => date("n-Y"),
			"active" => 1
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_11"));
		redirect(site_url("parents"));
	}

	public function delete_parent($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$parent = $this->user_model->get_parent($id);
		if($parent->num_rows() == 0) {
			$this->template->error(lang("error_52"));
		}

		$parent = $parent->row();

		$this->user_model->delete_user($id);
		$this->session->set_flashdata("globalmsg", lang("success_135"));
		redirect(site_url("parents"));

	}

}

?>