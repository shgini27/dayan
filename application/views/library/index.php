<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-book"></span> <?php echo lang("ctn_731") ?></div>
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
          <li><a href="#" onclick="change_search(2)"><span class="glyphicon glyphicon-ok no-display" id="title-exact"></span> <?php echo lang("ctn_11") ?></a></li>
          <li><a href="#" onclick="change_search(3)"><span class="glyphicon glyphicon-ok no-display" id="author-exact"></span> <?php echo lang("ctn_733") ?></a></li>
        </ul>
      </div><!-- /btn-group -->
</div>
</div>


<?php if($this->common->has_permissions(array("admin", "library_manager"), $this->user)) : ?>
  <input type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#checkModal" value="<?php echo lang("ctn_739") ?>">
<input type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" value="<?php echo lang("ctn_570") ?>">
<?php endif; ?>

</div>
</div>

<div class="table-responsive">
<table id="student-table" class="table table-striped table-hover table-bordered">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_735") ?></td><td><?php echo lang("ctn_11") ?></td><td><?php echo lang("ctn_733") ?></td><td><?php echo lang("ctn_476") ?></td><td><?php echo lang("ctn_736") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>


</div>

<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_739") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open_multipart(site_url("library/checkout_book"), array("class" => "form-horizontal")) ?>
          <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_571") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="title" placeholder="<?php echo lang("ctn_740") ?> ..." id="book-search">
                    </div>
            </div>
            <div class="form-group ui-front">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_339") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="username" placeholder="<?php echo lang("ctn_493") ?> ..." id="username-search">
                    </div>
            </div>
           
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_739") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo base_url();?>scripts/custom/get_usernames.js"></script>
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

  });
</script>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-send"></span> <?php echo lang("ctn_570") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open_multipart(site_url("library/add_book"), array("class" => "form-horizontal")) ?>
          <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_571") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="title" value="">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
                    <div class="col-md-8">
                        <textarea name="description" id="desc-area"></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_735") ?></label>
                    <div class="col-md-8">
                        <input type="file" name="userfile" class="form-control" value="Upload ...">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_733") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="author" class="form-control">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_476") ?></label>
                    <div class="col-md-8">
                        <select name="subjectid" class="form-control">
                        <option value="0"><?php echo lang("ctn_46") ?></option>
                        <?php foreach($subjects->result() as $r) : ?>
                          <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_736") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="stock" class="form-control" value="1">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_737") ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="reserve" class="form-control" value="1" checked>
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
<script type="text/javascript">
$(document).ready(function() {

  CKEDITOR.replace('desc-area', { height: '150'});

   var st = $('#search_type').val();
    var table = $('#student-table').DataTable({
        "dom" : "B<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      "processing": false,
        "pagingType" : "full_numbers",
        "pageLength" : 15,
        "serverSide": true,
        "orderMulti": false,
        buttons: [
          { "extend": 'copy', "text":'<?php echo lang("ctn_911") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'csv', "text":'<?php echo lang("ctn_912") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'excel', "text":'<?php echo lang("ctn_913") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'pdf', "text":'<?php echo lang("ctn_914") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'print', "text":'<?php echo lang("ctn_915") ?>',"className": 'btn btn-default btn-sm' }
        ],
        "order": [
        ],
        "columns": [
        { "orderable" : false },
        null,
        null,
        null,
        null,
        { "orderable" : false }
    ],
        "ajax": {
            url : "<?php echo site_url("library/book_page") ?>",
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
        "title-exact",
        "author-exact",
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