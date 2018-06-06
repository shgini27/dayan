<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hostel extends CI_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->model("user_model");
		$this->load->model("hostel_model");
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
			array("hostel" => array("general" => 1)));

		if(!$this->common->has_permissions(array("admin", "hostel_manager"), 
			$this->user)) {
			$this->template->error(lang("error_2"));
		}

		if(!$this->settings->info->hostel_section) {
			$this->template->error(lang("error_84"));
		}
	}

	public function index() 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("general" => 1)));

		$types = $this->hostel_model->get_hostel_types();

		$this->template->loadContent("hostel/index.php", array(
			"types" => $types
			)
		);
	}

	public function hostel_page()
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("hostels.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"hostels.name" => 0
				 ),
				 1 => array(
				 	"hostel_types.name" => 0
				 ),
				 2 => array(
				 	"hostels.address" => 0
				 ),
				 3 => array(
				 	"hostels.capacity" => 0
				 ),
				 4 => array(
				 	"hostels.description" => 0
				 )
			)
		);

		
		$this->datatables->set_total_rows(
			$this->hostel_model
				->get_hostels_total()
		);
		$hostels = $this->hostel_model->get_hostels($this->datatables);
		
		
		

		foreach($hostels->result() as $r) {

			
				$options = ' <a href="'.site_url("hostel/edit_hostel/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("hostel/delete_hostel/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			
			
			$this->datatables->data[] = array(
				$r->name,
				$r->type_name,
				$r->address,
				$r->capacity,
				$r->description,
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add_hostel() 
	{
		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->common->nohtml($this->input->post("description"));
		$address = $this->common->nohtml($this->input->post("address"));
		$type = intval($this->input->post("type"));
		$capacity = intval($this->input->post("capacity"));

		$type = $this->hostel_model->get_hostel_type($type);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_173"));
		}
		$type = $type->row();

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->hostel_model->add_hostel(array(
			"name" => $name,
			"description" => $description,
			"address" => $address,
			"capacity" => $capacity,
			"typeid" => $type->ID
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_136"));
		redirect(site_url("hostel"));
	}

	public function delete_hostel($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$hostel = $this->hostel_model->get_hostel($id);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$this->hostel_model->delete_hostel($id);

		// Add delete rooms/bookings too
		$this->session->set_flashdata("globalmsg", lang("success_137"));
		redirect(site_url("hostel"));
	}

	public function edit_hostel($id) 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("general" => 1)));

		$types = $this->hostel_model->get_hostel_types();

		$id = intval($id);
		$hostel = $this->hostel_model->get_hostel($id);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		$this->template->loadContent("hostel/edit_hostel.php", array(
			"types" => $types,
			"hostel" => $hostel
			)
		);
	}

	public function edit_hostel_pro($id) 
	{
		$id = intval($id);
		$hostel = $this->hostel_model->get_hostel($id);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->common->nohtml($this->input->post("description"));
		$address = $this->common->nohtml($this->input->post("address"));
		$type = intval($this->input->post("type"));
		$capacity = intval($this->input->post("capacity"));

		$type = $this->hostel_model->get_hostel_type($type);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_173"));
		}
		$type = $type->row();

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->hostel_model->update_hostel($id, array(
			"name" => $name,
			"description" => $description,
			"address" => $address,
			"capacity" => $capacity,
			"typeid" => $type->ID
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_138"));
		redirect(site_url("hostel"));
	}

	public function hostel_types() 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("general" => 1)));

		$types = $this->hostel_model->get_hostel_types();

		$this->template->loadContent("hostel/hostel_types.php", array(
			"types" => $types
			)
		);
	}

	public function add_hostel_type() 
	{
		$name = $this->common->nohtml($this->input->post("name"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->hostel_model->add_hostel_type(array(
			"name" => $name
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_139"));
		redirect(site_url("hostel/hostel_types"));
	}

	public function delete_hostel_type($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$type = $this->hostel_model->get_hostel_type($id);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_173"));
		}
		$this->hostel_model->delete_hostel_type($id);
		$this->session->set_flashdata("globalmsg", lang("success_140"));
		redirect(site_url("hostel/hostel_types"));
	}

	public function rooms() 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("rooms" => 1)));

		$types = $this->hostel_model->get_hostel_room_types();
		$hostels = $this->hostel_model->get_all_hostels();

		$this->template->loadContent("hostel/hostel_rooms.php", array(
			"types" => $types,
			"hostels" => $hostels
			)
		);
	}

	public function hostel_room_page()
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("hostels.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"hostels.name" => 0
				 ),
				 1 => array(
				 	"hostel_rooms.name" => 0
				 ),
				 2 => array(
				 	"hostel_room_types.name" => 0
				 ),
				 3 => array(
				 	"hostel_rooms.cost" => 0
				 ),
				 4 => array(
				 	"hostels_rooms.capacity" => 0
				 ),
				 5 => array(
				 	"hostels_rooms.description" => 0
				 )
			)
		);

		
		$this->datatables->set_total_rows(
			$this->hostel_model
				->get_hostel_rooms_total()
		);
		$hostels = $this->hostel_model->get_hostel_rooms($this->datatables);
		
		
		

		foreach($hostels->result() as $r) {

			
				$options = ' <a href="'.site_url("hostel/edit_hostel_room/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("hostel/delete_hostel_room/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			
			
			$this->datatables->data[] = array(
				$r->hostel_name,
				$r->name,
				$r->type_name,
				$r->cost,
				$r->capacity,
				$r->description,
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function add_hostel_room() 
	{
		$hostelid = intval($this->input->post("hostelid"));
		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->common->nohtml($this->input->post("description"));
		$type = intval($this->input->post("type"));
		$capacity = intval($this->input->post("capacity"));
		$cost = floatval($this->input->post("cost"));

		$hostel = $this->hostel_model->get_hostel($hostelid);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		// Check
		$type = $this->hostel_model->get_hostel_room_type($type);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_175"));
		}
		$type = $type->row();

		$this->hostel_model->add_hostel_room(array(
			"name" => $name,
			"hostelid" => $hostelid,
			"typeid" => $type->ID,
			"capacity" => $capacity,
			"description" => $description,
			"cost" => $cost
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_141"));
		redirect(site_url("hostel/rooms"));
	}

	public function delete_hostel_room($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$room = $this->hostel_model->get_hostel_room($id);
		if($room->num_rows() == 0) {
			$this->template->error(lang("error_176"));
		}
		$room = $room->row();

		$this->hostel_model->delete_hostel_room($id);
		$this->session->set_flashdata("globalmsg", lang("success_142"));
		redirect(site_url("hostel/rooms"));
	}

	public function edit_hostel_room($id) 
	{
		$id = intval($id);
		$room = $this->hostel_model->get_hostel_room($id);
		if($room->num_rows() == 0) {
			$this->template->error(lang("error_176"));
		}
		$room = $room->row();

		$this->template->loadData("activeLink", 
			array("hostel" => array("rooms" => 1)));

		$types = $this->hostel_model->get_hostel_room_types();
		$hostels = $this->hostel_model->get_all_hostels();

		$this->template->loadContent("hostel/edit_hostel_room.php", array(
			"types" => $types,
			"hostels" => $hostels,
			"room" => $room
			)
		);
	}

	public function edit_hostel_room_pro($id) 
	{
		$id = intval($id);
		$room = $this->hostel_model->get_hostel_room($id);
		if($room->num_rows() == 0) {
			$this->template->error(lang("error_176"));
		}
		$room = $room->row();

		$hostelid = intval($this->input->post("hostelid"));
		$name = $this->common->nohtml($this->input->post("name"));
		$description = $this->common->nohtml($this->input->post("description"));
		$type = intval($this->input->post("type"));
		$capacity = intval($this->input->post("capacity"));
		$cost = floatval($this->input->post("cost"));

		$hostel = $this->hostel_model->get_hostel($hostelid);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		// Check
		$type = $this->hostel_model->get_hostel_room_type($type);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_175"));
		}
		$type = $type->row();

		$this->hostel_model->update_hostel_room($id, array(
			"name" => $name,
			"hostelid" => $hostelid,
			"typeid" => $type->ID,
			"capacity" => $capacity,
			"description" => $description,
			"cost" => $cost
			)
		);
		$this->session->set_flashdata("globalmsg", lang("success_143"));
		redirect(site_url("hostel/rooms"));
	}

	public function hostel_room_types() 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("rooms" => 1)));

		$types = $this->hostel_model->get_hostel_room_types();

		$this->template->loadContent("hostel/hostel_room_types.php", array(
			"types" => $types
			)
		);
	}

	public function add_hostel_room_type() 
	{
		$name = $this->common->nohtml($this->input->post("name"));

		if(empty($name)) {
			$this->template->error(lang("error_81"));
		}

		$this->hostel_model->add_hostel_room_type(array(
			"name" => $name
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_144"));
		redirect(site_url("hostel/hostel_room_types"));
	}

	public function delete_hostel_room_type($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}
		$id = intval($id);
		$type = $this->hostel_model->get_hostel_room_type($id);
		if($type->num_rows() == 0) {
			$this->template->error(lang("error_173"));
		}
		$this->hostel_model->delete_hostel_room_type($id);
		$this->session->set_flashdata("globalmsg", lang("success_145"));
		redirect(site_url("hostel/hostel_room_types"));
	}

	public function bookings() 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("bookings" => 1)));

		$hostels = $this->hostel_model->get_all_hostels();

		$this->template->loadContent("hostel/bookings.php", array(
			"hostels" => $hostels
			)
		);
	}

	public function booking_page()
	{
		$this->load->library("datatables");

		$this->datatables->set_default_order("hostel_bookings.ID", "desc");

		// Set page ordering options that can be used
		$this->datatables->ordering(
			array(
				 0 => array(
				 	"hostels.name" => 0
				 ),
				 1 => array(
				 	"hostel_rooms.name" => 0
				 ),
				 2 => array(
				 	"users.username" => 0
				 ),
				 3 => array(
				 	"hostel_bookings.checkin" => 0
				 ),
				 4 => array(
				 	"hostel_bookings.checkout" => 0
				 ),
				 5 => array(
				 	"hostel_bookings.notes" => 0
				 )
			)
		);

		
		$this->datatables->set_total_rows(
			$this->hostel_model
				->get_bookings_total()
		);
		$bookings = $this->hostel_model->get_bookings($this->datatables);
		
		
		

		foreach($bookings->result() as $r) {

			if($r->userid > 0) {
				$user = $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name));
			} else {
				$user = $r->guest_name . " (" . $r->guest_email . ")";
			}

			
				$options = ' <a href="'.site_url("hostel/edit_booking/" . $r->ID).'" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_55").'"><span class="glyphicon glyphicon-cog"></span></a> <a href="'.site_url("hostel/delete_booking/" . $r->ID . "/" . $this->security->get_csrf_hash()).'" class="btn btn-danger btn-xs" onclick="return confirm(\''.lang("ctn_317").'\')" data-toggle="tooltip" data-placement="bottom" title="'.lang("ctn_57").'"><span class="glyphicon glyphicon-trash"></span></a>';
			
			
			$this->datatables->data[] = array(
				$r->hostel_name,
				$r->room_name,
				$user,
				date($this->settings->info->date_format, $r->checkin),
				date($this->settings->info->date_format, $r->checkout),
				$r->notes,
				$options
			);
		}

		echo json_encode($this->datatables->process());
	}

	public function ajax_get_hostel_rooms($hostelid) 
	{
		$hostelid = intval($hostelid);
		$rooms = $this->hostel_model->get_hostel_rooms_hostel($hostelid);
		$this->template->loadAjax("hostel/ajax_hostel_rooms.php", array(
			"rooms" => $rooms
			),1
		);
	}

	public function add_booking() 
	{
		$hostelid = intval($this->input->post("hostelid"));
		$roomid = intval($this->input->post("roomid"));
		$username = $this->common->nohtml($this->input->post("username"));
		$guest_name = $this->common->nohtml($this->input->post("guest_name"));
		$guest_email = $this->common->nohtml($this->input->post("guest_email"));
		$checkin = $this->common->nohtml($this->input->post("checkin"));
		$checkout = $this->common->nohtml($this->input->post("checkout"));
		$notes = $this->common->nohtml($this->input->post("notes"));

		$hostel = $this->hostel_model->get_hostel($hostelid);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		$room = $this->hostel_model->get_hostel_room($roomid);
		if($room->num_rows() == 0) {
			$this->template->error(lang("error_176"));
		}
		$room = $room->row();

		$userid = 0;
		if(!empty($username)) {
			$user = $this->user_model->get_user_by_username($username);
			if($user->num_rows() == 0) {
				$this->template->error(lang("error_177"));
			}
			$user = $user->row();
			$userid = $user->ID;
		} else {
			if(empty($guest_name)) {
				$this->template->error(lang("error_178"));
			}
		}

		if(!empty($checkin)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $checkin);
			$checkin_timestamp = $dd->getTimestamp();
		} else {
			$this->template->error(lang("error_179"));
		}

		if(!empty($checkout)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $checkout);
			$checkout_timestamp = $dd->getTimestamp();
		} else {
			$this->template->error(lang("error_180"));
		}

		$this->hostel_model->add_booking(array(
			"roomid" => $roomid,
			"userid" => $userid,
			"guest_name" => $guest_name,
			"guest_email" => $guest_email,
			"checkin" => $checkin_timestamp,
			"checkout" => $checkout_timestamp,
			"notes" => $notes
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_146"));
		redirect(site_url("hostel/bookings"));


	}

	public function delete_booking($id, $hash) 
	{
		if($hash != $this->security->get_csrf_hash()) {
			$this->template->error(lang("error_6"));
		}

		$id = intval($id);
		$booking = $this->hostel_model->get_booking($id);
		if($booking->num_rows() == 0) {
			$this->template->error(lang("error_181"));
		}
		$booking = $booking->row();

		$this->hostel_model->delete_booking($id);
		$this->session->set_flashdata("globalmsg", lang("success_147"));
		redirect(site_url("hostel/bookings"));
	}

	public function edit_booking($id) 
	{
		$this->template->loadData("activeLink", 
			array("hostel" => array("bookings" => 1)));

		$hostels = $this->hostel_model->get_all_hostels();

		$id = intval($id);
		$booking = $this->hostel_model->get_booking($id);
		if($booking->num_rows() == 0) {
			$this->template->error(lang("error_181"));
		}
		$booking = $booking->row();


		$this->template->loadContent("hostel/edit_booking.php", array(
			"hostels" => $hostels,
			"booking" => $booking
			)
		);
	}

	public function edit_booking_pro($id) 
	{
		$id = intval($id);
		$booking = $this->hostel_model->get_booking($id);
		if($booking->num_rows() == 0) {
			$this->template->error(lang("error_181"));
		}
		$booking = $booking->row();

		$hostelid = intval($this->input->post("hostelid"));
		$roomid = intval($this->input->post("roomid"));
		$username = $this->common->nohtml($this->input->post("username"));
		$guest_name = $this->common->nohtml($this->input->post("guest_name"));
		$guest_email = $this->common->nohtml($this->input->post("guest_email"));
		$checkin = $this->common->nohtml($this->input->post("checkin"));
		$checkout = $this->common->nohtml($this->input->post("checkout"));
		$notes = $this->common->nohtml($this->input->post("notes"));

		$hostel = $this->hostel_model->get_hostel($hostelid);
		if($hostel->num_rows() == 0) {
			$this->template->error(lang("error_174"));
		}
		$hostel = $hostel->row();

		$room = $this->hostel_model->get_hostel_room($roomid);
		if($room->num_rows() == 0) {
			$this->template->error(lang("error_176"));
		}
		$room = $room->row();

		$userid = 0;
		if(!empty($username)) {
			$user = $this->user_model->get_user_by_username($username);
			if($user->num_rows() == 0) {
				$this->template->error(lang("error_177"));
			}
			$user = $user->row();
			$userid = $user->ID;
		} else {
			if(empty($guest_name)) {
				$this->template->error(lang("error_178"));
			}
		}

		if(!empty($checkin)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $checkin);
			$checkin_timestamp = $dd->getTimestamp();
		} else {
			$this->template->error(lang("error_179"));
		}

		if(!empty($checkout)) {
			$dd = DateTime::createFromFormat($this->settings->info->date_picker_format, $checkout);
			$checkout_timestamp = $dd->getTimestamp();
		} else {
			$this->template->error(lang("error_180"));
		}

		$this->hostel_model->update_booking($id, array(
			"roomid" => $roomid,
			"userid" => $userid,
			"guest_name" => $guest_name,
			"guest_email" => $guest_email,
			"checkin" => $checkin_timestamp,
			"checkout" => $checkout_timestamp,
			"notes" => $notes
			)
		);

		$this->session->set_flashdata("globalmsg", lang("success_"));
		redirect(site_url("hostel/bookings"));
	}



}

?>