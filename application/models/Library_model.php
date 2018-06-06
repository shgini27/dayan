<?php

class Library_Model extends CI_Model 
{

	public function add_book($data) 
	{
		$this->db->insert("library_books", $data);
	}

	public function get_book($id) 
	{
		return $this->db->where("ID", $id)->get("library_books");
	}

	public function update_book($id, $data) 
	{
		$this->db->where("ID", $id)->update("library_books", $data);
	}

	public function delete_book($id) 
	{
		$this->db->where("ID", $id)->delete("library_books");
	}

	public function get_total_books() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("library_books");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_books($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"library_books.title",
			"library_books.author"
			)
		);

		return $this->db
			->select("library_books.ID, library_books.title, library_books.image,
				library_books.author, library_books.description, 
				library_books.stock,
				subjects.name")
			->join("subjects", "subjects.ID = library_books.subjectid", "left outer")
			->limit($datatable->length, $datatable->start)
			->get("library_books");
	}

	public function get_user_reserve_books($userid) 
	{
		return $this->db->where("userid", $userid)->get("library_reservations");
	}

	public function add_book_reservation($data) 
	{
		$this->db->insert("library_reservations", $data);
	}

	public function get_total_user_reservations($userid) 
	{
		if($userid > 0) {
			$this->db->where("library_reservations.userid", $userid);
		}

		$s = $this->db
			->select("COUNT(*) as num")
			->get("library_reservations");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_user_reservations($userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"library_books.title",
			"library_books.author"
			)
		);

		if($userid > 0) {
			$this->db->where("library_reservations.userid", $userid);
		}

		return $this->db
			->select("library_books.ID as bookid, library_books.title, library_books.image,
				library_books.author, library_books.description, 
				library_books.stock,
				library_reservations.ID, library_reservations.timestamp,
				library_reservations.userid,
				users.username, users.first_name, users.last_name, users.avatar,
				users.online_timestamp")
			->join("library_books", "library_books.ID = library_reservations.bookid")
			->join("users", "users.ID = library_reservations.userid")
			->limit($datatable->length, $datatable->start)
			->get("library_reservations");
	}

	public function get_reservation($id) 
	{
		return $this->db
			->where("library_reservations.ID", $id)
			->select("library_books.ID as bookid, library_books.title, library_books.image,
				library_books.author, library_books.description, 
				library_books.stock,
				library_reservations.ID, library_reservations.timestamp,
				library_reservations.userid,
				users.username, users.first_name, users.last_name, users.avatar,
				users.online_timestamp")
			->join("library_books", "library_books.ID = library_reservations.bookid")
			->join("users", "users.ID = library_reservations.userid")
			->get("library_reservations");
	}

	public function delete_reservation($id) 
	{
		$this->db->where("ID", $id)->delete("library_reservations");
	}

	public function get_book_by_name($title) 
	{
		return $this->db->where("title", $title)->get("library_books");
	}

	public function add_checkout($data) 
	{
		$this->db->insert("library_checkouts", $data);
	}

	public function get_books_by_name($title) 
	{
		return $this->db->like("title", $title)->get("library_books");
	}

	public function get_total_user_checkouts($userid) 
	{
		if($userid > 0) {
			$this->db->where("library_checkouts.userid", $userid);
		}

		$s = $this->db
			->select("COUNT(*) as num")
			->get("library_checkouts");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_user_checkouts($userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"library_books.title",
			"library_books.author"
			)
		);

		if($userid > 0) {
			$this->db->where("library_checkouts.userid", $userid);
		}

		return $this->db
			->select("library_books.ID as bookid, library_books.title, library_books.image,
				library_books.author, library_books.description, 
				library_books.stock,
				library_checkouts.ID, library_checkouts.timestamp,
				users.username, users.first_name, users.last_name, users.avatar,
				users.online_timestamp")
			->join("library_books", "library_books.ID = library_checkouts.bookid")
			->join("users", "users.ID = library_checkouts.userid")
			->limit($datatable->length, $datatable->start)
			->get("library_checkouts");
	}

	public function get_checkout($id) 
	{
		return $this->db
			->where("library_checkouts.ID", $id)
			->select("library_books.ID as bookid, library_books.title, library_books.image,
				library_books.author, library_books.description, 
				library_books.stock,
				library_checkouts.ID, library_checkouts.timestamp,
				users.username, users.first_name, users.last_name, users.avatar,
				users.online_timestamp")
			->join("library_books", "library_books.ID = library_checkouts.bookid")
			->join("users", "users.ID = library_checkouts.userid")
			->get("library_checkouts");
	}

	public function delete_checkout($id) 
	{
		$this->db->where("ID", $id)->delete("library_checkouts");
	}

}

?>