<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subjects extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("subjects_model");
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
			array("subject" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "subject_manager", 
			"subject_viewer"), $this->user)) {
			$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->subjects_section) {
			$this->template->error(lang("error_84"));
		}
	}

	public function index() 
	{
		$this->template->loadContent("subjects/index.php", array(
			)
		);
	}

	public function subject_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("subjects.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"subjects.name" => 0
				 ),
			)
		);

		$this->datatables->set_total_rows(
			$this->subjects_model
				->get_total_subjects()
		);
		$subjects = $this->subjects_model->get_subjects($this->datatables);

		foreach($subjects->result() as $r) {

			$options = '<a href="'.site_url("subjects/view/" . $r->ID) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "subject_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("subjects/edit_subject/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("subjects/delete_subject/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			$this->datatables->data[] = array(
				$r->name,
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function view($id) 
	{
		$id = intval($id);
		$subject = $this->subjects_model->get_subject($id);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$subject = $subject->row();

		$teachers = $this->subjects_model->get_teachers_by_subject($id);

		$this->template->loadContent("subjects/view.php", array(
			"subject" => $subject,
			"teachers" => $teachers
			)
		);
	}

	public function book_page($subjectid) 
	{
		$subjectid = intval($subjectid);

		$this->load->library("datatables");

		$this->datatables->set_default_order("library_books.title", "asc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"subjects.name" => 0
				 ),
			)
		);

		$this->datatables->set_total_rows(
			$this->subjects_model
				->get_total_books($subjectid)
		);
		$books = $this->subjects_model->get_books($subjectid, $this->datatables);

		foreach($books->result() as $r) {
			
			$this->datatables->data[] = array(
				'<img src="'.site_url() . $this->settings->info->upload_path_relative . "/" . $r->image.'" width="40" height="40">',
				$r->title,
				$r->author,
				'<a href="'.site_url("library/view/" . $r->ID) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>'
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function add() 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}

		$this->template->loadContent("subjects/add.php", array(
			)
		);
	}

	public function add_subject_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}

		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->lib_filter->go($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_158"));
		}

		$this->subjects_model->add_subject(array(
			"name" => $name,
			"description" => $description
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_121"));
		redirect(site_url("subjects"));
	}

	public function delete_subject($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$subject = $this->subjects_model->get_subject($id);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$this->subjects_model->delete_subject($id);
		$this->session->set_flashdata("globalmsg", lang("success_122"));
		redirect(site_url("subjects"));
	}

	public function edit_subject($id) 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$subject = $this->subjects_model->get_subject($id);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$subject = $subject->row();

		$this->template->loadContent("subjects/edit_subject.php", array(
			"subject" => $subject
			)
		);

	}

	public function edit_subject_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$subject = $this->subjects_model->get_subject($id);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$subject = $subject->row();

		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->lib_filter->go($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_158"));
		}

		$this->subjects_model->update_subject($id, array(
			"name" => $name,
			"description" => $description
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_123"));
		redirect(site_url("subjects"));
	}

	public function teachers() 
	{
		$this->template->loadData("activeLink", 
			array("subject" => array("teachers" => 1)));

		$subjects = $this->subjects_model->get_all_subjects();
		$this->template->loadContent("subjects/teachers.php", array(
			"subjects" => $subjects
			)
		);
	}

	public function teacher_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("subject_teachers.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"users.username" => 0
				 ),
				 1 => array(
				 	"subjects.name" => 0
				 ),
			)
		);

		$this->datatables->set_total_rows(
			$this->subjects_model
				->get_total_subject_teachers()
		);
		$teachers = $this->subjects_model->get_subject_teachers($this->datatables);

		foreach($teachers->result() as $r) {

			if($r->head == 1) {
				$head = '<label class="margin-left label label-success">'.lang("ctn_775").'</label>';
			} else {
				$head = "";
			}

			$options = '';
			if($this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
				$options = '<a href="'.site_url("subjects/edit_teacher_subject/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("subjects/delete_teacher_subject/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)) . $head,
				$r->name,
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function edit_teacher_subject($id) 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("subject" => array("teachers" => 1)));
		$id = intval($id);
		$teacher = $this->subjects_model->get_subject_teacher($id);
		if($teacher->num_rows() == 0) {
			$this->template->error(lang("error_111"));
		}
		$teacher = $teacher->row();
		$subjects = $this->subjects_model->get_all_subjects();


		$this->template->loadContent("subjects/edit_teacher.php", array(
			"teacher" => $teacher,
			"subjects" => $subjects
			)
		);
	}

	public function edit_teacher_subject_pro($id)
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$teacher = $this->subjects_model->get_subject_teacher($id);
		if($teacher->num_rows() == 0) {
			$this->template->error(lang("error_111"));
		}
		$teacher = $teacher->row();

		$username = $this->common->nohtml($this->input->post("username"));
		$subjectid = intval($this->input->post("subjectid"));
		$head = intval($this->input->post("head"));

		$subject = $this->subjects_model->get_subject($subjectid);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_159"));
		}
		$user = $user->row();

		// Check user role
		$user_role = $this->user_model->get_users_user_role($user->user_role);
		if($user_role->num_rows() == 0) {
			$this->template->error(lang("error_88"));
			$user_role = $user_role->row();
			if(!$user_role->teacher) {
				$this->template->error(lang("error_112"));
			}
		}

		$this->subjects_model->update_teacher_to_subject($id, array(
			"teacherid" => $user->ID, 
			"subjectid" => $subjectid,
			"head" => $head
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_124"));
		redirect(site_url("subjects/teachers"));
	}

	public function delete_teacher_subject($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}

		$id = intval($id);
		$teacher = $this->subjects_model->get_subject_teacher($id);
		if($teacher->num_rows() == 0) {
			$this->template->error(lang("error_111"));
		}

		$this->subjects_model->delete_subject_teacher($id);
		$this->session->set_flashdata("globalmsg", lang("success_125"));
		redirect(site_url("subjects/teachers"));
	}

	public function add_teacher_to_subject() 
	{
		if(!$this->common->has_permissions(array("admin", "subject_manager",), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$username = $this->common->nohtml($this->input->post("username"));
		$subjectid = intval($this->input->post("subjectid"));
		$head = intval($this->input->post("head"));

		$subject = $this->subjects_model->get_subject($subjectid);
		if($subject->num_rows() == 0) {
			$this->template->error(lang("error_93"));
		}

		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_159"));
		}
		$user = $user->row();

		// Check user role
		$user_role = $this->user_model->get_users_user_role($user->user_role);
		if($user_role->num_rows() == 0) {
			$this->template->error(lang("error_88"));
			$user_role = $user_role->row();
			if(!$user_role->teacher) {
				$this->template->error(lang("error_112"));
			}
		}

		$this->subjects_model->add_teacher_to_subject(array(
			"teacherid" => $user->ID, 
			"subjectid" => $subjectid,
			"head" => $head
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_126"));
		redirect(site_url("subjects/teachers"));
	}

	public function get_teachers() 
	{
		$query = $this->common->nohtml($this->input->get("query"));

		if(!empty($query)) {
			$usernames = $this->user_model->get_usernames_by_user_role($query, "teacher");
			if($usernames->num_rows() == 0) {
				echo json_encode(array());
			} else {
				$array = array();
				foreach($usernames->result() as $r) {
					$array[] = $r->username;
				}
				echo json_encode($array);
				exit();
			}
		} else {
			echo json_encode(array());
			exit();
		}
	}

}

?>