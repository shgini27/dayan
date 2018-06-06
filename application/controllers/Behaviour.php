<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Behaviour extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("behaviour_model");
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
			array("behaviour" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "behaviour_manager",
			"behaviour_viewer"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->behaviour_section) {
			$this->template->error(lang("error_84"));
		}
	}

	public function index() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("behaviour" => array("general" => 1)));

		$this->template->loadContent("behaviour/index.php", array(
			"page" => "index"
			)
		);
	}

	public function your() 
	{
		$this->template->loadData("activeLink", 
			array("behaviour" => array("your" => 1)));

		$this->template->loadContent("behaviour/index.php", array(
			"page" => "your"
			)
		);
	}

	public function record_page($page) 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("behaviour_records.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"users.username" => 0
				 ),
				 1 => array(
				 	"rules.name" => 0
				 ),
				 2 => array(
				 	"behaviour_records.timestamp" => 0
				 ),
				 3 => array(
				 	"users2.username" => 0
				 )
			)
		);

		if($page == "index") {
			if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
			$this->datatables->set_total_rows(
				$this->behaviour_model
					->get_records_total()
			);
			$records = $this->behaviour_model->get_records($this->datatables);
		} elseif($page == "your") {
			$this->datatables->set_total_rows(
				$this->behaviour_model
					->get_records_total_user($this->user->info->ID)
			);
			$records = $this->behaviour_model->get_records_user($this->user->info->ID, $this->datatables);
		}
		
		

		foreach($records->result() as $r) {

			$options = "";
			if($this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
				$options = ' <a href="'.site_url("behaviour/edit_record/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("behaviour/delete_record/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				$r->name,
				date($this->settings->info->date_format, $r->timestamp),
				$this->common->get_user_display(array("username" => $r->username2, "avatar" => $r->avatar2, "online_timestamp" => $r->online_timestamp2, "first_name" => $r->first_name2, "last_name" => $r->last_name2)),
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add_record() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("behaviour" => array("general" => 1)));

		$rules = $this->behaviour_model->get_all_rules();

		$this->template->loadContent("behaviour/add.php", array(
			"rules" => $rules
			)
		);
	}

	public function add_record_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$username = $this->common->nohtml($this->input->post("username"));
		$ruleid = intval($this->input->post("ruleid"));
		$incident = $this->lib_filter->go($this->input->post("incident"));
		$date = $this->common->nohtml($this->input->post("date"));

		// Get student
		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_87"));
		}
		$user = $user->row();

		$userid = $user->ID;

		$user_role = $this->user_model->get_users_user_role($user->user_role);
		if($user_role->num_rows() == 0) {
			$this->template->error(lang("error_88"));
			$user_role = $user_role->row();
			if(!$user_role->student) {
				$this->template->error(lang("error_89"));
			}
		}

		$rule = $this->behaviour_model->get_rule($ruleid);
		if($rule->num_rows() == 0) {
			$this->template->erro(lang("error_90"));
		}

		// Date
		if(!empty($date)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
			$dd_timestamp = $dd->getTimestamp();
		} else {
			$dd_timestamp = 0;
		}

		$this->behaviour_model->add_record(array(
			"userid" => $userid,
			"timestamp" => $dd_timestamp,
			"incident" => $incident,
			"ruleid" => $ruleid,
			"teacherid" => $this->user->info->ID
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_52"));
		redirect(site_url("behaviour"));
	}

	public function edit_record($id) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("behaviour" => array("general" => 1)));

		$id = intval($id);
		$record = $this->behaviour_model->get_record($id);
		if($record->num_rows() == 0) {
			$this->template->error(lang("error_91"));
		}
		$record = $record->row();


		$rules = $this->behaviour_model->get_all_rules();

		$this->template->loadContent("behaviour/edit_record.php", array(
			"rules" => $rules,
			"record" => $record
			)
		);
	}

	public function edit_record_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$record = $this->behaviour_model->get_record($id);
		if($record->num_rows() == 0) {
			$this->template->error(lang("error_91"));
		}
		$record = $record->row();

		$username = $this->common->nohtml($this->input->post("username"));
		$ruleid = intval($this->input->post("ruleid"));
		$incident = $this->lib_filter->go($this->input->post("incident"));
		$date = $this->common->nohtml($this->input->post("date"));

		// Get student
		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_87"));
		}
		$user = $user->row();

		$userid = $user->ID;

		$user_role = $this->user_model->get_users_user_role($user->user_role);
		if($user_role->num_rows() == 0) {
			$this->template->error(lang("error_88"));
			$user_role = $user_role->row();
			if(!$user_role->student) {
				$this->template->error(lang("error_89"));
			}
		}

		$rule = $this->behaviour_model->get_rule($ruleid);
		if($rule->num_rows() == 0) {
			$this->template->erro(lang("error_90"));
		}

		// Date
		if(!empty($date)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
			$dd_timestamp = $dd->getTimestamp();
		} else {
			$dd_timestamp = 0;
		}

		$this->behaviour_model->update_record($id, array(
			"userid" => $userid,
			"timestamp" => $dd_timestamp,
			"incident" => $incident,
			"ruleid" => $ruleid,
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_53"));
		redirect(site_url("behaviour"));
	}

	public function delete_record($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash() ) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$record = $this->behaviour_model->get_record($id);
		if($record->num_rows() == 0) {
			$this->template->error(lang("error_91"));
		}

		$this->behaviour_model->delete_record($id);
		$this->session->set_flashdata("globalmsg", lang("success_54"));
		redirect(site_url("behaviour"));
	}

	public function rules() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("behaviour" => array("rules" => 1)));

		$this->template->loadContent("behaviour/rules.php", array(
			)
		);
	}

	public function rule_page() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->load->library("datatables");

		$this->datatables->set_default_order("rules.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"rules.name" => 0
				 ),
				 1 => array(
				 	"rules.points" => 0
				 )
			)
		);

		$this->datatables->set_total_rows(
			$this->behaviour_model
				->get_rules_total()
		);
		$rules = $this->behaviour_model->get_rules($this->datatables);
		
		

		foreach($rules->result() as $r) {

			$options = ' <a href="'.site_url("behaviour/edit_rule/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("behaviour/delete_rule/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			
			$this->datatables->data[] = array(
				$r->name,
				$r->points,
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add_rule() 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$name = $this->common->nohtml($this->input->post("name"));
		$points = intval($this->input->post("points"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->behaviour_model->add_rule(array(
			"name" => $name,
			"points" => $points
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_55"));
		redirect(site_url("behaviour/rules"));
	}

	public function edit_rule($id) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$rule = $this->behaviour_model->get_rule($id);
		if($rule->num_rows() == 0) {
			$this->template->error(lang("error_90"));
		}
		$rule = $rule->row();

		$this->template->loadData("activeLink", 
			array("behaviour" => array("rules" => 1)));

		$this->template->loadContent("behaviour/edit_rule.php", array(
			"rule" => $rule
			)
		);

	}

	public function edit_rule_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$rule = $this->behaviour_model->get_rule($id);
		if($rule->num_rows() == 0) {
			$this->template->error(lang("error_90"));
		}
		$rule = $rule->row();

		$name = $this->common->nohtml($this->input->post("name"));
		$points = intval($this->input->post("points"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->behaviour_model->update_rule($id, array(
			"name" => $name,
			"points" => $points
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_56"));
		redirect(site_url("behaviour/rules"));
	}

	public function delete_rule($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "behaviour_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$rule = $this->behaviour_model->get_rule($id);
		if($rule->num_rows() == 0) {
			$this->template->error(lang("error_90"));
		}

		$this->behaviour_model->delete_rule($id);
		$this->session->set_flashdata("globalmsg", lang("success_57"));
		redirect(site_url("behaviour/rules"));
	}

}

?>