<script src="<?php echo base_url();?>scripts/custom/get_usernames.js"></script>
<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-credit-card"></span> <?php echo lang("ctn_670") ?></div>
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
          <li><a href="#" onclick="change_search(3)"><span class="glyphicon glyphicon-ok no-display" id="client-exact"></span> <?php echo lang("ctn_671") ?></a></li>
        </ul>
      </div><!-- /btn-group -->
</div>
</div>


    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal"><?php echo lang("ctn_695") ?></button>
</div>
</div>

<div class="table-responsive">
<table id="invoices-table" class="table table-bordered table-striped table-hover">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_11") ?></td><td><?php echo lang("ctn_671") ?></td><td><?php echo lang("ctn_617") ?></td><td><?php echo lang("ctn_673") ?></td><td><?php echo lang("ctn_696") ?></td><td><?php echo lang("ctn_697") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
</tbody>
</table>
</div>

</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_695") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open_multipart(site_url("invoices/add_reoccuring_invoice"), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_698") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="client_username" value="" id="username-search" placeholder="Enter username ...">
                        <span class="help-block"><?php echo lang("ctn_672") ?></span>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_699") ?></label>
                    <div class="col-md-8 ui-front">
                        <select name="templateid" class="form-control">
                        <?php foreach($templates->result() as $r) : ?>
                          <option value="<?php echo $r->ID ?>"><?php echo $r->title ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_673") ?></label>
                    <div class="col-md-5 ui-front">
                        <?php echo lang("ctn_674") ?> <input type="text" name="amount" value="1">
                    </div>
                    <div class="col-md-3">
                        <?php echo lang("ctn_675") ?>
                        <select name="amount_time" class="form-control">
                        <option value="0"><?php echo lang("ctn_676") ?></option>
                        <option value="1"><?php echo lang("ctn_677") ?></option>
                        <option value="2"><?php echo lang("ctn_678") ?></option>
                        <option value="3"><?php echo lang("ctn_679") ?></option>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_680") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="start_date" class="form-control datepicker">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_681") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="end_date" class="form-control datepicker">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_617") ?></label>
                    <div class="col-md-8 ui-front">
                        <select name="status" class="form-control">
                        <option value="0"><?php echo lang("ctn_682") ?></option>
                        <option value="1"><?php echo lang("ctn_683") ?></option>
                        <option value="2"><?php echo lang("ctn_684") ?></option>
                        </select>
                    </div>
            </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_695") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<script tye="text/javascript">
$(document).ready(function() {
    // page is now ready, initialize the calendar...
    var date_last_clicked = null;
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

   var st = $('#search_type').val();
    var table = $('#invoices-table').DataTable({
        "dom" : "B<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      "processing": false,
        "pagingType" : "full_numbers",
        buttons: [
          { "extend": 'copy', "text":'<?php echo lang("ctn_911") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'csv', "text":'<?php echo lang("ctn_912") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'excel', "text":'<?php echo lang("ctn_913") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'pdf', "text":'<?php echo lang("ctn_914") ?>',"className": 'btn btn-default btn-sm' },
          { "extend": 'print', "text":'<?php echo lang("ctn_915") ?>',"className": 'btn btn-default btn-sm' }
        ],
        "pageLength" : 15,
        "serverSide": true,
        "orderMulti": false,
        "order" : [],
        "columns": [
            null,
            { "orderable": false },
            null,
            null,
            null,
            null,
            { "orderable": false }
        ],
        "ajax": {
            url : "<?php echo site_url("invoices/reoccuring_page/") ?>",
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
});


function change_search(search) 
{
      var options = [
        "search-like", 
        "search-exact",
        "title-exact",
        "client-exact"
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