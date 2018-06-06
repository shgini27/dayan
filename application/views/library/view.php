<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-book"></span> <?php echo lang("ctn_731") ?></div>
    <div class="db-header-extra form-inline"> 


</div>
</div>


<div class="media">
  <div class="media-left">
    <p><img src="<?php echo base_url () . $this->settings->info->upload_path_relative ?>/<?php echo $book->image ?>" width="200" height="200"></p>
    <p class="small-text"><?php echo lang("ctn_733") ?>: <?php echo $book->author ?></p>
    <?php if($book->reserve && $book->stock > 0) : ?>
    <p><a href="<?php echo site_url("library/reserve/" . $book->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-primary"><?php echo lang("ctn_743") ?></a></p>
  <?php endif; ?>
  <?php if($book->stock > 0) : ?>
    <?php if($this->common->has_permissions(array("admin", "library_manager"), $this->user)) : ?>
    <p><input type="button" class="btn btn-warning" value="<?php echo lang("ctn_739") ?>" data-toggle="modal" data-target="#myModal"></p>
  <?php endif; ?>
  <?php endif; ?>
  </div>
  <div class="media-body">
    <h3 class="media-heading"><?php echo $book->title ?></h3>
    <?php echo $book->description ?>
    <hr>
    <?php if($book->stock > 0) : ?>
        <h4><?php echo lang("ctn_744") ?>: <?php echo $book->stock ?></h4>
    <?php else : ?>
        <h4><?php echo lang("ctn_745") ?></h4>
    <?php endif; ?>
  </div>
</div>



</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                        <input type="text" class="form-control" name="title" value="<?php echo $book->title ?>" id="book-search">
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