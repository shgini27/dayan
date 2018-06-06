<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-education"></span> <?php echo lang("ctn_759") ?></div>
    <div class="db-header-extra form-inline"> 

</div>
</div>



<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("students/edit_student_group_pro/" . $group->ID), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_18") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" value="<?php echo $group->name ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_761") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" value="<?php echo $username ?>" id="username-search">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_762") ?></label>
                    <div class="col-md-8">
                        <textarea name="description" id="desc-area"><?php echo $group->description ?></textarea>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_763") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});

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