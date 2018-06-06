<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_759") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>

</div>

<div class="row">
<div class="col-md-8">

<div class="white-area-content content-separator">
<h3><?php echo $group->name ?></h3>
<?php echo $group->description ?>
</div>

<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"> <?php echo lang("ctn_21") ?></div>
    <div class="db-header-extra form-inline"> 

            <div class="form-group has-feedback no-margin">
<div class="input-group">
<input type="text" class="form-control input-sm" placeholder="<?php echo lang("ctn_336") ?>" id="form-search-input" />
<div class="input-group-btn">
    <input type="hidden" id="search_type" value="0">
        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
<span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
        <ul class="dropdown-menu small-text" style="min-width: 90px !important; left: -90px;">
          <li><a href="#" onclick="change_search(0)"><span class="glyphicon glyphicon-ok" id="search-like"></span> <?php echo lang("ctn_337") ?></a></li>
          <li><a href="#" onclick="change_search(1)"><span class="glyphicon glyphicon-ok no-display" id="search-exact"></span> <?php echo lang("ctn_338") ?></a></li>
          <li><a href="#" onclick="change_search(2)"><span class="glyphicon glyphicon-ok no-display" id="user-exact"></span> <?php echo lang("ctn_25") ?></a></li>
        </ul>
      </div><!-- /btn-group -->
</div>
</div>

<?php if($this->common->has_permissions(array("admin", "student_group_manager"), $this->user) || ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid)) : ?>
<input type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" value="<?php echo lang("ctn_765") ?>">
<?php endif; ?>

</div>
</div>

<div class="table-responsive">
<table id="student-table" class="table table-striped table-hover table-bordered">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>


</div>


</div>
<div class="col-md-4">


<div class="white-area-content content-separator">
<div class="db-header clearfix">
    <div class="page-header-title"><?php echo lang("ctn_560") ?></div>
    <div class="db-header-extra form-inline"> 

  

<?php if($this->common->has_permissions(array("admin", "student_group_manager"), $this->user) || ($this->settings->info->teacher_group_manage && $this->user->info->ID == $group->teacherid)) : ?>
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
<?php echo $str[0] ?> ... <a href="<?php echo site_url("students/view_announcement/" . $r->ID) ?>"><?php echo lang("ctn_561") ?></a>
<p class="small-text"><?php echo date($this->settings->info->date_format, $r->timestamp) ?></p>
<hr>
<?php endforeach; ?>

<a href="<?php echo site_url("students/view_announcements/" . $group->ID) ?>" class="btn btn-primary btn-sm form-control"><?php echo lang("ctn_562") ?></a>

</div>

</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_766") ?>: <?php echo $group->name ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("students/add_student_to_group/" . $group->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_767") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" value="" id="username-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_766") ?>">
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
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_451") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("students/add_group_announcement/" . $group->ID), array("class" => "form-horizontal")) ?>
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
<script type="text/javascript">
$(document).ready(function() {


   /* Get list of usernames */
  $('#username-search').autocomplete({
    delay : 300,
    minLength: 2,
    source: function (request, response) {
         $.ajax({
             type: "GET",
             url: global_base_url + "students/get_students",
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

  CKEDITOR.replace('ann-area', { height: '150'});

   var st = $('#search_type').val();
    var table = $('#student-table').DataTable({
        "dom" : "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      "processing": false,
        "pagingType" : "full_numbers",
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
            url : "<?php echo site_url("students/student_group_page/" . $group->ID) ?>",
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