<?php include("class_header.php") ?>

<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
    <div class="db-header-extra form-inline"> 
  
  <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || (isset($teacher_flag) && $this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
<a href="<?php echo site_url("classes/edit_class/" . $class->ID) ?>" class="btn btn-warning btn-sm"><?php echo lang("ctn_55") ?></a>
<?php endif; ?>

</div>
</div>

</div>


<div class="row">
<div class="col-md-8">

<div class="white-area-content content-separator">
<h3><?php echo $class->name ?></h3>
<?php echo $class->description ?>
<?php if($member_flag) : ?>
<?php echo $class->content ?>
<?php endif; ?>
</div>

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title">Upcoming <?php echo lang("ctn_546") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag) ) : ?>
<input type="button" class="btn btn-primary btn-sm" value="<?php echo lang("ctn_548") ?>" data-toggle="modal" data-target="#assignModal" />
<?php endif; ?>

</div>
</div>

<table class="table table-bordered table-striped table-hover">
<tr class="table-header"><td><?php echo lang("ctn_11") ?></td><td><?php echo lang("ctn_510") ?></td><td><?php echo lang("ctn_549") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
<?php foreach($assignments->result() as $r) : ?>
  <?php if($r->type == 0) {
    $type = lang("ctn_550");
  } elseif($r->type == 1) {
    $type = lang("ctn_551");
  }
  ?>
<tr><td><?php echo $r->title ?></td><td><?php echo date($this->settings->info->date_format, $r->due_date) ?></td><td><?php echo $type ?></td><td><a href="<?php echo site_url("classes/view_assignment/" . $r->ID) ?>" class="btn btn-primary btn-xs"><?php echo lang("ctn_552") ?></a> <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?> <a href="<?php echo site_url("classes/view_assignment_submissions/" . $r->ID) ?>" class="btn btn-info btn-xs"><?php echo lang("ctn_553") ?></a> <a href="<?php echo site_url("classes/edit_assignment/" . $r->ID) ?>" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_55") ?>"><span class="glyphicon glyphicon-cog"></span></a> <a href="<?php echo site_url("classes/delete_assignment/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs" onclick="return confirm(' <?php echo lang("ctn_317") ?>')" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_57") ?>"><span class="glyphicon glyphicon-trash"></span></a> <?php endif; ?></td></tr>
<?php endforeach; ?>
</table>

<a href="<?php echo site_url("classes/view_assignments/" . $class->ID) ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_554") ?></a>

</div>

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"> <?php echo lang("ctn_555") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag) ) : ?>
<input type="button" class="btn btn-primary btn-sm" value="<?php echo lang("ctn_548") ?>" data-toggle="modal" data-target="#studyModal" />
<?php endif; ?>

</div>
</div>

<table class="table table-bordered table-striped table-hover">
<tr class="table-header"><td><?php echo lang("ctn_556") ?></td><td><?php echo lang("ctn_557") ?></td><td><?php echo lang("ctn_107") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
<?php foreach($files->result() as $r) : ?>
<tr><td><?php echo $r->file_name ?></td><td><?php echo $r->file_type ?></td><td><?php echo $r->file_size ?></td><td><a href="<?php echo site_url("files/view/" . $r->fileid) ?>" class="btn btn-primary btn-xs"><?php echo lang("ctn_552") ?></a> <a class="btn btn-info btn-xs" href="<?php echo base_url() . $this->settings->info->upload_path_relative. '/'. $r->upload_file_name ?>" download><span class="glyphicon glyphicon-save"></span></a> <a href="<?php echo site_url("classes/delete_class_file/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs"><?php echo lang("ctn_558") ?></a></td></tr>
<?php endforeach; ?>
</table>


</div>

</div>
<div class="col-md-4">

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"> <?php echo lang("ctn_559") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag) ) : ?>
<input type="button" class="btn btn-primary btn-sm" value="<?php echo lang("ctn_548") ?>" data-toggle="modal" data-target="#myModal" />
<?php endif; ?>

</div>
</div>

<div class="table-responsive">
<table id="teacher-table" class="table table-striped table-hover table-bordered">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>


</div>

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"><?php echo lang("ctn_560") ?></div>
    <div class="db-header-extra form-inline"> 

  

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $this->user->info->ID == $teacher_flag)) : ?>
<input type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addModal" value="<?php echo lang("ctn_548") ?>">
<?php endif; ?>

</div>
</div>

<?php foreach($announcements->result() as $r) : ?>
<h4><?php echo $r->title ?></h4>
<?php
$desired_width = 100; 
$str = wordwrap($r->body, $desired_width, "***"); 
$str = explode("***",$str);
?>
<?php echo $str[0] ?> ... <a href="<?php echo site_url("classes/view_announcement/" . $r->ID) ?>"><?php echo lang("ctn_561") ?></a>
<p class="small-text"><?php echo date($this->settings->info->date_format, $r->timestamp) ?></p>
<hr>
<?php endforeach; ?>

<a href="<?php echo site_url("classes/view_announcements/" . $class->ID) ?>" class="btn btn-primary btn-sm form-control"><?php echo lang("ctn_562") ?></a>

</div>

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"><?php echo lang("ctn_563") ?></div>
    <div class="db-header-extra form-inline"> 

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $this->user->info->ID == $teacher_flag)) : ?>
<input type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addBooksModal" value="<?php echo lang("ctn_548") ?>">
<?php endif; ?>

</div>
</div>

<?php foreach($books->result() as $r) : ?>
<div class="media">
  <div class="media-left">
    <a href="#">
      <img class="media-object" src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $r->image ?>" width="40" height="40">
    </a>
  </div>
  <div class="media-body">
    <h4 class="media-heading"><?php echo $r->title ?></h4>
    <a href="<?php echo site_url("library/view/" . $r->bookid) ?>" class="btn btn-info btn-xs"><?php echo lang("ctn_552") ?></a>
    <?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $this->user->info->ID == $teacher_flag)) : ?>
      <a href="<?php echo site_url("classes/delete_book/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs"><?php echo lang("ctn_558") ?></a>
    <?php endif; ?>

  </div>
</div>
<?php endforeach; ?>


</div>

</div>
</div>

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_564") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_assignment/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_11") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="title">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_509") ?></label>
                    <div class="col-md-8">
                        <textarea name="assignment" id="assign-area"></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_565") ?></label>
                    <div class="col-md-8">
                        <select name="type" class="form-control" id="type_change">
                        <option value="0"><?php echo lang("ctn_550") ?></option>
                        <option value="1"><?php echo lang("ctn_551") ?></option>
                        </select>
                    </div>
            </div>
            <div class="form-group ui-front">
                      <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_510") ?></label>
                      <div class="col-md-8">
                          <input type="text" name="due_date" class="form-control datepicker" value="">
                      </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_852") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="weighting">
                        <span class="help-block"><?php echo lang("ctn_853") ?></span>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_854") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="max_mark">
                        <span class="help-block"><?php echo lang("ctn_855") ?></span>
                    </div>
            </div>
            <div id="type_one" style="display: none;">
              <div class="form-group">
                      <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_566") ?></label>
                      <div class="col-md-8">
                          <input type="checkbox" name="generate_entries" class="form-control" value="1">
                          <span class="help-block"><?php echo lang("ctn_567") ?></span>
                      </div>
              </div>
            </div>
            <div id="type_zero">
              <div class="form-group">
                      <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_511") ?></label>
                      <div class="col-md-8">
                          <input type="text" name="file_types" class="form-control" value="<?php echo $this->settings->info->file_types ?>">
                      </div>
              </div>
              <div class="form-group">
                      <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_512") ?></label>
                      <div class="col-md-8">
                          <input type="checkbox" name="reupload" value="1" checked>
                          <span class="help-block"><?php echo lang("ctn_513") ?></span>
                      </div>
              </div>
              <div class="form-group">
                      <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_514") ?></label>
                      <div class="col-md-8">
                          <input type="checkbox" name="deny_upload" value="1" checked>
                          <span class="help-block"><?php echo lang("ctn_515") ?></span>
                      </div>
              </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_564") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_568") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_class_announcement/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_569") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="title">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_449") ?></label>
                    <div class="col-md-8 ui-front">
                        <textarea name="announcement" id="ann-area"></textarea>
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_451") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addBooksModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_570") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_class_books/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_571") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="title" id="book-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_570") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="studyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_572") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_class_file/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_573") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" id="file-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_572") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_574") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_teacher/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_25") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" value="" id="username-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_574") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<script type="text/javascript">
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy"
    });
</script>
<script type="text/javascript">
$(document).ready(function() {


   /* Get list of usernames */
  $('#book-search').autocomplete({
    delay : 300,
    minLength: 2,
    source: function (request, response) {
         $.ajax({
             type: "GET",
             url: global_base_url + "library/get_books",
             data: {
                query : request.term
             },
             dataType: 'JSON',
             success: function (msg) {
                 response(msg);
             }
         });
      }
  });

   /* Get list of usernames */
  $('#file-search').autocomplete({
    delay : 300,
    minLength: 2,
    source: function (request, response) {
         $.ajax({
             type: "GET",
             url: global_base_url + "files/get_files",
             data: {
                query : request.term
             },
             dataType: 'JSON',
             success: function (msg) {
                 response(msg);
             }
         });
      }
  });

  });
</script>
<script type="text/javascript">
$(document).ready(function() {

  $('#type_change').on("change", function() {
    var val = $('#type_change').val();
    if(val == 0) {
      $('#type_zero').fadeIn(10);
      $('#type_one').fadeOut(10);
    } else if(val == 1) {
      $('#type_zero').fadeOut(10);
      $('#type_one').fadeIn(10);
    }
  });

  if($('#ann-area').length > 0) {
    CKEDITOR.replace('ann-area', { height: '150'});
  }
  if($('#assign-area').length > 0) {
    CKEDITOR.replace('assign-area', { height: '150'});
  }

   /* Get list of usernames */
  $('#username-search').autocomplete({
    delay : 300,
    minLength: 2,
    source: function (request, response) {
         $.ajax({
             type: "GET",
             url: global_base_url + "subjects/get_teachers",
             data: {
                query : request.term
             },
             dataType: 'JSON',
             success: function (msg) {
                 response(msg);
             }
         });
      }
  });

  });
</script>

<script type="text/javascript">
$(document).ready(function() {

   var st = $('#search_type').val();
    var table = $('#teacher-table').DataTable({
        "dom" : "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5 small-text'><'col-sm-7 small-text'p>>",
      "processing": false,
        "pagingType" : "numbers",
        "pageLength" : 15,
        "serverSide": true,
        "orderMulti": false,
        "order": [
        	[0, "desc"]
        ],
        "columns": [
        null,
        { "orderable" : false }
    ],
        "ajax": {
            url : "<?php echo site_url("classes/teacher_page/" . $class->ID) ?>",
            type : 'GET',
            data : function ( d ) {
                d.search_type = $('#search_type').val();
            }
        },
        "drawCallback": function(settings, json) {
        $('[data-toggle="tooltip"]').tooltip();
      }
    });
    $('#form-search-input').on('keyup change', function () {
    table.search(this.value).draw();
});

} );
function change_search(search) 
    {
      var options = [
        "search-like", 
        "search-exact",
        "user-exact",
      ];
      set_search_icon(options[search], options);
        $('#search_type').val(search);
        $( "#form-search-input" ).trigger( "change" );
    }

function set_search_icon(icon, options) 
    {
      for(var i = 0; i<options.length;i++) {
        if(options[i] == icon) {
          $('#' + icon).fadeIn(10);
        } else {
          $('#' + options[i]).fadeOut(10);
        }
      }
    }
</script>