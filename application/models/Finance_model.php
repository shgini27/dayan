<?php

class Finance_Model extends CI_Model 
{

	public function get_categories() 
	{
		return $this->db->get("finance_categories");
	}

	public function get_category($id) 
	{
		return $this->db->where("ID", $id)->get("finance_categories");
	}

	public function delete_category($id) 
	{
		$this->db->where("ID", $id)->delete("finance_categories");
	}

	public function update_category($id, $data) 
	{
		$this->db->where("ID", $id)->update("finance_categories", $data);
	}

	public function add_category($data) 
	{
		$this->db->insert("finance_categories", $data);
	}

	public function add_finance($data) 
	{
		$this->db->insert("finance", $data);
	}

	public function get_all_finances($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"finance.title",
			"finance_categories.name",
			"users.username"
			)
		);

		return $this->db->select("finance.ID, finance.title, finance.amount, 
			finance.notes, finance.userid, finance.timestamp,
			finance.categoryid,
			users.username, users.avatar, users.online_timestamp, users.first_name,
			users.last_name,
			finance_categories.name as catname")
			->join("users", "users.ID = finance.userid")
			->join("finance_categories", "finance_categories.ID = finance.categoryid")
			->order_by("finance.ID", "DESC")
			->limit($datatable->length, $datatable->start)
			->get("finance");
	}

	public function get_all_finances_total() 
	{
		$s = $this->db->select("COUNT(*) as num")
			->join("users", "users.ID = finance.userid")
			->join("finance_categories", "finance_categories.ID = finance.categoryid")
			->order_by("finance.ID", "DESC")
			->get("finance");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_finance($id) 
	{
		return $this->db->where("ID", $id)->get("finance");
	}

	public function delete_finance($id) 
	{
		$this->db->where("ID", $id)->delete("finance");
	}

	public function update_finance($id, $data) 
	{
		$this->db->where("ID", $id)->update("finance", $data);
	}

	public function get_sum_for_month($month, $year, $type) 
	{
		if($type) {
			$this->db->where("finance.amount >", 0);
		} else {
			$this->db->where("finance.amount <", 0);
		}
		$s = $this->db
			->where("finance.month", $month)
			->where("finance.year", $year)
			->select("SUM(finance.amount) as total")
			->join("users", "users.ID = finance.userid")
			->join("finance_categories", "finance_categories.ID = finance.categoryid")
			->get("finance");
		$r = $s->row();
		if(isset($r->total)) return $r->total;
		return 0;
	}

}

?>