<div id="responsive-menu-links">
          <select name='link' OnChange="window.location.href=$(this).val();" class="form-control">
          <option value='<?php echo site_url() ?>'><?php echo lang("ctn_154") ?></option>
          <option value='<?php echo site_url("user_settings") ?>'><?php echo lang("ctn_156") ?></option>
          <?php if($this->user->loggedin && isset($this->user->info->user_role_id) && 
           ($this->user->info->admin || $this->user->info->admin_settings || $this->user->info->admin_members || $this->user->info->admin_payment)

           ) : ?>
           <?php if($this->user->info->admin || $this->user->info->admin_settings) : ?>
            <option value='<?php echo site_url("admin/settings") ?>'><?php echo lang("ctn_158") ?></option>
            <option value='<?php echo site_url("admin/social_settings") ?>'><?php echo lang("ctn_159") ?></option>
            <option value='<?php echo site_url("admin/school_settings") ?>'><?php echo lang("ctn_431") ?></option>
            <option value='<?php echo site_url("admin/date_settings") ?>'><?php echo lang("ctn_442") ?></option>
            <option value='<?php echo site_url("admin/invoice_settings") ?>'><?php echo lang("ctn_412") ?></option>
            <option value='<?php echo site_url("admin/section_settings") ?>'><?php echo lang("ctn_389") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
            <option value='<?php echo site_url("admin/members") ?>'><?php echo lang("ctn_160") ?></option>
            <option value='<?php echo site_url("admin/custom_fields") ?>'><?php echo lang("ctn_346") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin) : ?>
            <option value='<?php echo site_url("admin/user_roles") ?>'><?php echo lang("ctn_316") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
            <option value='<?php echo site_url("admin/user_groups") ?>'><?php echo lang("ctn_161") ?></option>
            <option value='<?php echo site_url("admin/ipblock") ?>'><?php echo lang("ctn_162") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin) : ?>
            <option value='<?php echo site_url("admin/email_templates") ?>'><?php echo lang("ctn_163") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
            <option value='<?php echo site_url("admin/email_members") ?>'><?php echo lang("ctn_164") ?></option>
            <?php endif; ?>
            <?php if($this->user->info->admin || $this->user->info->admin_payment) : ?>
            <option value='<?php echo site_url("admin/currencies") ?>'><?php echo lang("ctn_426") ?></option>
            <option value='<?php echo site_url("admin/payment_logs") ?>'><?php echo lang("ctn_288") ?></option>
            <?php endif; ?>
          <?php endif; ?>
           <?php if($this->settings->info->announcements_section && $this->common->has_permissions(array("admin", "announcement_manager", "announcement_viewer"), $this->user)) : ?>
            <option value='<?php echo site_url("announcements") ?>'><?php echo lang("ctn_447") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->classes_section && $this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
            <option value='<?php echo site_url("classes") ?>'><?php echo lang("ctn_708") ?></option>
            <option value='<?php echo site_url("classes/your") ?>'><?php echo lang("ctn_625") ?></option>
            <option value='<?php echo site_url("classes/your_assignments") ?>'><?php echo lang("ctn_594") ?></option>
            <option value='<?php echo site_url("classes/your_timetable") ?>'><?php echo lang("ctn_709") ?></option>
            <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user)) : ?>
            <option value='<?php echo site_url("classes/categories") ?>'><?php echo lang("ctn_710") ?></option>
            <option value='<?php echo site_url("classes/branches") ?>'><?php echo lang("ctn_985") ?></option>
            <option value='<?php echo site_url("classes/rooms") ?>'><?php echo lang("ctn_992") ?></option>
            <option value='<?php echo site_url("classes/overall_events") ?>'><?php echo lang("ctn_1007") ?></option>
            <?php endif; ?>
           <?php endif; ?>
           <?php if($this->settings->info->subjects_section && $this->common->has_permissions(array("admin", "subject_manager", "subject_viewer"), $this->user)) : ?>
             <option value='<?php echo site_url("subjects") ?>'><?php echo lang("ctn_711") ?></option>
             <option value='<?php echo site_url("subjects/teachers") ?>'><?php echo lang("ctn_712") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->students_section && $this->common->has_permissions(array("admin", "student_group_manager", "student_group_viewer", "student_manager", "student_viewer"), $this->user)) : ?>
            <?php if($this->common->has_permissions(array("admin", "student_manager", "student_viewer"), $this->user)) : ?>
            <option value='<?php echo site_url("students") ?>'><?php echo lang("ctn_713") ?></option>
            <option value='<?php echo site_url("students/dropped_students") ?>'><?php echo lang("ctn_1009") ?></option>
            <?php endif; ?>
            <?php if($this->common->has_permissions(array("admin", "student_group_manager", "student_group_viewer"), $this->user)) : ?>
            <!--
            <option value='<?php echo site_url("students/groups") ?>'><?php echo lang("ctn_714") ?></option>
            <option value='<?php echo site_url("students/your") ?>'><?php echo lang("ctn_715") ?></option>
            -->
            <?php endif; ?>
           <?php endif; ?>
           <?php if($this->settings->info->parent_section) : ?>
             <?php if($this->common->has_permissions(array("admin", "parent_manager"), $this->user)) : ?>
              <option value='<?php echo site_url("parents") ?>'><?php echo lang("ctn_863") ?></option>
             <?php endif; ?>
           <?php endif; ?>
           <?php if($this->settings->info->files_section && $this->common->has_permissions(array("admin", "files_manager", "files_viewer"), $this->user)) : ?>
             <option value='<?php echo site_url("files") ?>'><?php echo lang("ctn_716") ?></option>
             <?php if($this->common->has_permissions(array("admin", "files_manager"), $this->user)) : ?>
             <option value='<?php echo site_url("files/categories") ?>'><?php echo lang("ctn_710") ?></option>
           <?php endif; ?>
           <?php endif; ?>
           <?php if($this->settings->info->finance_section && $this->common->has_permissions(array("admin", "finance_manager", "finance_viewer"), $this->user)) : ?>
             <option value='<?php echo site_url("finance") ?>'><?php echo lang("ctn_717") ?></option>
             <?php if($this->common->has_permissions(array("admin", "finance_manager"), $this->user)) : ?>
             <option value='<?php echo site_url("finance/categories") ?>'><?php echo lang("ctn_710") ?></option>
           <?php endif; ?>
           <?php endif; ?>
           <?php if($this->settings->info->invoices_section && $this->common->has_permissions(array("admin", "invoice_manager", "invoice_viewer"), $this->user)) : ?>
            <?php if($this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) : ?>
              <option value='<?php echo site_url("invoices") ?>'><?php echo lang("ctn_718") ?></option>
              <option value='<?php echo site_url("invoices/templates") ?>'><?php echo lang("ctn_719") ?></option>
              <!-- <option value='<?php echo site_url("invoices/reoccuring") ?>'><?php echo lang("ctn_670") ?></option> -->
              <option value='<?php echo site_url("invoices/paying_accounts") ?>'><?php echo lang("ctn_720") ?></option>
              <option value='<?php echo site_url("invoices/payed_students") ?>'><?php echo lang("ctn_1030") ?></option>
              <option value='<?php echo site_url("invoices/not_payed_students") ?>'><?php echo lang("ctn_1031") ?></option>
              <option value='<?php echo site_url("invoices/partially_payed_students") ?>'><?php echo lang("ctn_1032") ?></option>
            <?php endif; ?>
            <option value='<?php echo site_url("invoices/your") ?>'><?php echo lang("ctn_721") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->behaviour_section && $this->common->has_permissions(array("admin", "behaviour_manager", "behaviour_viewer"), $this->user)) : ?>
            <?php if($this->common->has_permissions(array("admin", "behaviour_manager"), $this->user)) : ?>
                    <option value='<?php echo site_url("behaviour") ?>'><?php echo lang("ctn_455") ?></option>
                    <option value='<?php echo site_url("behaviour/rules") ?>'><?php echo lang("ctn_463") ?></option>
                  <?php endif; ?>
                    <option value='<?php echo site_url("behaviour/your") ?>'><?php echo lang("ctn_722") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->library_section && $this->common->has_permissions(array("admin", "library_manager", "library_viewer"), $this->user)) : ?>
              <option value='<?php echo site_url("library") ?>'><?php echo lang("ctn_723") ?></option>
              <option value='<?php echo site_url("library/reservations") ?>'><?php echo lang("ctn_724") ?></option>
              <option value='<?php echo site_url("library/checkedout") ?>'><?php echo lang("ctn_725") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->reports_section && $this->common->has_permissions(array("admin", "report_manager"), $this->user)) : ?>
            <!-- <option value='<?php echo site_url("reports/finance") ?>'><?php echo lang("ctn_726") ?></option>
            <option value='<?php echo site_url("reports/attendance") ?>'><?php echo lang("ctn_727") ?></option> -->
               <option value='<?php echo site_url("reports/statistics") ?>'><?php echo lang("ctn_979") ?></option>
           <?php endif; ?>
           <?php if($this->settings->info->hostel_section && $this->common->has_permissions(array("admin", "hostel_manager"), $this->user)) : ?>
                <option value='<?php echo site_url("hostel") ?>'><?php echo lang("ctn_947") ?></option>
                <option value='<?php echo site_url("hostel/rooms") ?>'><?php echo lang("ctn_948") ?></option>
                <option value='<?php echo site_url("hostel/bookings") ?>'><?php echo lang("ctn_949") ?></option>
          <?php endif; ?>
          </select> 
        </div>