<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-user"></span> <?php echo lang("ctn_862") ?></div>
    <div class="db-header-extra form-inline"> 

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
    <div class="page-header-title"> <?php echo $student->username ?><?php echo lang("ctn_866") ?></div>
    <div class="db-header-extra form-inline"> <input type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal" value="<?php echo lang("ctn_868") ?>">
</div>
</div>

<table class="table table-bordered table-striped table-hover">
<tr class="table-header"><td><?php echo lang("ctn_339") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
<?php foreach($children->result() as $r) : ?>
<tr><td><?php echo $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)) ?></td><td><a href="<?php echo site_url("students/view/" . $r->studentid) ?>" class="btn btn-primary btn-xs"><?php echo lang("ctn_552") ?></a> <a href="<?php echo site_url("parents/delete_child/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs" onclick="return confirm(\'<?php echo lang("ctn_317") ?>')" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang("ctn_57") ?>"><span class="glyphicon glyphicon-trash"></span></a></td></tr>
<?php endforeach; ?>
</table>

</div>

<div class="white-area-content content-separator">

<div class="db-header clearfix">
    <div class="page-header-title"> <?php echo $student->username ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>
<div class="form-horizontal">
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_420") ?></label>
                <div class="col-md-9">
                <?php echo $student->address_line_1 ?>
                </div>
</div>
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_421") ?></label>
                <div class="col-md-9">
                <?php echo $student->address_line_2 ?>
                </div>
</div>
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_422") ?></label>
                <div class="col-md-9">
                <?php echo $student->city ?>
                </div>
</div>
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_423") ?></label>
                <div class="col-md-9">
                <?php echo $student->state ?>
                </div>
</div>
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_424") ?></label>
                <div class="col-md-9">
                <?php echo $student->zip ?>
                </div>
</div>
<div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_425") ?></label>
                <div class="col-md-9">
                <?php echo $student->country ?>
                </div>
</div>


<h4><?php echo lang("ctn_346") ?></h4>
<?php foreach($fields->result() as $r) : ?>
            <div class="form-group">
                <label for="name-in" class="col-md-3 label-heading"><?php echo $r->name ?></label>
                <div class="col-md-9">
                    <?php if($r->type == 0) : ?>
                       <?php echo $r->value ?>
                    <?php elseif($r->type == 1) : ?>
                        <?php echo $r->value ?>
                    <?php elseif($r->type == 2) : ?>
                         <?php $options = explode(",", $r->options); ?>
                         <?php $values = array_map('trim', (explode(",", $r->value))); ?>
                        <?php if(count($options) > 0) : ?>
                            <?php foreach($options as $k=>$v) : ?>
                            <?php if(in_array($v,$values)) echo $v ?><br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif($r->type == 3) : ?>
                        <?php $options = explode(",", $r->options); ?>
                        
                        <?php if(count($options) > 0) : ?>
                            <?php foreach($options as $k=>$v) : ?>
                           <?php if($r->value == $v) echo $v ?><br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif($r->type == 4) : ?>
                        <?php $options = explode(",", $r->options); ?>
                        <?php if(count($options) > 0) : ?>
                            <?php foreach($options as $k=>$v) : ?>
                            <?php if($r->value == $v) echo $v ?><br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif($r->type == 5) : ?>
                       <?php echo date($this->settings->info->date_picker_format, $r->value) ?>
                   <?php endif; ?>
                </div>
        </div>
    <?php endforeach; ?>
</div>

</div>

</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-user"></span> <?php echo lang("ctn_867") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("parents/add_child/" . $student->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_456") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="username" value="" id="username-search">
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_868") ?>">
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