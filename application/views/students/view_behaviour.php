<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_481") ?></div>
    <div class="db-header-extra form-inline"> 

<a href="<?php echo site_url("students/view/" . $student->ID) ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_456") ?></a> <a href="<?php echo site_url("students/view_classes/" . $student->ID) ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_471") ?></a> <a href="<?php echo site_url("students/view_attendance/" . $student->ID) ?>" class="btn btn-success btn-sm"><?php echo lang("ctn_494") ?></a> <a href="<?php echo site_url("students/view_behaviour/" . $student->ID) ?>" class="btn btn-danger btn-sm"><?php echo lang("ctn_455") ?></a> <a href="<?php echo site_url("students/view_report/" . $student->ID) ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_845") ?></a>

</div>
</div>


</div>

<div class="row">
<div class="col-md-3">

<div class="white-area-content content-separator align-center">
<p><img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $student->avatar ?>"></p>
<p><?php echo $student->first_name ?> <?php echo $student->last_name ?></p>
<p class="small-text">@<?php echo $student->username ?></p>
</div>

</div>
<div class="col-md-9">

<div class="white-area-content content-separator">

<div class="db-header clearfix">
    <div class="page-header-title"><?php echo $student->first_name ?> <?php echo $student->last_name ?>'s <?php echo lang("ctn_455") ?></div>
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
          <li><a href="#" onclick="change_search(2)"><span class="glyphicon glyphicon-ok no-display" id="name-exact"></span> <?php echo lang("ctn_81") ?></a></li>
          <li><a href="#" onclick="change_search(3)"><span class="glyphicon glyphicon-ok no-display" id="rule-exact"></span> <?php echo lang("ctn_458") ?></a></li>
          <li><a href="#" onclick="change_search(4)"><span class="glyphicon glyphicon-ok no-display" id="name2-exact"></span> <?php echo lang("ctn_467") ?></a></li>
        </ul>
      </div><!-- /btn-group -->
</div>
</div>

</div>

</div>


<div class="table-responsive">
<table id="rules-table" class="table table-bordered table-striped table-hover">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_468") ?></td><td><?php echo lang("ctn_458") ?></td><td><?php echo lang("ctn_293") ?></td><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>


</div>

</div>
</div>


<script type="text/javascript">
$(document).ready(function() {
   var st = $('#search_type').val();
    var table = $('#rules-table').DataTable({
        "dom" : "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      "processing": false,
        "pagingType" : "full_numbers",
        "pageLength" : 15,
        "serverSide": true,
        "orderMulti": false,
        "order": [ 
        ],
        "columns": [
        null,
        null,
        null,
        null,
        { "orderable": false }
    ],
        "ajax": {
            url : "<?php echo site_url("students/view_behaviour_page/" . $student->ID) ?>",
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
        "name-exact",
        "rule-exact",
        "name2-exact"
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