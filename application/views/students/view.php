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
    <p><img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $student->avatar ?>" width="160" height="160"></p>
<p><?php echo $student->first_name ?> <?php echo $student->last_name ?></p>
<p class="small-text">@<?php echo $student->username ?></p>
</div>

</div>
<div class="col-md-9">

<div class="white-area-content content-separator">

<div class="db-header clearfix">
    <div class="page-header-title"><?php echo $student->first_name ?> <?php echo $student->last_name ?></div>
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
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_1021") ?></label>
                <div class="col-md-9">
                <?php echo $student->mobile_phone ?>
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
                <label for="name-in" class="col-md-3 label-heading"><?php echo lang("ctn_1022") ?></label>
                <div class="col-md-9">
                <?php echo $student->phone ?>
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
                                <?php if(in_array($v,$values)): ?>
                                    <?php echo $v . "<br />"; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif($r->type == 3) : ?>
                        <?php $options = explode(",", $r->options); ?>
                        
                        <?php if(count($options) > 0) : ?>
                            <?php foreach($options as $k=>$v) : ?>
                                <?php if($r->value == $v): ?>
                                    <?php echo $v . "<br />"; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php elseif($r->type == 4) : ?>
                        <?php $options = explode(",", $r->options); ?>
                        <?php if(count($options) > 0) : ?>
                            <?php foreach($options as $k=>$v) : ?>
                                <?php if($r->value == $v): ?>
                                    <?php echo $v . "<br />"; ?>
                                <?php endif; ?>
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