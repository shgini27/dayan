<?php

class Classes_Model extends CI_Model 
{

	public function get_assignments($id) 
	{
		return $this->db->where("classid", $id)
			->order_by("class_assignments.due_date")
			->limit(5)
			->get("class_assignments");
	}

	public function add_category($data) 
	{
		$this->db->insert("class_categories", $data);
	}

	public function get_category($id) 
	{
		return $this->db->where("ID", $id)->get("class_categories");
	}

	public function delete_category($id) 
	{
		$this->db->where("ID", $id)->delete("class_categories");
	}

	public function update_category($id, $data) 
	{
		$this->db->where("ID", $id)->update("class_categories", $data);
	}

	public function get_categories() 
	{
		return $this->db->get("class_categories");
	}

	public function get_categories_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("class_categories");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_categories_dt($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_categories.name"
			)
		);

		return $this->db
			->limit($datatable->length, $datatable->start)
			->get("class_categories");

	}
        
        public function get_category_data($category_id){
            //SELECT s.name, cc.start_date, cc.end_date FROM `classes` c left join 
            //`class_categories` cc on(c.categoryid = cc.ID) left join `subjects` s 
            //on(s.ID = c.subjectid) WHERE cc.ID = 2 GROUP by s.name
            return $this->db
			->select("subjects.name, class_categories.start_date, class_categories.end_date")
			->where("class_categories.ID", $category_id)
			->join("class_categories", "classes.categoryid = class_categories.ID")
                        ->join("subjects", "subjects.ID = classes.subjectid")
			->group_by("subjects.name")
			->get("classes");
        }
        
	public function add_class($data) 
	{
		$this->db->insert("classes", $data);
		return $this->db->insert_id();
	}

	public function delete_class($id) 
	{
		$this->db->where("ID", $id)->delete("classes");
	}

	public function get_class($id) 
	{
		return $this->db->where("ID", $id)->get("classes");
	}

	public function update_class($id, $data) 
	{
		$this->db->where("ID", $id)->update("classes", $data);
	}

	public function add_student($data) 
	{
		$this->db->insert("class_students", $data);
	}

	public function get_classes_total() 
	{
		$s = $this->db->select("COUNT(*) as num")->get("classes");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_classes($datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_categories.name",
			"classes.name",
			"subjects.name"
			)
		);

		return $this->db
			->select("classes.ID, classes.name, classes.description,
				classes.subjectid, classes.categoryid, classes.students,
				classes.max_students, classes.allow_signups,
				subjects.name as subject_name,
				class_categories.name as cat_name")
			->join("subjects", "subjects.ID = classes.subjectid")
			->join("class_categories", "class_categories.ID = classes.categoryid")
			->limit($datatable->length, $datatable->start)
			->get("classes");
	}

	public function get_classes_total_your($userid) 
	{
		$s = $this->db
			->select("COUNT(*) as num")
			->where("class_students.userid", $userid)
			->join("classes", "classes.ID = class_students.classid")
			->group_by("class_students.classid")
			->get("class_students");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_classes_your($userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_categories.name"
			)
		);

		return $this->db
			->where("class_students.userid", $userid)
			->select("classes.ID, classes.name, classes.description,
				classes.subjectid, classes.categoryid, classes.students,
				classes.max_students, classes.allow_signups,
				subjects.name as subject_name,
				class_categories.name as cat_name")
			->join("classes", "classes.ID = class_students.classid")
			->join("subjects", "subjects.ID = classes.subjectid")
			->join("class_categories", "class_categories.ID = classes.categoryid")
			->group_by("class_students.classid")
			->limit($datatable->length, $datatable->start)
			->get("class_students");
	}

	public function get_class_students($classid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.first_name",
			"users.last_name"
			)
		);

		return $this->db
			->where("class_students.classid", $classid)
			->where("class_students.teacher_flag", 0)
			->select("class_students.ID, class_students.teacher_flag,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name, users.email")
			->join("users", "users.ID = class_students.userid")
			->limit($datatable->length, $datatable->start)
			->get("class_students");
	}

	public function get_students_from_class($id) 
	{
		return $this->db
			->where("class_students.classid", $id)
			->select("users.ID as userid, users.username, users.email,
				users.email_notification, users.avatar, users.first_name,
				users.last_name, users.online_timestamp,
				class_students.teacher_flag")
			->join("users", "users.ID = class_students.userid")
			->get("class_students");
	}

	public function get_students_from_class_only($id) 
	{
		return $this->db
			->where("class_students.classid", $id)
			->where("class_students.teacher_flag", 0)
			->select("users.ID as userid, users.username, users.email,
				users.email_notification, users.avatar, users.first_name,
				users.last_name, users.online_timestamp,
				class_students.teacher_flag")
			->join("users", "users.ID = class_students.userid")
			->get("class_students");
	}

	public function get_student_count($classid) 
	{
		$s = $this->db->where("classid", $classid)
			->where("teacher_flag", 0)
			->select("COUNT(*) as num")->get("class_students");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_class_teachers_all($classid) 
	{
		return $this->db
			->where("class_students.teacher_flag", 1)
			->where("class_students.classid", $classid)
			->select("class_students.ID, class_students.teacher_flag,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name, users.email")
			->join("users", "users.ID = class_students.userid")
			->get("class_students");
	}

	public function get_class_teachers($classid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"users.first_name",
			"users.last_name"
			)
		);

		return $this->db
			->where("class_students.teacher_flag", 1)
			->where("class_students.classid", $classid)
			->select("class_students.ID, class_students.teacher_flag,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name, users.email")
			->join("users", "users.ID = class_students.userid")
			->limit($datatable->length, $datatable->start)
			->get("class_students");
	}

	public function get_teacher_count($classid) 
	{
		$s = $this->db->where("classid", $classid)
			->where("teacher_flag", 1)
			->select("COUNT(*) as num")->get("class_students");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_class_student($id) 
	{
		return $this->db
			->where("class_students.ID", $id)
			->select("class_students.ID, class_students.classid,
				class_students.userid,
				classes.name,
				users.username, users.first_name, users.last_name")
			->join("classes", "classes.ID = class_students.classid")
			->join("users", "users.ID = class_students.userid")
			->get("class_students");
	}

	public function delete_student($id) 
	{
		$this->db->where("ID", $id)->delete("class_students");
	}

	public function get_class_student_user($userid, $classid) 
	{
		return $this->db
			->where("userid", $userid)
			->where("classid", $classid)
			->get("class_students");
	}

	public function get_announcements($id) 
	{
		return $this->db
			->where("classid", $id)
			->order_by("ID", "DESC")
			->limit(5)
			->get("class_announcements");
	}

	public function add_announcement($data) 
	{
		$this->db->insert("class_announcements", $data);
		return $this->db->insert_id();
	}

	public function get_announcement($id) 
	{
		return $this->db
			->where("class_announcements.ID", $id)
			->select("class_announcements.ID, class_announcements.title,
				class_announcements.body, class_announcements.timestamp,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name,
				classes.name, classes.ID as classid")
			->join("users", "users.ID = class_announcements.userid")
			->join("classes", "classes.ID = class_announcements.classid")
			->get("class_announcements");
	}

	public function delete_announcement($id) 
	{
		$this->db->where("ID", $id)->delete("class_announcements");
	}

	public function update_announcement($id, $data) 
	{
		$this->db->where("ID", $id)->update("class_announcements", $data);
	}

	public function get_total_announcements($id) 
	{
		$s = $this->db->where("classid", $id)->select("COUNT(*) as num")
			->get("class_announcements");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_announcements_dt($classid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_announcements.title",
			"users.username"
			)
		);

		return $this->db
			->where("class_announcements.classid", $classid)
			->select("class_announcements.ID, class_announcements.title,
				class_announcements.body, class_announcements.timestamp,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
			->join("users", "users.ID = class_announcements.userid")
			->limit($datatable->length, $datatable->start)
			->get("class_announcements");
	}

	public function get_user_announcement($userid, $id)
	{
		return $this->db->where("announcementid", $id)
			->where("userid", $userid)->get("class_announcement_users");
	}

	public function add_user_announcement($data) 
	{
		$this->db->insert("class_announcement_users", $data);
	}

	public function get_user_announcements($id) 
	{
		return $this->db
			->where("class_announcement_users.announcementid", $id)
			->select("users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name")
			->join("users", "users.ID = class_announcement_users.userid")
			->get("class_announcement_users");
	}

	public function add_assignment($data) 
	{
		$this->db->insert("class_assignments", $data);
		return $this->db->insert_id();
	}

	public function get_assignment($id) 
	{
		return $this->db
			->where("class_assignments.ID", $id)
			->select("class_assignments.ID, class_assignments.title,
				class_assignments.body, class_assignments.userid,
				class_assignments.timestamp, class_assignments.due_date,
				class_assignments.file_types, class_assignments.reupload,
				class_assignments.deny_upload, class_assignments.type,
				class_assignments.weighting, class_assignments.max_mark,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name,
				classes.ID as classid, classes.name")
			->join("users", "users.ID = class_assignments.userid")
			->join("classes", "classes.ID = class_assignments.classid")
			->get("class_assignments");
	}

	public function update_assignment($id, $data) 
	{
		$this->db->where("ID", $id)->update("class_assignments", $data);
	}

	public function delete_assignment($id) 
	{
		$this->db->where("ID", $id)->delete("class_assignments");
	}

	public function get_total_assignments($id) 
	{
		$s = $this->db->where("classid", $id)
			->select("COUNT(*) as num")->get("class_assignments");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_assignments_dt($classid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_assignments.title",
			"users.username"
			)
		);

		return $this->db
			->where("class_assignments.classid", $classid)
			->select("class_assignments.ID, class_assignments.title,
				class_assignments.body, class_assignments.timestamp,
				class_assignments.due_date, class_assignments.type,
				class_assignments.weighting, class_assignments.max_mark,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
			->join("users", "users.ID = class_assignments.userid")
			->limit($datatable->length, $datatable->start)
			->get("class_assignments");
	}

	public function add_user_assignment($data) 
	{
		$this->db->insert("user_assignments", $data);
	}

	public function get_user_assignment($userid, $id) 
	{
		return $this->db->where("userid", $userid)->where("assignmentid", $id)
			->get("user_assignments");
	}

	public function delete_assignment_by_user($userid, $id) 
	{
		$this->db->where("userid", $userid)->where("assignmentid", $id)
			->delete("user_assignments");
	}

	public function get_total_user_assignments($id) 
	{
		$s = $this->db->where("assignmentid", $id)->select("COUNT(*) as num")
			->get("user_assignments");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_user_assignments_dt($id, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			"user_assignments.IP"
			)
		);

		return $this->db
			->where("user_assignments.assignmentid", $id)
			->select("user_assignments.ID, user_assignments.timestamp,
				user_assignments.IP, user_assignments.file_name,
				user_assignments.mark,
				class_assignments.ID as assignmentid, class_assignments.title,
				class_assignments.body, class_assignments.weighting,
				class_assignments.max_mark,
				class_assignments.due_date,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
			->join("class_assignments", "class_assignments.ID = user_assignments.assignmentid")
			->join("users", "users.ID = user_assignments.userid")
			->limit($datatable->length, $datatable->start)
			->get("user_assignments");
	}

	public function get_user_assignment_id($id) 
	{
            // i changed some value (class_assignments.classid, user_assignments.userid) 
            // added this two to avoid bugs
		return $this->db
			->where("user_assignments.ID", $id)
			->select("user_assignments.ID, class_assignments.classid, 
                                user_assignments.userid, user_assignments.timestamp,
				user_assignments.IP, user_assignments.file_name,
				user_assignments.file_size, user_assignments.file_type,
				user_assignments.notes, user_assignments.file_extension,
				user_assignments.mark,
				class_assignments.ID as assignmentid, class_assignments.title,
				class_assignments.body, class_assignments.weighting,
				class_assignments.max_mark,
				class_assignments.due_date, class_assignments.file_types,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
			->join("class_assignments", "class_assignments.ID = user_assignments.assignmentid")
			->join("users", "users.ID = user_assignments.userid")
			->get("user_assignments");
	}

	public function delete_user_assignment($id) 
	{
		$this->db->where("ID", $id)->delete("user_assignments");
	}

	public function update_user_assignment($id, $data) 
	{
		$this->db->where("ID", $id)->update("user_assignments", $data);
	}

	public function get_your_assignments_total($userid) 
	{
		$s = $this->db
				->select("COUNT(*) as num")
				->join("classes", "classes.ID = class_assignments.classid")
				->join("class_students", "class_students.classid = class_assignments.classid", "left outer")
				->join("user_assignments", "user_assignments.assignmentid = class_assignments.ID AND user_assignments.userid =" . $userid)
				->group_start()
				->where("class_students.userid", $userid)
				->group_end()
				->order_by("class_assignments.due_date")
				->get("class_assignments");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_your_assignments_dt($userid, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"class_assignments.title",
			"classes.name"
			)
		);

		return $this->db
			->select("class_assignments.title, class_assignments.ID,
				class_assignments.due_date, class_assignments.type,
				class_assignments.weighting, class_assignments.max_mark,
				user_assignments.mark, user_assignments.timestamp,
				(user_assignments.mark * class_assignments.max_mark / 100) as `user_score`,
				classes.name, classes.ID as classid")
			->join("classes", "classes.ID = class_assignments.classid")
			->join("class_students", "class_students.classid = class_assignments.classid", "left outer")
			->join("user_assignments", "user_assignments.assignmentid = class_assignments.ID AND user_assignments.userid =" . $userid, "left outer")
			->group_start()
			->where("class_students.userid", $userid)
			->group_end()
			->order_by("class_assignments.due_date")
			->limit($datatable->length, $datatable->start)
			->get("class_assignments");
			
	}

	public function get_user_assignments_class_all($userid, $classid) 
	{
		return $this->db
			->select("class_assignments.title, class_assignments.ID,
				class_assignments.due_date, class_assignments.type,
				class_assignments.weighting, class_assignments.max_mark,
				user_assignments.mark, user_assignments.timestamp,
				classes.name, classes.ID as classid")
			->join("classes", "classes.ID = class_assignments.classid")
			->join("class_students", "class_students.classid = class_assignments.classid", "left outer")
			->join("user_assignments", "user_assignments.assignmentid = class_assignments.ID AND user_assignments.userid =" . $userid, "left outer")
			->group_start()
			->where("class_students.userid", $userid)
			->where("class_assignments.classid", $classid)
			->group_end()
			->order_by("class_assignments.due_date")
			->get("class_assignments");
			
	}

	public function get_class_events($start, $end, $classid) 
	{
		return $this->db
			->where("classid", $classid)
			->where("start >=", $start)
			->where("end <=", $end)
			->get("calendar_events");
	}
        
        public function get_room_events($start, $end, $room) 
	{
		return $this->db
			->where("room", $room)
			->where("start >=", $start)
			->where("end <=", $end)
			->get("calendar_events");
	}

	public function add_class_event($data) 
	{
		$this->db->insert("calendar_events", $data);
	}

	public function get_class_event($id) 
	{
		return $this->db->where("ID", $id)->get("calendar_events");
	}

	public function update_class_event($id, $data) 
	{
		$this->db->where("ID", $id)->update("calendar_events", $data);
	}

	public function delete_class_event($id) 
	{
		$this->db->where("ID", $id)->delete("calendar_events");
	}

	public function get_user_classes($userid) 
	{
		return $this->db
			->where("class_students.userid", $userid)
			->select("class_students.ID, class_students.classid, class_students.userid,
				class_students.teacher_flag,
				classes.name,
				subjects.name as subject")
			->join("classes", "classes.ID = class_students.classid")
			->join("subjects", "subjects.ID = classes.subjectid")
			->get("class_students");
	}

	public function add_reading_book($data) 
	{
		$this->db->insert("class_books", $data);
	}

	public function get_reading_books($classid) 
	{
		return $this->db
			->where("class_books.classid", $classid)
			->select("library_books.title, library_books.ID as bookid, library_books.image,
				library_books.author, class_books.ID")
			->join("library_books", "library_books.ID = class_books.bookid")
			->get("class_books");
	}

	public function get_reading_book($id) 
	{
		return $this->db->where("ID", $id)->get("class_books");
	}

	public function delete_book($id) 
	{
		$this->db->where("ID", $id)->delete("class_books");
	}

	public function add_class_file($data) 
	{
		$this->db->insert("class_files", $data);
	}

	public function get_class_file($id) 
	{
		return $this->db->where("ID", $id)->get("class_files");
	}

	public function delete_file($id) 
	{
		$this->db->where("ID", $id)->delete("class_files");
	}

	public function get_files($id) 
	{
		return $this->db
			->where("class_files.classid", $id)
			->select("files.file_name, files.ID as fileid, files.file_type,
				files.file_size, files.upload_file_name,
				class_files.ID")
			->join("files", "files.ID = class_files.fileid")
			->get("class_files");
	}

	public function add_attendance($data) 
	{
		$this->db->insert("attendance_sheets", $data);
		return $this->db->insert_id();
	}

	public function add_attendance_entry($data) 
	{
		$this->db->insert("attendance_sheet_entries", $data);
	}

	public function get_attendance_count($id) 
	{
		$s = $this->db->where("classid", $id)->select("COUNT(*) as num")
			->get("attendance_sheets");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_class_attendance($id, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
			"users.username",
			)
		);

		$this->db->where("attendance_sheets.classid", $id);

		return $this->db
			->select("attendance_sheets.ID, attendance_sheets.attendance_date,
				attendance_sheets.attendance,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name,
				calendar_events.title, calendar_events.ID as eventid, calendar_events.start")
			->join("users", "users.ID = attendance_sheets.teacherid")
			->join("calendar_events", "calendar_events.ID = attendance_sheets.eventid")
			->limit($datatable->length, $datatable->start)
			->get("attendance_sheets");
	}

	public function get_class_attendance_sheet($id) 
	{
		return $this->db
			->where("attendance_sheets.ID", $id)
			->select("attendance_sheets.ID, attendance_sheets.attendance_date,
				attendance_sheets.attendance, attendance_sheets.classid,
				attendance_sheets.eventid,
				users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name,
				classes.name")
			->join("users", "users.ID = attendance_sheets.teacherid")
			->join("classes", "classes.ID = attendance_sheets.classid")
			->get("attendance_sheets");
	}

	public function update_attendance_sheet($id, $data) 
	{
		$this->db->where("ID", $id)->update("attendance_sheets", $data);
	}

	public function delete_attendance_sheet($id) 
	{
		$this->db->where("ID", $id)->delete("attendance_sheets");
	}

	public function delete_attendance_sheet_entries($id) 
	{
		$this->db->where("attendanceid", $id)->delete("attendance_sheet_entries");
	}

	public function get_attendance_sheet_entries($id, $attendanceid) 
	{
		return $this->db
			->where("class_students.classid", $id)
			->select("users.ID as userid, users.username, users.email,
				users.email_notification, users.avatar, users.first_name,
				users.last_name, users.online_timestamp,
				class_students.teacher_flag,
				attendance_sheet_entries.present, attendance_sheet_entries.absent,
				attendance_sheet_entries.late, attendance_sheet_entries.holiday,
				attendance_sheet_entries.notes")
			->join("users", "users.ID = class_students.userid")
			->join("attendance_sheet_entries", "attendance_sheet_entries.userid = class_students.userid AND attendance_sheet_entries.attendanceid = " . $attendanceid, "left outer")
			->get("class_students");
	}

	public function add_grade($data) 
	{
		$this->db->insert("class_grades", $data);
	}

	public function delete_class_grade($id) 
	{
		$this->db->where("ID", $id)->delete("class_grades");
	}

	public function get_class_grade($id) 
	{
		return $this->db->where("ID", $id)->get("class_grades");
	}

	public function update_class_grade($id, $data) 
	{
		$this->db->where("ID", $id)->update("class_grades", $data);
	}

	public function get_total_class_grades($id) 
	{
		$s = $this->db
			->where("classid", $id)
			->select("COUNT(*) as num")->get("class_grades");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}

	public function get_class_grades($id, $datatable) 
	{
		$datatable->db_order();

		$datatable->db_search(array(
                            "class_grades.grade"
			)
		);

		$this->db->where("class_grades.classid", $id);

		return $this->db
			->select("class_grades.ID, class_grades.grade, class_grades.min_score,
				class_grades.max_score")
			->limit($datatable->length, $datatable->start)
			->get("class_grades");
	}

	public function get_class_grades_all($id) 
	{
		return $this->db
			->where("classid", $id)
			->select("class_grades.ID, class_grades.grade, class_grades.min_score,
				class_grades.max_score")
			->get("class_grades");
	}

	public function get_grades_classes($classes) 
	{
		foreach($classes as $classid) {
			$this->db->or_where("classid", $classid);
		}
		return $this->db
			->select("class_grades.ID, class_grades.grade, class_grades.min_score,
				class_grades.max_score, class_grades.classid")
			->get("class_grades");
	}

	public function get_all_user_classes($userid) 
	{
		return $this->db
			->where("class_students.userid", $userid)
			->select("classes.name, classes.ID")
			->join("classes", "classes.ID = class_students.classid")
			->get("class_students");
	}

}

?>