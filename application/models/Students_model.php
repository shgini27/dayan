<?php

class Students_Model extends CI_Model {

    public function get_total_groups_your($userid) {
        $s = $this->db
                ->where("student_group_users.userid", $userid)
                ->select("COUNT(*) as num")
                ->join("student_groups", "student_groups.ID = student_group_users.groupid")
                ->group_by("student_group_users.groupid")
                ->get("student_group_users");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_groups_your($userid, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "student_groups.name",
            "users.username"
                )
        );

        return $this->db
                        ->where("student_group_users.userid", $userid)
                        ->select("student_groups.ID, student_groups.name, student_groups.description,
				student_groups.teacherid,
				users.username, users.avatar, users.online_timestamp, users.first_name,
				users.last_name")
                        ->join("student_groups", "student_groups.ID = student_group_users.groupid")
                        ->join("users", "users.ID = student_groups.teacherid", "left outer")
                        ->limit($datatable->length, $datatable->start)
                        ->group_by("student_group_users.groupid")
                        ->get("student_group_users");
    }

    public function get_all_groups() {
        return $this->db->get("student_groups");
    }

    public function get_total_groups() {
        $s = $this->db->select("COUNT(*) as num")->get("student_groups");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_groups($datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "student_groups.name",
            "users.username"
                )
        );

        return $this->db
                        ->select("student_groups.ID, student_groups.name, student_groups.description,
				student_groups.teacherid,
				users.username, users.avatar, users.online_timestamp, users.first_name,
				users.last_name")
                        ->join("users", "users.ID = student_groups.teacherid", "left outer")
                        ->limit($datatable->length, $datatable->start)
                        ->get("student_groups");
    }

    public function add_student_group($data) {
        $this->db->insert("student_groups", $data);
        return $this->db->insert_id();
    }

    public function get_student_group($id) {
        return $this->db->where("ID", $id)->get("student_groups");
    }

    public function delete_student_group($id) {
        $this->db->where("ID", $id)->delete("student_groups");
    }

    public function update_student_group($id, $data) {
        $this->db->where("ID", $id)->update("student_groups", $data);
    }

    public function get_group_user_check($userid, $groupid) {
        return $this->db->where("userid", $userid)
                        ->where("groupid", $groupid)
                        ->get("student_group_users");
    }

    public function add_student_to_group($data) {
        $this->db->insert("student_group_users", $data);
    }

    public function get_total_users_in_group($groupid) {
        $s = $this->db->select("COUNT(*) as num")->get("student_group_users");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_group_users($groupid, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "users.username"
                )
        );

        return $this->db
                        ->where("student_group_users.groupid", $groupid)
                        ->select("student_group_users.ID,
				users.username, users.avatar, users.online_timestamp, users.first_name,
				users.last_name")
                        ->join("users", "users.ID = student_group_users.userid")
                        ->limit($datatable->length, $datatable->start)
                        ->get("student_group_users");
    }

    public function get_student_from_group($id) {
        return $this->db->where("ID", $id)->get("student_group_users");
    }

    public function delete_user_from_group($id) {
        $this->db->where("ID", $id)->delete("student_group_users");
    }

    public function get_announcements($groupid) {
        return $this->db
                        ->where("groupid", $groupid)
                        ->order_by("ID", "DESC")
                        ->limit(5)
                        ->get("student_group_announcements");
    }

    public function add_announcement($data) {
        $this->db->insert("student_group_announcements", $data);
    }

    public function get_announcement($id) {
        return $this->db
                        ->where("student_group_announcements.ID", $id)
                        ->select("student_group_announcements.ID, student_group_announcements.title,
				student_group_announcements.body, student_group_announcements.timestamp,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name,
				student_groups.name, student_groups.ID as groupid")
                        ->join("users", "users.ID = student_group_announcements.userid")
                        ->join("student_groups", "student_groups.ID = student_group_announcements.groupid")
                        ->get("student_group_announcements");
    }

    public function delete_announcement($id) {
        $this->db->where("ID", $id)->delete("student_group_announcements");
    }

    public function update_announcement($id, $data) {
        $this->db->where("ID", $id)->update("student_group_announcements", $data);
    }

    public function get_total_announcements($groupid) {
        $s = $this->db->where("groupid", $groupid)->select("COUNT(*) as num")
                ->get("student_group_announcements");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_announcements_dt($groupid, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "student_group_announcements.title",
            "users.username"
                )
        );

        return $this->db
                        ->where("student_group_announcements.groupid", $groupid)
                        ->select("student_group_announcements.ID, student_group_announcements.title,
				student_group_announcements.body, student_group_announcements.timestamp,
				users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name")
                        ->join("users", "users.ID = student_group_announcements.userid")
                        ->limit($datatable->length, $datatable->start)
                        ->get("student_group_announcements");
    }

    public function get_students_from_group($id) {
        return $this->db
                        ->where("student_group_users.groupid", $id)
                        ->select("users.ID as userid, users.username, users.email,
				users.email_notification")
                        ->join("users", "users.ID = student_group_users.userid")
                        ->get("student_group_users");
    }

    public function get_user_announcement($userid, $id) {
        return $this->db->where("announcementid", $id)
                        ->where("userid", $userid)->get("student_group_announcement_users");
    }

    public function add_user_announcement($data) {
        $this->db->insert("student_group_announcement_users", $data);
    }

    public function get_user_announcements($id) {
        return $this->db
                        ->where("student_group_announcement_users.announcementid", $id)
                        ->select("users.username, users.avatar, users.online_timestamp,
				users.first_name, users.last_name")
                        ->join("users", "users.ID = student_group_announcement_users.userid")
                        ->get("student_group_announcement_users");
    }

    public function get_all_students() {
        return $this->db
                        ->select("users.ID, users.username")
                        ->join("user_roles", "user_roles.ID = users.user_role")
                        ->where("user_roles.student", 1)
                        ->get("users");
    }

    public function get_all_teachers() {
        return $this->db
                        ->select("users.ID, users.username")
                        ->join("user_roles", "user_roles.ID = users.user_role")
                        ->where("user_roles.teacher", 1)
                        ->get("users");
    }

    public function get_total_students() {
        $s = $this->db
                ->select("COUNT(*) as num")
                ->join("user_roles", "users.user_role = user_roles.ID")
                ->where("user_roles.student", 1)
                ->get("users");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }
    
    /**

     * Method to get dropped students
     * @return object     /
     */
    public function get_dropped_students() {
        return $this->db
                        ->select("users.ID, users.username, users.email, users.online_timestamp,
                                users.avatar, users.first_name, users.last_name, 
                                classes.name as class_name, dropped_student.agreement_number")
                        ->join("users", "users.ID = dropped_student.user_id", "LEFT")
                        ->join("classes", "classes.ID = dropped_student.class_id", "LEFT")
                        ->where("dropped_student.flag_teacher", 0)
                        ->get("dropped_student");
    }
    
    /**

     * Method to get dropped students by class
     * @return object     /
     */
    public function get_all_dropped_students_by_class($class_id) {
        return $this->db
                        ->select("users.ID, users.username, users.email, users.online_timestamp,
                                users.avatar, users.first_name, users.last_name, users.fathers_name,
                                classes.name as class_name, dropped_student.agreement_number")
                        ->join("users", "users.ID = dropped_student.user_id", "LEFT")
                        ->join("classes", "classes.ID = dropped_student.class_id", "LEFT")
                        ->where("dropped_student.flag_teacher", 0)
                        ->where("dropped_student.class_id", $class_id)
                        ->get("dropped_student");
    }
    
    /**

     * Method to get count of dropped students
     * @return int Total number of dropped students     /
     */
    public function get_total_dropped_students() {
        $s = $this->db
                ->select("COUNT(*) as num")
                ->where("dropped_student.flag_teacher", 0)
                ->get("dropped_student");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }
    
    /**

     * Method to get dropped students
     * @param int $class_id Id of class
     * @return int Total dropped students of this given class     /
     */
    public function get_dropped_students_by_class($class_id) {
        $s = $this->db
                ->select("COUNT(*) as num")
                ->where("dropped_student.class_id", $class_id)
                ->where("dropped_student.flag_teacher", 0)
                ->get("dropped_student");
        $r = $s->row();
        if (isset($r->num)){
            return $r->num;
        }
        
        return 0;
    }

    public function get_students($datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "users.username",
            "users.first_name",
            "users.last_name",
            "users.email"
                )
        );

        return $this->db
                        ->where("user_roles.student", 1)
                        ->select("users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name, users.ID, users.email")
                        ->join("user_roles", "users.user_role = user_roles.ID")
                        ->limit($datatable->length, $datatable->start)
                        ->get("users");
    }

    public function get_student($id) {
        return $this->db
                        ->where("user_roles.student", 1)
                        ->where("users.ID", $id)
                        ->select("users.username, users.avatar, users.online_timestamp, 
				users.first_name, users.last_name, users.fathers_name, users.ID, users.email,
				users.points, users.aboutme, users.joined, users.active,
				users.IP, users.password, users.address_line_1,
				users.mobile_phone, users.state, users.city, users.country,
				users.phone")
                        ->join("user_roles", "users.user_role = user_roles.ID")
                        ->get("users");
    }

    public function get_student_attendance($id, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "classes.name"
                )
        );

        return $this->db
                        ->where("attendance_sheet_entries.userid", $id)
                        ->select("classes.name, classes.ID as classid,
				attendance_sheet_entries.ID, attendance_sheet_entries.present,
				attendance_sheet_entries.absent, attendance_sheet_entries.holiday,
				attendance_sheet_entries.late, attendance_sheet_entries.notes,
				attendance_sheets.attendance_date,
				calendar_events.title, calendar_events.start, calendar_events.ID as eventid")
                        ->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
                        ->join("classes", "classes.ID = attendance_sheets.classid")
                        ->join("calendar_events", "calendar_events.ID = attendance_sheets.eventid")
                        ->limit($datatable->length, $datatable->start)
                        ->get("attendance_sheet_entries");
    }

    public function get_student_attendance_by_class($id, $datatable) {
        $datatable->db_order();

        $datatable->db_search(array(
            "classes.name"
                )
        );

        return $this->db
                        ->where("attendance_sheet_entries.userid", $id)
                        ->select("classes.name, classes.ID as classid,
				SUM(attendance_sheet_entries.present) as present,
				SUM(attendance_sheet_entries.absent) as absent, 
				SUM(attendance_sheet_entries.holiday) as holiday,
				SUM(attendance_sheet_entries.late) as late")
                        ->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
                        ->join("classes", "classes.ID = attendance_sheets.classid")
                        ->join("calendar_events", "calendar_events.ID = attendance_sheets.eventid")
                        ->limit($datatable->length, $datatable->start)
                        ->group_by("attendance_sheets.classid")
                        ->get("attendance_sheet_entries");
    }

    public function get_student_attendance_by_class_total($id) {
        $s = $this->db
                ->where("attendance_sheet_entries.userid", $id)
                ->select("COUNT(*) as num")
                ->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
                ->join("classes", "classes.ID = attendance_sheets.classid")
                ->join("calendar_events", "calendar_events.ID = attendance_sheets.eventid")
                ->group_by("attendance_sheets.classid")
                ->get("attendance_sheet_entries");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function get_student_attendance_for_class($userid, $classid) {
        return $this->db
                        ->where("attendance_sheet_entries.userid", $userid)
                        ->where("attendance_sheets.classid", $classid)
                        ->select("classes.name, classes.ID as classid,
				SUM(attendance_sheet_entries.present) as present,
				SUM(attendance_sheet_entries.absent) as absent, 
				SUM(attendance_sheet_entries.holiday) as holiday,
				SUM(attendance_sheet_entries.late) as late")
                        ->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
                        ->join("classes", "classes.ID = attendance_sheets.classid")
                        ->group_by("attendance_sheets.classid")
                        ->get("attendance_sheet_entries");
    }

    public function get_student_attendance_total($id) {
        $s = $this->db
                ->where("attendance_sheet_entries.userid", $id)
                ->select("COUNT(*) as num")
                ->join("attendance_sheets", "attendance_sheets.ID = attendance_sheet_entries.attendanceid")
                ->join("classes", "classes.ID = attendance_sheets.classid")
                ->join("calendar_events", "calendar_events.ID = attendance_sheets.eventid")
                ->get("attendance_sheet_entries");
        $r = $s->row();
        if (isset($r->num))
            return $r->num;
        return 0;
    }

    public function delete_student($id) {
        $this->db->where("ID", $id)->delete("users");
    }

    public function get_student_report($id) {
        return $this->db->where("userid", $id)->get("user_reports");
    }

    public function add_report($data) {
        $this->db->insert("user_reports", $data);
    }

    public function update_report($id, $data) {
        $this->db->where("ID", $id)->update("user_reports", $data);
    }

}

?>