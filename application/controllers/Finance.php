<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("finance_model");
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
			array("finance" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "finance_manager",
			"finance_viewer"), $this->user)) {
			$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->finance_section) {
			$this->template->error(lang("error_84"));
		}

	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("finance" => array("general" => 1)));


		$this->template->loadContent("finance/index.php", array(
			)
		);
	}

	public function finance_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("finance.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"finance.title" => 0
				 ),
				 1 => array(
				 	"finance.amount" => 0
				 ),
				 2 => array(
				 	"finance_categories.name" => 0
				 ),
				 3 => array(
				 	"users.username" => 0
				 ),
				 4 => array(
				 	"finance.timestamp" => 0
				 )
			)
		);

			
		$this->datatables->set_total_rows(
			$this->finance_model
				->get_all_finances_total()
		);
		$finances = $this->finance_model->get_all_finances($this->datatables);

		foreach($finances->result() as $r) {
			if($r->amount > 0) {
				$fcl = "finance-positive";
			} elseif($r->amount < 0) {
				$fcl = "finance-negative";
			} else {
				$fcl = "";
			}
			$amount = '<span class="'.$fcl.'">'.number_format($r->amount,2).'</span>';

			$options = "";
			if($this->common->has_permissions(array("admin", "finance_manager"), $this->user)) {
				$options = '<a href="'.site_url("finance/edit_finance/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("finance/delete_finance/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}

			$this->datatables->data[] = array(
				$r->title,
				$amount,
				$r->catname,
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				date($this->settings->info->date_format, $r->timestamp),
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add_finance() 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadExternal(
			'<script src="//cdn.ckeditor.com/4.5.8/standard/ckeditor.js">
			</script>'
		);
		$this->template->loadData("activeLink", 
			array("finance" => array("general" => 1)));

		$categories = $this->finance_model->get_categories();

		

		$this->template->loadContent("finance/add.php", array(
			"categories" => $categories
			)
		);
	}

	public function add_finance_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$title = $this->common->nohtml($this->input->post("title"));
		$notes = $this->lib_filter->go($this->input->post("notes"));
		$catid = intval($this->input->post("catid"));
		$amount = floatval($this->input->post("amount"));

		$date = $this->common->nohtml($this->input->post("date"));

		if(!empty($date)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
			$dd_timestamp = $dd->getTimestamp();
		} else {
			$dd_timestamp = 0;
		}
		
		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$cat = $this->finance_model->get_category($catid);
		if($cat->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}

		// Add
		$this->finance_model->add_finance(array(
			"title" => $title,
			"notes" => $notes,
			"categoryid" => $catid,
			"userid" => $this->user->info->ID,
			"amount" => $amount,
			"timestamp" => $dd_timestamp,
			"month" => date("n", $dd_timestamp),
			"year" => date("Y", $dd_timestamp),
			"time_date" => date("Y-m-d", $dd_timestamp)
			)
		);


		$this->session->set_flashdata("globalmsg", 
			lang("success_88"));
		redirect(site_url("finance"));
	}

	public function edit_finance($id) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadExternal(
			'<script src="//cdn.ckeditor.com/4.5.8/standard/ckeditor.js">
			</script>'
		);
		$id = intval($id);
		$finance = $this->finance_model->get_finance($id);
		if($finance->num_rows() == 0) {
			$this->template->error(lang("error_121"));
		}
		$finance = $finance->row();

		$this->template->loadData("activeLink", 
			array("finance" => array("general" => 1)));
		$categories = $this->finance_model->get_categories();


		$this->template->loadContent("finance/edit_finance.php", array(
			"categories" => $categories,
			"finance" => $finance
			)
		);
	}

	public function edit_finance_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$finance = $this->finance_model->get_finance($id);
		if($finance->num_rows() == 0) {
			$this->template->error(lang("error_121"));
		}
		$finance = $finance->row();

		$title = $this->common->nohtml($this->input->post("title"));
		$notes = $this->lib_filter->go($this->input->post("notes"));
		$catid = intval($this->input->post("catid"));
		$amount = floatval($this->input->post("amount"));

		$date = $this->common->nohtml($this->input->post("date"));
		
		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$cat = $this->finance_model->get_category($catid);
		if($cat->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}

		if(!empty($date)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $date);
			$dd_timestamp = $dd->getTimestamp();
		} else {
			$dd_timestamp = 0;
		}
	

		// Add
		$this->finance_model->update_finance($id, array(
			"title" => $title,
			"notes" => $notes,
			"categoryid" => $catid,
			"amount" => $amount,
			"timestamp" => $dd_timestamp,
			"month" => date("n", $dd_timestamp),
			"year" => date("Y", $dd_timestamp),
			"time_date" => date("Y-m-d", $dd_timestamp)
			)
		);

		$this->session->set_flashdata("globalmsg", 
			lang("success_89"));
		redirect(site_url("finance"));
	}

	public function delete_finance($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$finance = $this->finance_model->get_finance($id);
		if($finance->num_rows() == 0) {
			$this->template->error(lang("error_121"));
		}
		$finance = $finance->row();
		

		$this->finance_model->delete_finance($id);

		$this->session->set_flashdata("globalmsg", 
			lang("success_90"));
		redirect(site_url("finance"));
	}

	public function categories() 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("finance" => array("cats" => 1)));
		
		$categories = $this->finance_model->get_categories();
		$this->template->loadContent("finance/categories.php", array(
			"categories" => $categories
			)
		);
	}

	public function add_category_pro() 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->lib_filter->go($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->finance_model->add_category(array(
			"name" => $name,
			"description" => $desc
			)
		);

		$this->session->set_flashdata("globalmsg", 
			lang("success_77"));
		redirect(site_url("finance/categories"));
	}

	public function delete_category($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$category = $this->finance_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$this->finance_model->delete_category($id);

		$this->session->set_flashdata("globalmsg", 
			lang("success_78"));
		redirect(site_url("finance/categories"));
	}

	public function edit_category($id) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$this->template->loadData("activeLink", 
			array("finance" => array("cats" => 1)));

		$id = intval($id);
		$category = $this->finance_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$this->template->loadContent("finance/edit_category.php", array(
			"category" => $category
			)
		);
	}

	public function edit_category_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "finance_manager"),
		 $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$category = $this->finance_model->get_category($id);
		if($category->num_rows() == 0) {
			$this->template->error(lang("error_94"));
		}
		$category = $category->row();

		$name = $this->common->nohtml($this->input->post("name"));
		$desc = $this->lib_filter->go($this->input->post("description"));

		if(empty($name)) {
			$this->template->error(lang("error_122"));
		}

		$this->finance_model->update_category($id, array(
			"name" => $name,
			"description" => $desc
			)
		);

		$this->session->set_flashdata("globalmsg", 
			lang("success_79"));
		redirect(site_url("finance/categories"));
	}

}

?>