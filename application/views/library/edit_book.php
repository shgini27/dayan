<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-book"></span> <?php echo lang("ctn_731") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open_multipart(site_url("library/edit_book_pro/" . $book->ID), array("class" => "form-horizontal")) ?>
       <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_11") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="title" value="<?php echo $book->title ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
                    <div class="col-md-8">
                        <textarea name="description" id="desc-area"><?php echo $book->description ?></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_735") ?></label>
                    <div class="col-md-8">
                        <img src="<?php echo base_url() ?><?php echo $this->settings->info->upload_path_relative ?>/<?php echo $book->image ?>">
                        <input type="file" name="userfile" class="form-control" value="Upload ...">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_733") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="author" class="form-control" value="<?php echo $book->author ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_476") ?></label>
                    <div class="col-md-8">
                        <select name="subjectid" class="form-control">
                        <option value="0"><?php echo lang("ctn_46") ?></option>
                        <?php foreach($subjects->result() as $r) : ?>
                          <option value="<?php echo $r->ID ?>" <?php if($r->ID == $book->subjectid) echo "selected" ?>><?php echo $r->name ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_736") ?></label>
                    <div class="col-md-8">
                        <input type="text" name="stock" class="form-control" value="<?php echo $book->stock ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_737") ?></label>
                    <div class="col-md-8">
                        <input type="checkbox" name="reserve" class="form-control" value="1" <?php if($book->reserve) echo "checked" ?>>
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_738") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>



</div>
<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('desc-area', { height: '150'});
});
</script>