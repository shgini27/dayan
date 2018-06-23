<?php include("class_header.php") ?>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-bell"></span> <?php echo lang("ctn_471") ?></div>
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

<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag) ) : ?>
<input type="button" class="btn btn-success btn-sm" value="<?php echo lang("ctn_504") ?>" data-toggle="modal" data-target="#myModal" />
<?php endif; ?>

</div>
</div>

</div>


<div class="white-area-content content-separator">

<p><?php echo lang("ctn_505") ?> <strong><?php echo $class->name ?></strong></p>


<div class="table-responsive">
<table id="student-table" class="table table-striped table-hover table-bordered">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_24") ?></td><td><?php echo lang("ctn_1019") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>

</div>
<?php if($this->common->has_permissions(array("admin", "class_manager"), $this->user) || ($this->settings->info->teacher_class_manage && $teacher_flag)) : ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_504") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("classes/add_student/" . $class->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_25") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" value="" id="username-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_504") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
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
        null,
        null,
        { "orderable" : false }
    ],
        "ajax": {
            url : "<?php echo site_url("classes/class_student_page/" . $class->ID) ?>",
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