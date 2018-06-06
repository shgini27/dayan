<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-flag"></span> <?php echo lang("ctn_455") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("behaviour/add_record_pro"), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_456") ?></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="username" id="username-search" placeholder="<?php echo lang("ctn_457") ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_458") ?></label>
                    <div class="col-md-8">
                         <select name="ruleid" class="form-control">
                         <?php foreach($rules->result() as $r) : ?>
                          <option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
                         <?php endforeach; ?>
                         </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_459") ?></label>
                    <div class="col-md-8">
                        <textarea name="incident" id="inci-area"></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_460") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="date" class="form-control datepicker">
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_461") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>




</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('inci-area', { height: '150'});
});
</script>
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