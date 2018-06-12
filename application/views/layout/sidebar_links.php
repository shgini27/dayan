<ul class="newnav nav nav-sidebar">
           <?php if($this->user->loggedin && isset($this->user->info->user_role_id) && 
           ($this->user->info->admin || $this->user->info->admin_settings || $this->user->info->admin_members || $this->user->info->admin_payment)

           ) : ?>
              <li id="admin_sb">
                <a data-toggle="collapse" data-parent="#admin_sb" href="#admin_sb_c" class="collapsed <?php if(isset($activeLink['admin'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-wrench sidebar-icon sidebar-icon-red"></span> <?php echo lang("ctn_157") ?>
                  <span class="plus-sidebar"><span class="glyphicon <?php if(isset($activeLink['admin'])) : ?>glyphicon-menu-down<?php else : ?>glyphicon-menu-right<?php endif; ?>"></span></span>
                </a>
                <div id="admin_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['admin'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <?php if($this->user->info->admin || $this->user->info->admin_settings) : ?>
                      <li class="<?php if(isset($activeLink['admin']['settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/settings") ?>"> <?php echo lang("ctn_158") ?></a></li>
                      <li class="<?php if(isset($activeLink['admin']['social_settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/social_settings") ?>"> <?php echo lang("ctn_159") ?></a></li>
                      <li class="<?php if(isset($activeLink['admin']['school_settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/school_settings") ?>"> <?php echo lang("ctn_431") ?></a></li>
                      <li class="<?php if(isset($activeLink['admin']['date_settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/date_settings") ?>"> <?php echo lang("ctn_442") ?></a></li>
                      <li class="<?php if(isset($activeLink['admin']['invoice_settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/invoice_settings") ?>"> <?php echo lang("ctn_412") ?></a></li>
                      <li class="<?php if(isset($activeLink['admin']['section_settings'])) echo "active" ?>"><a href="<?php echo site_url("admin/section_settings") ?>"> <?php echo lang("ctn_389") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
                    <li class="<?php if(isset($activeLink['admin']['members'])) echo "active" ?>"><a href="<?php echo site_url("admin/members") ?>"> <?php echo lang("ctn_160") ?></a></li>
                    <li class="<?php if(isset($activeLink['admin']['custom_fields'])) echo "active" ?>"><a href="<?php echo site_url("admin/custom_fields") ?>"> <?php echo lang("ctn_346") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin) : ?>
                    <li class="<?php if(isset($activeLink['admin']['user_roles'])) echo "active" ?>"><a href="<?php echo site_url("admin/user_roles") ?>"> <?php echo lang("ctn_316") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
                    <li class="<?php if(isset($activeLink['admin']['user_groups'])) echo "active" ?>"><a href="<?php echo site_url("admin/user_groups") ?>"> <?php echo lang("ctn_161") ?></a></li>
                    <li class="<?php if(isset($activeLink['admin']['ipblock'])) echo "active" ?>"><a href="<?php echo site_url("admin/ipblock") ?>"> <?php echo lang("ctn_162") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin) : ?>
                      <li class="<?php if(isset($activeLink['admin']['email_templates'])) echo "active" ?>"><a href="<?php echo site_url("admin/email_templates") ?>"> <?php echo lang("ctn_163") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin || $this->user->info->admin_members) : ?>
                      <li class="<?php if(isset($activeLink['admin']['email_members'])) echo "active" ?>"><a href="<?php echo site_url("admin/email_members") ?>"> <?php echo lang("ctn_164") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin || $this->user->info->admin_payment) : ?>
                    <li class="<?php if(isset($activeLink['admin']['payment_currency'])) echo "active" ?>"><a href="<?php echo site_url("admin/currencies") ?>"> <?php echo lang("ctn_426") ?></a></li>
                    <li class="<?php if(isset($activeLink['admin']['payment_logs'])) echo "active" ?>"><a href="<?php echo site_url("admin/payment_logs") ?>"> <?php echo lang("ctn_288") ?></a></li>
                    <?php endif; ?>
                    <?php if($this->user->info->admin) : ?>
                      <li class="<?php if(isset($activeLink['admin']['tools'])) echo "active" ?>"><a href="<?php echo site_url("admin/tools") ?>"> <?php echo lang("ctn_945") ?></a></li>
                    <?php endif; ?>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
            <li class="<?php if(isset($activeLink['home']['general'])) echo "active" ?>"><a href="<?php echo site_url() ?>"><span class="glyphicon glyphicon-dashboard sidebar-icon sidebar-icon-orange"></span> <?php echo lang("ctn_154") ?> <span class="sr-only">(current)</span></a></li>
            <?php if($this->settings->info->announcements_section) : ?>
              <li id="behaviour_sb">
                  <a data-toggle="collapse" data-parent="#announcement_sb" href="#announcement_sb_c" class="collapsed <?php if(isset($activeLink['announcement'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-bullhorn sidebar-icon sidebar-icon-brown"></span> <?php echo lang("ctn_449") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="announcement_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['announcement'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                      <li class="<?php if(isset($activeLink['announcement']['general'])) echo "active" ?>"><a href="<?php echo site_url("announcements") ?>"> <?php echo lang("ctn_447") ?></a></li>
                    </ul>
                  </div>
                </li>
            <?php endif; ?>
            <?php if($this->settings->info->classes_section) : ?>
             <?php if($this->common->has_permissions(array("admin", "class_manager", "class_viewer"), $this->user)) : ?>
            <li id="classes_sb">
                <a data-toggle="collapse" data-parent="#classes_sb" href="#classes_sb_c" class="collapsed <?php if(isset($activeLink['classes'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-bell sidebar-icon sidebar-icon-red"></span> <?php echo lang("ctn_471") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="classes_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['classes'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <li class="<?php if(isset($activeLink['classes']['general'])) echo "active" ?>"><a href="<?php echo site_url("classes") ?>"> <?php echo lang("ctn_708") ?></a></li>
                    <li class="<?php if(isset($activeLink['classes']['your'])) echo "active" ?>"><a href="<?php echo site_url("classes/your") ?>"> <?php echo lang("ctn_625") ?></a></li>
                    <li class="<?php if(isset($activeLink['classes']['your_assignments'])) echo "active" ?>"><a href="<?php echo site_url("classes/your_assignments") ?>"> <?php echo lang("ctn_594") ?></a></li>
                    <li class="<?php if(isset($activeLink['classes']['your_timetable'])) echo "active" ?>"><a href="<?php echo site_url("classes/your_timetable") ?>"> <?php echo lang("ctn_709") ?></a></li>
                    <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user)) : ?>
                      <li class="<?php if(isset($activeLink['classes']['cats'])) echo "active" ?>"><a href="<?php echo site_url("classes/categories") ?>"> <?php echo lang("ctn_710") ?></a></li>
                      <li class="<?php if(isset($activeLink['classes']['branches'])) echo "active" ?>"><a href="<?php echo site_url("classes/branches") ?>"> <?php echo lang("ctn_985") ?></a></li>
                      <li class="<?php if(isset($activeLink['classes']['rooms'])) echo "active" ?>"><a href="<?php echo site_url("classes/rooms") ?>"> <?php echo lang("ctn_992") ?></a></li>
                      <li class="<?php if(isset($activeLink['classes']['all_timetable'])) echo "active" ?>"><a href="<?php echo site_url("classes/overall_events") ?>"> <?php echo lang("ctn_1007") ?></a></li>
                    <?php endif; ?>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->subjects_section) : ?>
              <?php if($this->common->has_permissions(array("admin", "subject_manager", "subject_viewer"), $this->user)) : ?>
              <li id="subject_sb">
                  <a data-toggle="collapse" data-parent="#subject_sb" href="#subject_sb_c" class="collapsed <?php if(isset($activeLink['subject'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-education sidebar-icon sidebar-icon-blue"></span> <?php echo lang("ctn_728") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="subject_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['subject'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                      <li class="<?php if(isset($activeLink['subject']['general'])) echo "active" ?>"><a href="<?php echo site_url("subjects") ?>"> <?php echo lang("ctn_711") ?></a></li>
                      <li class="<?php if(isset($activeLink['subject']['teachers'])) echo "active" ?>"><a href="<?php echo site_url("subjects/teachers") ?>"> <?php echo lang("ctn_712") ?></a></li>
                    </ul>
                  </div>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->students_section) : ?>
              <?php if($this->common->has_permissions(array("admin", "student_group_manager", "student_group_viewer", "student_manager", "student_viewer"), $this->user)) : ?>
              <li id="students_sb">
                  <a data-toggle="collapse" data-parent="#students_sb" href="#students_sb_c" class="collapsed <?php if(isset($activeLink['students'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-blackboard sidebar-icon sidebar-icon-orange"></span> <?php echo lang("ctn_481") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="students_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['students'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                        <?php if($this->common->has_permissions(array("admin", "student_manager", "student_viewer"), $this->user)) : ?>
                        <li class="<?php if(isset($activeLink['students']['general'])) echo "active" ?>"><a href="<?php echo site_url("students") ?>"> <?php echo lang("ctn_713") ?></a></li>
                      <?php endif; ?>
                      <?php if($this->common->has_permissions(array("admin", "student_group_manager", "student_group_viewer"), $this->user)) : ?>
                        <li class="<?php if(isset($activeLink['students']['groups'])) echo "active" ?>"><a href="<?php echo site_url("students/groups") ?>"> <?php echo lang("ctn_714") ?></a></li>
                        <li class="<?php if(isset($activeLink['students']['your'])) echo "active" ?>"><a href="<?php echo site_url("students/your") ?>"> <?php echo lang("ctn_715") ?></a></li>
                      <?php endif; ?>
                    </ul>
                  </div>
                </li>
              <?php endif; ?>
            <?php endif; ?>
              <?php if($this->settings->info->parent_section) : ?>
             <?php if($this->common->has_permissions(array("admin", "parent_manager"), $this->user)) : ?>
            <li id="parent_sb">
                <a data-toggle="collapse" data-parent="#parent_sb" href="#parent_sb_c" class="collapsed <?php if(isset($activeLink['parent'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-user sidebar-icon sidebar-icon-green"></span> <?php echo lang("ctn_862") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="parent_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['parent'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <li class="<?php if(isset($activeLink['parent']['general'])) echo "active" ?>"><a href="<?php echo site_url("parents") ?>"> <?php echo lang("ctn_863") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->files_section) : ?>
              <?php if($this->common->has_permissions(array("admin", "files_manager", "files_viewer"), $this->user)) : ?>
              <li id="classes_sb">
                  <a data-toggle="collapse" data-parent="#files_sb" href="#files_sb_c" class="collapsed <?php if(isset($activeLink['files'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-file sidebar-icon sidebar-icon-red"></span> <?php echo lang("ctn_555") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="files_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['files'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                      <li class="<?php if(isset($activeLink['files']['general'])) echo "active" ?>"><a href="<?php echo site_url("Files") ?>"> <?php echo lang("ctn_716") ?></a></li>
                      <?php if($this->common->has_permissions(array("admin", "files_manager"), $this->user)) : ?>
                      <li class="<?php if(isset($activeLink['files']['cats'])) echo "active" ?>"><a href="<?php echo site_url("files/categories") ?>"> <?php echo lang("ctn_710") ?></a></li>
                    <?php endif; ?>
                    </ul>
                  </div>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->finance_section) : ?>
              <?php if($this->common->has_permissions(array("admin", "finance_manager", "finance_viewer"), $this->user)) : ?>
              <li id="finance_sb">
                  <a data-toggle="collapse" data-parent="#finance_sb" href="#finance_sb_c" class="collapsed <?php if(isset($activeLink['finance'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-piggy-bank sidebar-icon sidebar-icon-brown"></span> <?php echo lang("ctn_606") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="finance_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['finance'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                      <li class="<?php if(isset($activeLink['finance']['general'])) echo "active" ?>"><a href="<?php echo site_url("finance") ?>"> <?php echo lang("ctn_717") ?></a></li>
                      <?php if($this->common->has_permissions(array("admin", "finance_manager"), $this->user)) : ?>
                      <li class="<?php if(isset($activeLink['finance']['cats'])) echo "active" ?>"><a href="<?php echo site_url("finance/categories") ?>"> <?php echo lang("ctn_710") ?></a></li>
                    <?php endif; ?>
                    </ul>
                  </div>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->invoices_section) : ?>
              <?php if($this->common->has_permissions(array("admin", "invoice_manager", "invoice_viewer"), $this->user)) : ?>
              <li id="invoice_sb">
                  <a data-toggle="collapse" data-parent="#invoice_sb" href="#invoice_sb_c" class="collapsed <?php if(isset($activeLink['invoice'])) echo "active" ?>" >
                    <span class="glyphicon glyphicon-education sidebar-icon sidebar-icon-pink"></span> <?php echo lang("ctn_614") ?>
                    <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                  </a>
                  <div id="invoice_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['invoice'])) echo "in" ?>">
                    <ul class="inner-sidebar-links">
                    <?php if($this->common->has_permissions(array("admin", "invoice_manager"), $this->user)) : ?>
                      <li class="<?php if(isset($activeLink['invoice']['general'])) echo "active" ?>"><a href="<?php echo site_url("invoices") ?>"> <?php echo lang("ctn_718") ?></a></li>
                      <li class="<?php if(isset($activeLink['invoice']['templates'])) echo "active" ?>"><a href="<?php echo site_url("invoices/templates") ?>"> <?php echo lang("ctn_719") ?></a></li>
                      <li class="<?php if(isset($activeLink['invoice']['reoccuring'])) echo "active" ?>"><a href="<?php echo site_url("invoices/reoccuring") ?>"> <?php echo lang("ctn_670") ?></a></li>
                      <li class="<?php if(isset($activeLink['invoice']['items'])) echo "active" ?>"><a href="<?php echo site_url("invoices/items") ?>"> <?php echo lang("ctn_946") ?></a></li>
                      <li class="<?php if(isset($activeLink['invoice']['pay'])) echo "active" ?>"><a href="<?php echo site_url("invoices/paying_accounts") ?>"> <?php echo lang("ctn_720") ?></a></li>
                    <?php endif; ?>
                    <li class="<?php if(isset($activeLink['invoice']['your'])) echo "active" ?>"><a href="<?php echo site_url("invoices/your") ?>"> <?php echo lang("ctn_721") ?></a></li>
                    </ul>
                  </div>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <?php if($this->settings->info->behaviour_section) : ?>
            <?php if($this->common->has_permissions(array("admin", "behaviour_manager", "behaviour_viewer"), $this->user)) : ?>
            <li id="behaviour_sb">
                <a data-toggle="collapse" data-parent="#behaviour_sb" href="#behaviour_sb_c" class="collapsed <?php if(isset($activeLink['behaviour'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-flag sidebar-icon sidebar-icon-red"></span> <?php echo lang("ctn_729") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="behaviour_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['behaviour'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                  <?php if($this->common->has_permissions(array("admin", "behaviour_manager"), $this->user)) : ?>
                    <li class="<?php if(isset($activeLink['behaviour']['general'])) echo "active" ?>"><a href="<?php echo site_url("behaviour") ?>"> <?php echo lang("ctn_455") ?></a></li>
                    <li class="<?php if(isset($activeLink['behaviour']['rules'])) echo "active" ?>"><a href="<?php echo site_url("behaviour/rules") ?>"> <?php echo lang("ctn_730") ?></a></li>
                  <?php endif; ?>
                    <li class="<?php if(isset($activeLink['behaviour']['your'])) echo "active" ?>"><a href="<?php echo site_url("behaviour/your") ?>"> <?php echo lang("ctn_722") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <?php if($this->settings->info->library_section) : ?>
            <?php if($this->common->has_permissions(array("admin", "library_manager", "library_viewer"), $this->user)) : ?>
            <li id="subject_sb">
                <a data-toggle="collapse" data-parent="#library_sb" href="#library_sb_c" class="collapsed <?php if(isset($activeLink['library'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-book sidebar-icon sidebar-icon-green"></span> <?php echo lang("ctn_731") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="library_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['library'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <li class="<?php if(isset($activeLink['library']['general'])) echo "active" ?>"><a href="<?php echo site_url("library") ?>"> <?php echo lang("ctn_723") ?></a></li>
                    <li class="<?php if(isset($activeLink['library']['reservations'])) echo "active" ?>"><a href="<?php echo site_url("library/reservations") ?>"> <?php echo lang("ctn_724") ?></a></li>
                    <li class="<?php if(isset($activeLink['library']['checkedout'])) echo "active" ?>"><a href="<?php echo site_url("library/checkedout") ?>"> <?php echo lang("ctn_725") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <?php if($this->settings->info->reports_section) : ?>
             <?php if($this->common->has_permissions(array("admin", "report_manager"), $this->user)) : ?>
            <li id="reports_sb">
                <a data-toggle="collapse" data-parent="#reports_sb" href="#reports_sb_c" class="collapsed <?php if(isset($activeLink['reports'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-list-alt sidebar-icon sidebar-icon-blue"></span> <?php echo lang("ctn_732") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="reports_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['reports'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <li class="<?php if(isset($activeLink['reports']['finance'])) echo "active" ?>"><a href="<?php echo site_url("reports/finance") ?>"> <?php echo lang("ctn_726") ?></a></li>
                    <li class="<?php if(isset($activeLink['reports']['attendance'])) echo "active" ?>"><a href="<?php echo site_url("reports/attendance") ?>"> <?php echo lang("ctn_727") ?></a></li>
                    <li class="<?php if(isset($activeLink['reports']['statistics'])) echo "active" ?>"><a href="<?php echo site_url("reports/statistics") ?>"> <?php echo lang("ctn_979") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <!-- CUSTOM ADDED SECTION DOCUMENTS START -->
          <?php if($this->settings->info->documents_section) : ?>
            <?php if($this->common->has_permissions(array("admin", "documents_manager", "documents_viewer"), $this->user)) : ?>
            <li id="documents_sb">
                <a data-toggle="collapse" data-parent="#documents_sb" href="#documents_sb_c" class="collapsed <?php if(isset($activeLink['documents'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-paperclip sidebar-icon sidebar-icon-red"></span> <?php echo lang("ctn_951") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="documents_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['documents'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                  <?php if($this->common->has_permissions(array("admin", "documents_manager"), $this->user)) : ?>
                    <li class="<?php if(isset($activeLink['documents']['documents'])) echo "active" ?>"><a href="<?php echo site_url("documents") ?>"> <?php echo lang("ctn_953") ?></a></li>
                    <li class="<?php if(isset($activeLink['documents']['certificate'])) echo "active" ?>"><a href="<?php echo site_url("documents/certificate") ?>"> <?php echo lang("ctn_954") ?></a></li>
                  <?php endif; ?>
                    <li class="<?php if(isset($activeLink['documents']['agreement'])) echo "active" ?>"><a href="<?php echo site_url("documents/agreement") ?>"> <?php echo lang("ctn_955") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <!-- CUSTOM ADDED SECTION DOCUMENTS ENDS -->
          <?php if($this->settings->info->hostel_section) : ?>
             <?php if($this->common->has_permissions(array("admin", "hostel_manager"), $this->user)) : ?>
            <li id="hostel_sb">
                <a data-toggle="collapse" data-parent="#hostel_sb" href="#hostel_sb_c" class="collapsed <?php if(isset($activeLink['hostel'])) echo "active" ?>" >
                  <span class="glyphicon glyphicon-home sidebar-icon sidebar-icon-orange"></span> <?php echo lang("ctn_925") ?>
                  <span class="plus-sidebar"><span class="glyphicon glyphicon-menu-right"></span></span>
                </a>
                <div id="hostel_sb_c" class="panel-collapse collapse sidebar-links-inner <?php if(isset($activeLink['hostel'])) echo "in" ?>">
                  <ul class="inner-sidebar-links">
                    <li class="<?php if(isset($activeLink['hostel']['general'])) echo "active" ?>"><a href="<?php echo site_url("hostel") ?>"> <?php echo lang("ctn_947") ?></a></li>
                    <li class="<?php if(isset($activeLink['hostel']['rooms'])) echo "active" ?>"><a href="<?php echo site_url("hostel/rooms") ?>"> <?php echo lang("ctn_948") ?></a></li>
                    <li class="<?php if(isset($activeLink['hostel']['bookings'])) echo "active" ?>"><a href="<?php echo site_url("hostel/bookings") ?>"> <?php echo lang("ctn_949") ?></a></li>
                  </ul>
                </div>
              </li>
            <?php endif; ?>
          <?php endif; ?>
            <li class="<?php if(isset($activeLink['settings']['general'])) echo "active" ?>"><a href="<?php echo site_url("user_settings") ?>"><span class="glyphicon glyphicon-cog sidebar-icon sidebar-icon-grey"></span> <?php echo lang("ctn_156") ?></a></li>       
          </ul>