<?php

class Hostel_Model extends CI_Model 
{

	public function get_hostel_types() 
	{
		return $this->db->get("hostel_types");
	}

	public function get_hostel_type($id) 
	{
		return $this->db->where("ID", $id)->get("hostel_types");
	}

	public function add_hostel($data) 
	{
		$this->db->insert("hostels", $data);
	}

	public function get_hostel($id) 
	{
		return $this->db->where("ID", $id)->get("hostels");
	}

	public function update_hostel($id, $data) 
	{
		$this->db->where("ID", $id)->update("hostels", $data);
	}

	public function delete_hostel($id) 
	{
		$this->db->where("ID", $id)->delete("hostels");
	}

	public function get_hostels_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("hostels");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_hostels($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"hostels.name",
			"hostel_types.name",
			"hostels.address",
			"hostels.description"
			)
		);

		return $this->db
			->select("hostels.ID, hostels.name, hostels.description,
				hostels.address, hostels.capacity,
				hostel_types.name as type_name")
			->join("hostel_types", "hostel_types.ID = hostels.typeid")
			->limit($datatable->length, $datatable->start)
			->get("hostels");
	}

	public function add_hostel_type($data) 
	{
		$this->db->insert("hostel_types", $data);
	}

	public function delete_hostel_type($id) 
	{
		$this->db->where("ID", $id)->delete("hostel_types");
	}

	public function get_hostel_room_types() 
	{
		return $this->db->get("hostel_room_types");
	}

	public function get_all_hostels() 
	{
		return $this->db->get("hostels");
	}

	public function get_hostel_rooms_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("hostel_rooms");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_hostel_rooms($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"hostels.name",
			"hostel_rooms.name",
			"hostel_types.name",
			"hostel_rooms.description"
			)
		);

		return $this->db
			->select("hostels.name as hostel_name,
				hostel_rooms.ID, hostel_rooms.name, hostel_rooms.cost,
				hostel_rooms.capacity, hostel_rooms.description,
				hostel_types.name as type_name")
			->join("hostels", "hostels.ID = hostel_rooms.hostelid")
			->join("hostel_types", "hostel_types.ID = hostels.typeid")
			->limit($datatable->length, $datatable->start)
			->get("hostel_rooms");
	}

	public function get_hostel_room_type($id) 
	{
		return $this->db->where("ID", $id)->get("hostel_room_types");
	}

	public function add_hostel_room($data) 
	{
		$this->db->insert("hostel_rooms", $data);
	}

	public function update_hostel_room($id, $data) 
	{
		$this->db->where("ID", $id)->update("hostel_rooms", $data);
	}

	public function delete_hostel_room($id) 
	{
		$this->db->where("ID", $id)->delete("hostel_rooms");
	}

	public function get_hostel_room($id) 
	{
		return $this->db->where("ID", $id)->get("hostel_rooms");
	}

	public function add_hostel_room_type($data) 
	{
		$this->db->insert("hostel_room_types", $data);
	}

	public function delete_hostel_room_type($id) 
	{
		$this->db->where("ID", $id)->delete("hostel_room_types");
	}

	public function get_hostel_rooms_hostel($hostelid) 
	{
		return $this->db->where("hostelid", $hostelid)->get("hostel_rooms");
	}

	public function add_booking($data) 
	{
		$this->db->insert("hostel_bookings", $data);
	}

	public function get_booking($id) 
	{
		return $this->db
			->where("hostel_bookings.ID", $id)
			->select("hostel_bookings.ID, hostel_bookings.roomid, hostel_bookings.userid,
				hostel_bookings.guest_name, hostel_bookings.guest_email, hostel_bookings.checkin,
				hostel_bookings.checkout, hostel_bookings.notes,
				hostel_rooms.hostelid, hostel_rooms.name as room_name,
				users.username")
			->join("users", "users.ID = hostel_bookings.userid", "left outer")
			->join("hostel_rooms", "hostel_rooms.ID = hostel_bookings.roomid")
			->get("hostel_bookings");
	}

	public function delete_booking($id) 
	{
		$this->db->where("ID", $id)->delete("hostel_bookings");
	}

	public function update_booking($id, $data) 
	{
		$this->db->where("ID", $id)->update("hostel_bookings", $data);
	}

	public function get_bookings_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("hostel_bookings");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_bookings($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"hostels.name",
			"hostel_rooms.name",
			"hostel_types.name",
			"hostel_rooms.description"
			)
		);

		return $this->db
			->select("hostels.name as hostel_name,
				hostel_rooms.ID as roomid, hostel_rooms.name as room_name,
				users.username, users.ID as userid, users.online_timestamp, users.avatar,
				users.first_name, users.last_name,
				hostel_bookings.checkin, hostel_bookings.checkout, hostel_bookings.ID,
				hostel_bookings.notes, hostel_bookings.guest_name, hostel_bookings.guest_email")
			->join("hostel_rooms", "hostel_rooms.ID = hostel_bookings.roomid")
			->join("hostels", "hostels.ID = hostel_rooms.hostelid")
			->join("users", "users.ID = hostel_bookings.userid", "left outer")
			->limit($datatable->length, $datatable->start)
			->get("hostel_bookings");
	}

}

?>