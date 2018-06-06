<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Library extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("library_model");
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
			array("library" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "library_manager",
			"library_viewer" 
				), $this->user)) {
				$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->library_section) {
			$this->template->error(lang("error_84"));
		}
	}

	public function index() 
	{
		$subjects = $this->subjects_model->get_all_subjects();
		$this->template->loadContent("library/index.php", array(
			"subjects" => $subjects
			)
		);
	}

	public function book_page() 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("library_books.title", "asc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 1 => array(
				 	"library_books.title" => 0
				 ),
				 2 => array(
				 	"library_books.author" => 0
				 ),
				 3 => array(
				 	"subjects.name" => 0
				 ),
				 4 => array(
				 	"library_books.stock" => 0
				 ),
			)
		);

		

		$this->datatables->set_total_rows(
			$this->library_model->get_total_books()
		);
		$books = $this->library_model->get_books($this->datatables);
		

		foreach($books->result() as $r) {

			$options = '<a href="'.site_url("library/view/" . $r->ID) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "library_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("library/edit_book/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("library/delete_book/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			$this->datatables->data[] = array(
				'<img src="'.base_url() . $this->settings->info->upload_path_relative . "/" . $r->image .'" width="40" height="40">',
				$r->title,
				$r->author,
				$r->name,
				$r->stock,
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function add_book() 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$title = $this->common->nohtml($this->input->post("title"));
		$desc = $this->lib_filter->go($this->input->post("description"));
		$author = $this->common->nohtml($this->input->post("author"));
		$stock = intval($this->input->post("stock"));
		$reserve = intval($this->input->post("reserve"));
		$subjectid = intval($this->input->post("subjectid"));

		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$this->load->library("upload");

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
			$image= "default_book.png";
		}

		// Add book
		$this->library_model->add_book(array(
			"title" => $title,
			"description" => $desc,
			"author" => $author,
			"image" => $image,
			"stock" => $stock,
			"reserve" => $reserve,
			"subjectid" => $subjectid
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_102"));
		redirect(site_url("library"));
	}

	public function edit_book($id) 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$book = $this->library_model->get_book($id);
		if($book->num_rows() == 0) {
			$this->template->erorr(lang("error_101"));
		}
		$book = $book->row();

		$subjects = $this->subjects_model->get_all_subjects();
		$this->template->loadContent("library/edit_book.php", array(
			"subjects" => $subjects,
			"book" => $book
			)
		);
	}

	public function edit_book_pro($id) 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$book = $this->library_model->get_book($id);
		if($book->num_rows() == 0) {
			$this->template->erorr(lang("error_101"));
		}
		$book = $book->row();

		$title = $this->common->nohtml($this->input->post("title"));
		$desc = $this->lib_filter->go($this->input->post("description"));
		$author = $this->common->nohtml($this->input->post("author"));
		$stock = intval($this->input->post("stock"));
		$reserve = intval($this->input->post("reserve"));
		$subjectid = intval($this->input->post("subjectid"));

		if(empty($title)) {
			$this->template->error(lang("error_85"));
		}

		$this->load->library("upload");

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
			$image= $book->image;
		}

		// Add book
		$this->library_model->update_book($id, array(
			"title" => $title,
			"description" => $desc,
			"author" => $author,
			"image" => $image,
			"stock" => $stock,
			"reserve" => $reserve,
			"subjectid" => $subjectid
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_103"));
		redirect(site_url("library"));
	}

	public function delete_book($id, $hash) 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$book = $this->library_model->get_book($id);
		if($book->num_rows() == 0) {
			$this->template->erorr(lang("error_101"));
		}

		$this->library_model->delete_book($id);
		$this->session->set_flashdata("globalmsg", lang("success_104"));
		redirect(site_url("library"));
	}

	public function view($id) 
	{
		$id = intval($id);
		$book = $this->library_model->get_book($id);
		if($book->num_rows() == 0) {
			$this->template->erorr(lang("error_101"));
		}
		$book = $book->row();

		$this->template->loadContent("library/view.php", array(
			"book" => $book
			)
		);
	}

	public function reserve($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$book = $this->library_model->get_book($id);
		if($book->num_rows() == 0) {
			$this->template->erorr(lang("error_101"));
		}
		$book = $book->row();

		// Check user can reserve book
		$user_reserves = $this->library_model->get_user_reserve_books($this->user->info->ID);
		if($user_reserves->num_rows() >= $this->settings->info->reserve_books) {
			$this->template->error(lang("error_144"));
		}

		if(!$book->reserve) {
			$this->template->error(lang("error_145"));
		}

		if($book->stock <= 0) {
			$this->template->error(lang("error_146"));
		}

		$this->library_model->add_book_reservation(array(
			"userid" => $this->user->info->ID,
			"bookid" => $id,
			"timestamp" => time()
			)
		);

		$this->library_model->update_book($id, array(
			"stock" => $book->stock - 1
			)
		);

		// Update book stock

		$this->session->set_flashdata("globalmsg", lang("success_105"));
		redirect(site_url("library/view/" . $id));
	}

	public function reservations() 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
				$page = "index";
		} else {
			$page = "all";
		}

		$this->template->loadData("activeLink", 
			array("library" => array("reservations" => 1)));

		$this->template->loadContent("library/reservations.php", array(
			"page" => $page
			)
		);
	}

	public function reservation_page($page) 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("library_reservations.timestamp", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"users.username" => 0
				 ), 
				 1 => array(
				 	"library_books.title" => 0
				 ),
				 2 => array(
				 	"library_books.author" => 0
				 ),
				 3 => array(
				 	"library_reservations.timestamp" => 0
				 ),
			)
		);

		if($page == "index") {

			$this->datatables->set_total_rows(
				$this->library_model->get_total_user_reservations($this->user->info->ID)
			);
			$books = $this->library_model->get_user_reservations($this->user->info->ID, $this->datatables);
		} elseif($page == "all") {
			if(!$this->common->has_permissions(array("admin", "library_manager", 
					), $this->user)) {
					$this->template->error(lang("error_2"));
			}
			$this->datatables->set_total_rows(
				$this->library_model->get_total_user_reservations(0)
			);
			$books = $this->library_model->get_user_reservations(0, $this->datatables);	
		}
		

		foreach($books->result() as $r) {

			$options = '<a href="'.site_url("library/view/" . $r->bookid) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "library_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("library/res_to_checkout/" . $r->ID).'" class="btn btn-warning btn-xs">'.lang("ctn_835").'</a>';
			}
			if($r->userid == $this->user->info->ID) {
				$options .= ' <a href="'.site_url("library/delete_reservation/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}  
			
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				$r->title,
				$r->author,
				date($this->settings->info->date_format, $r->timestamp),
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function res_to_checkout($id) 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$id = intval($id);
		$reservation = $this->library_model->get_reservation($id);
		if($reservation->num_rows() == 0) {
			$this->template->error(lang("error_147"));
		}
		$reservation = $reservation->row();

		$this->library_model->delete_reservation($id);

		$this->library_model->add_checkout(array(
			"userid" => $reservation->userid,
			"bookid" => $reservation->bookid,
			"timestamp" => time()
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_106"));
		redirect(site_url("library/reservations"));

	}

	public function delete_reservation($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$reservation = $this->library_model->get_reservation($id);
		if($reservation->num_rows() == 0) {
			$this->template->erorr(lang("error_147"));
		}
		$reservation = $reservation->row();

		if($reservation->userid != $this->user->info->ID) {
			// Check permission
			if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
				$this->template->error(lang("error_2"));
			}
		}

		$this->library_model->delete_reservation($id);

		$this->library_model->update_book($reservation->bookid, array(
			"stock" => $reservation->stock + 1
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_107"));
		redirect(site_url("library/reservations"));
	}

	public function checkout_book() 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		$title = $this->common->nohtml($this->input->post("title"));
		$username = $this->common->nohtml($this->input->post("username"));

		// Check
		$book = $this->library_model->get_book_by_name($title);
		if($book->num_rows() == 0) {
			$this->template->error(lang("error_117"));
		}
		$book = $book->row();
		if($book->stock <=0) {
			$this->template->error(lang("error_146"));
		}

		// Get user
		$user = $this->user_model->get_user_by_username($username);
		if($user->num_rows() == 0) {
			$this->template->error(lang("error_71"));
		}
		$user = $user->row();

		$this->library_model->add_checkout(array(
			"userid" => $user->ID,
			"bookid" => $book->ID,
			"timestamp" => time()
			)
		);

		$this->library_model->update_book($book->ID, array(
			"stock" => $book->stock - 1
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_108"));
		redirect(site_url("library/view/" . $book->ID));
	}

	public function get_books() 
	{
		$query = $this->common->nohtml($this->input->get("query"));

		if(!empty($query)) {
			$usernames = $this->library_model->get_books_by_name($query);
			if($usernames->num_rows() == 0) {
				echo json_encode(array());
			} else {
				$array = array();
				foreach($usernames->result() as $r) {
					$array[] = $r->title;
				}
				echo json_encode($array);
				exit();
			}
		} else {
			echo json_encode(array());
			exit();
		}
	}

	public function checkedout() 
	{
		if(!$this->common->has_permissions(array("admin", "library_manager", 
				), $this->user)) {
				$page = "index";
		} else {
			$page = "all";
		}

		$this->template->loadData("activeLink", 
			array("library" => array("checkedout" => 1)));

		$this->template->loadContent("library/checkouts.php", array(
			"page" => $page
			)
		);
	}

	public function checkedout_page($page) 
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("library_checkouts.timestamp", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"users.username" => 0
				 ), 
				 1 => array(
				 	"library_books.title" => 0
				 ),
				 2 => array(
				 	"library_books.author" => 0
				 ),
				 3 => array(
				 	"library_checkouts.timestamp" => 0
				 ),
			)
		);

		if($page == "index") {

			$this->datatables->set_total_rows(
				$this->library_model->get_total_user_checkouts($this->user->info->ID)
			);
			$books = $this->library_model->get_user_checkouts($this->user->info->ID, $this->datatables);
		} elseif($page == "all") {
			if(!$this->common->has_permissions(array("admin", "library_manager", 
					), $this->user)) {
					$this->template->error(lang("error_2"));
			}
			$this->datatables->set_total_rows(
				$this->library_model->get_total_user_checkouts(0)
			);
			$books = $this->library_model->get_user_checkouts(0, $this->datatables);	
		}
		

		foreach($books->result() as $r) {

			$options = '<a href="'.site_url("library/view/" . $r->bookid) .'" class="btn btn-primary btn-xs">'.lang("ctn_552").'</a>';
			if($this->common->has_permissions(array("admin", "library_manager"), $this->user)) {
				$options .= ' <a href="'.site_url("library/delete_checkout/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			
			$this->datatables->data[] = array(
				$this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
				$r->title,
				$r->author,
				date($this->settings->info->date_format, $r->timestamp),
				$options
			);
		}
		echo json_encode($this->datatables->process());
	}

	public function delete_checkout($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$checkout = $this->library_model->get_checkout($id);
		if($checkout->num_rows() == 0) {
			$this->template->erorr(lang("error_148"));
		}
		$checkout = $checkout->row();

		if(!$this->common->has_permissions(array("admin", "library_manager", 
			), $this->user)) {
			$this->template->error(lang("error_2"));
		}
		
		$this->library_model->delete_checkout($id);

		$this->library_model->update_book($checkout->bookid, array(
			"stock" => $checkout->stock + 1
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_109"));
		redirect(site_url("library/checkedout"));
	}



}

?>