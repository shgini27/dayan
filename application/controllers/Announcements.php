<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcements extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("announcement_model");
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
			array("announcement" => array("general" => 1)));

		if(!$this->settings->info->announcements_section) {
			$this->template->error(lang("error_84"));
		}

	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("announcement" => array("general" => 1)));

		$this->template->loadContent("announcement/index.php", array(
			)
		);
	}

	public function announcement_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("announcements.ID", "asc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"announcements.title" => 0
				 ),
				 1 => array(
				 	"announcements.timestamp" => 0
				 ),
				 2 => array(
				 	"users.username" => 0
				 )
			)
		);

		
		$this->datatables->set_total_rows(
			$this->announcement_model
				->get_announcements_total()
		);
		$news = $this->announcement_model->get_announcements($this->datatables);
		
		

		foreach($news->result() as $r) {

			$options = '<a href="'.site_url("announcements/view/" . $r->ID).'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "announcement_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("announcements/edit_announcement/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("announcements/delete_announcement/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}

			$this->datatables->data[] = array(
				$r->title,
				date($this->settings->info->date_format, $r->timestamp),
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add() 
	{
		if(!$this->common->has_permissions(array("admin", "announcement_manager"), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$title = $this->common->nohtml($this->input->post("title"));
		$announcement = $this->lib_filter->go($this->input->post("announcement"));
		$notify = intval($this->input->post("notify"));

		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$id = $this->announcement_model->add(array(
			"title" => $title,
			"announcement" => $announcement,
			"userid" => $this->user->info->ID,
			"timestamp" => time()
			)
		);

		if($notify) {
			// Get all users
			$users = $this->user_model->get_all_users();
			foreach($users->result() as $r) {
				$this->user_model->increment_field($r->ID, "noti_count", 1);
				$this->user_model->add_notification(array(
					"userid" => $r->ID,
					"url" => "announcements/view/" . $id,
					"timestamp" => time(),
					"message" => lang("ctn_819"). ": <strong>" . $title . "</strong>",
					"status" => 0,
					"fromid" => $this->user->info->ID,
					"email" => $r->email,
					"username" => $r->username,
					"email_notification" => $r->email_notification
					)
				);
			}
		}

		$this->session->set_flashdata("globalmsg", lang("success_49"));
		redirect(site_url("announcements"));
	}

	public function view($id) 
	{
		$id = intval($id);
		$announcement = $this->announcement_model->get_announcement($id);
		if($announcement->num_rows() == 0) {
			$this->template->error(lang("error_86"));
		}
		$announcement = $announcement->row();

		$this->template->loadData("activeLink", 
			array("announcement" => array("general" => 1)));

		$this->template->loadContent("announcement/view.php", array(
			"announcement" => $announcement
			)
		);
	}

	public function edit_announcement($id) 
	{
		if(!$this->common->has_permissions(array("admin", "announcement_manager"), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$announcement = $this->announcement_model->get_announcement($id);
		if($announcement->num_rows() == 0) {
			$this->template->error(lang("error_86"));
		}
		$announcement = $announcement->row();

		$this->template->loadData("activeLink", 
			array("announcement" => array("general" => 1)));

		$this->template->loadContent("announcement/edit.php", array(
			"announcement" => $announcement
			)
		);
	}

	public function edit_announcement_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "announcement_manager"), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$announcement = $this->announcement_model->get_announcement($id);
		if($announcement->num_rows() == 0) {
			$this->template->error(lang("error_86"));
		}
		$announcement = $announcement->row();

		$title = $this->common->nohtml($this->input->post("title"));
		$announcement = $this->lib_filter->go($this->input->post("announcement"));

		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$this->announcement_model->update($id, array(
			"title" => $title,
			"announcement" => $announcement
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_50"));
		redirect(site_url("announcements"));
	}

	public function delete_announcement($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "announcement_manager"), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$announcement = $this->announcement_model->get_announcement($id);
		if($announcement->num_rows() == 0) {
			$this->template->error(lang("error_86"));
		}

		$this->announcement_model->delete_announcement($id);
		$this->session->set_flashdata("globalmsg", lang("success_51"));
		redirect(site_url("announcements"));
	}

}

?>