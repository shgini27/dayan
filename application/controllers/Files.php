<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("files_model");
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
			array("files" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "files_manager",
			"files_viewer"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->files_section) {
			$this->template->error(lang("error_84"));
		}
	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("files" => array("general" => 1)));

		$this->template->loadContent("files/index.php", array(
			)
		);
	}

	public function file_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("files.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"files.file_name" => 0
				 ),
				 1 => array(
				 	"files.file_size" => 0
				 ),
				 2 => array(
				 	"file_categories.name" => 0
				 ),
				 3 => array(
				 	"users.username" => 0
				 ),
				 4 => array(
				 	"files.timestamp" => 0
				 )
			)
		);

			
		$this->datatables->set_total_rows(
			$this->files_model
				->get_files_total()
		);
		$files = $this->files_model->get_files($this->datatables);

		foreach($files->result() as $r) {
			

			$options = '<a href="'.site_url("files/view/" . $r->ID).'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if(empty($r->file_url)) {
				$options .= ' <a class="btn btn-info btn-xs" href="'.base_url() . $this->settings->info->upload_path_relative. '/'. $r->upload_file_name .'" download><span class="glyphicon glyphicon-save"></span></a>';
			} else {
				$options .= ' <a class="btn btn-info btn-xs" href="'. $r->file_url .'" download><span class="glyphicon glyphicon-save"></span></a>';
			}
			if($this->common->has_permissions(array("admin", "files_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("files/edit_file/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("files/delete_file/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}

			$this->datatables->data[] = array(
				$r->file_name,
				$r->file_size . "kb",
				$r->catname,
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				date($this->settings->info->date_format, $r->timestamp),
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add() 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("files" => array("general" => 1)));

		$categories = $this->files_model->get_categories();

		$this->template->loadContent("files/add.php", array(
			"categories" => $categories
			)
		);
	}

	public function add_file_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$file_name = $this->common->nohtml($this->input->post("file_name"));
		$file_url = $this->common->nohtml($this->input->post("file_url"));
		$notes = $this->lib_filter->go($this->input->post("notes"));
		$categoryid = intval($this->input->post("categoryid"));

		if(empty($file_name)) {
			$this->template->error(lang("error_119"));
		}

		$this->load->library("upload");

		if(empty($file_url)) {
			// Check upload
			if(isset($_FILES['userfile']['size']) && $_FILES['userfile']['size'] > 0) {
				$this->upload->initialize(array(
				   "upload_path" => $this->settings->info->upload_path,
			       "overwrite" => FALSE,
			       "max_filename" => 300,
			       "encrypt_name" => TRUE,
			       "remove_spaces" => TRUE,
			       "allowed_types" => $this->settings->info->file_types,
			       "max_size" => $this->settings->info->file_size,
					)
				);

				if ( ! $this->upload->do_upload('userfile'))
	            {
	                    $error = array('error' => $this->upload->display_errors());

	                    $this->template->error(lang("error_94") . "<br /><br />" .
	                    	 $this->upload->display_errors());
	            }

	            $data = $this->upload->data();
	        } else {
	        	$this->template->error(lang("error_107"));
	        }
		} else {
			$data = array(
				"file_name" => "",
				"file_type" => "External",
				"file_ext" => "",
				"file_size" => ""
			);
		}

		$category = $this->files_model->get_category($categoryid);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}

		$fileid = $this->files_model->add_file(array(
        	"file_name" => $file_name,
        	"file_url" => $file_url,
        	"upload_file_name" => $data['file_name'],
        	"file_type" => $data['file_type'],
        	"extension" => $data['file_ext'],
        	"file_size" => $data['file_size'],
        	"userid" => $this->user->info->ID,
        	"timestamp" => time(),
        	"categoryid" => $categoryid,
        	"notes" => $notes
        	)
        );

		$this->session->set_flashdata("globalmsg", lang("success_85"));
		redirect(site_url("files"));
	}

	public function edit_file($id) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$file = $this->files_model->get_file($id);
		if($file->num_rows() == 0) {
			$this->template->error(lang("error_102"));
		}
		$file = $file->row();

		$categories = $this->files_model->get_categories();

		$this->template->loadContent("files/edit_file.php", array(
			"file" => $file,
			"categories" => $categories
			)
		);
	}

	public function edit_file_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$file = $this->files_model->get_file($id);
		if($file->num_rows() == 0) {
			$this->template->error(lang("error_102"));
		}
		$file = $file->row();

		$file_name = $this->common->nohtml($this->input->post("file_name"));
		$file_url = $this->common->nohtml($this->input->post("file_url"));
		$notes = $this->lib_filter->go($this->input->post("notes"));
		$categoryid = intval($this->input->post("categoryid"));

		if(empty($file_name)) {
			$this->template->error(lang("error_119"));
		}

		$this->load->library("upload");

		if(empty($file_url)) {
			// Check upload
			if(isset($_FILES['userfile']['size']) && $_FILES['userfile']['size'] > 0) {
				$this->upload->initialize(array(
				   "upload_path" => $this->settings->info->upload_path,
			       "overwrite" => FALSE,
			       "max_filename" => 300,
			       "encrypt_name" => TRUE,
			       "remove_spaces" => TRUE,
			       "allowed_types" => $this->settings->info->file_types,
			       "max_size" => $this->settings->info->file_size,
					)
				);

				if ( ! $this->upload->do_upload('userfile'))
	            {
	                    $error = array('error' => $this->upload->display_errors());

	                    $this->template->error(lang("error_120") . "<br /><br />" .
	                    	 $this->upload->display_errors());
	            }

	            $data = $this->upload->data();
	        } else {
	        	$data = array(
					"file_name" => $file->upload_file_name,
					"file_type" => $file->file_type,
					"file_ext" => $file->extension,
					"file_size" => $file->file_size
				);
	        }
		} else {
			$data = array(
				"file_name" => "",
				"file_type" => "External",
				"file_ext" => "",
				"file_size" => ""
			);
		}

		$category = $this->files_model->get_category($categoryid);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}

		$this->files_model->update_file($id, array(
        	"file_name" => $file_name,
        	"file_url" => $file_url,
        	"upload_file_name" => $data['file_name'],
        	"file_type" => $data['file_type'],
        	"extension" => $data['file_ext'],
        	"file_size" => $data['file_size'],
        	"categoryid" => $categoryid,
        	"notes" => $notes
        	)
        );

		$this->session->set_flashdata("globalmsg", lang("success_86"));
		redirect(site_url("files"));
	}

	public function delete_file($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$file = $this->files_model->get_file($id);
		if($file->num_rows() == 0) {
			$this->template->error(lang("error_102"));
		}
		$this->files_model->delete_file($id);
		$this->session->set_flashdata("globalmsg", lang("success_87"));
		redirect(site_url("files"));
	}

	public function view($id) 
	{
		$id = intval($id);
		$file = $this->files_model->get_file($id);
		if($file->num_rows() == 0) {
			$this->template->error(lang("error_102"));
		}
		$file = $file->row();

		$this->template->loadContent("files/view.php", array(
			"file" => $file
			)
		);
	}

	public function categories() 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("files" => array("cats" => 1)));
		
		$categories = $this->files_model->get_categories();
		$this->template->loadContent("files/categories.php", array(
			"categories" => $categories
			)
		);
	}

	public function add_category_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->common->nohtml($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->files_model->add_category(array(
			"name" => $name,
			"description" => $desc
			)
		);

		$this->session->set_flashdata("globalmsg", 
			lang("success_77"));
		redirect(site_url("files/categories"));
	}

	public function delete_category($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$category = $this->files_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$this->files_model->delete_category($id);

		$this->session->set_flashdata("globalmsg", 
			lang("success_78"));
		redirect(site_url("files/categories"));
	}

	public function edit_category($id) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("files" => array("cats" => 1)));

		$id = intval($id);
		$category = $this->files_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$this->template->loadContent("files/edit_category.php", array(
			"category" => $category
			)
		);
	}

	public function edit_category_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "files_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$category = $this->files_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->common->nohtml($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->files_model->update_category($id, array(
			"name" => $name,
			"description" => $desc
			)
		);

		$this->session->set_flashdata("globalmsg", 
			lang("success_79"));
		redirect(site_url("files/categories"));
	}

	public function get_files() 
	{
		$query = $this->common->nohtml($this->input->get("query"));

		if(!empty($query)) {
			$usernames = $this->files_model->get_files_by_name($query);
			if($usernames->num_rows() == 0) {
				echo json_encode(array());
			} else {
				$array = array();
				foreach($usernames->result() as $r) {
					$array[] = $r->file_name;
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