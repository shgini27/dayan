<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-home"></span> <?php echo lang("ctn_931") ?></div>
    <div class="db-header-extra form-inline">

</div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <?php echo form_open(site_url("hostel/edit_hostel_room_pro/" . $room->ID), array("class" => "form-horizontal")) ?>
      <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_925") ?></label>
                    <div class="col-md-8 ui-front">
                        <select name="hostelid" class="form-control">
                          <?php foreach($hostels->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>" <?php if($room->hostelid == $r->ID) echo "selected" ?>><?php echo $r->name ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="name" value="<?php echo $room->name ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
                    <div class="col-md-8">
                        <textarea name="description" class="form-control"><?php echo $room->description ?></textarea>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_549") ?></label>
                    <div class="col-md-8">
                        <select name="type" class="form-control">
                          <?php foreach($types->result() as $r) : ?>
                            <option value="<?php echo $r->ID ?>" <?php if($r->ID == $room->typeid) echo "selected" ?>><?php echo $r->name ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_930") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="capacity" value="<?php echo $room->capacity ?>">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_932") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="cost" value="<?php echo $room->cost ?>">
                    </div>
            </div>
     <input type="submit" name="s" value="<?php echo lang("ctn_13") ?>" class="btn btn-primary form-control" />
  <?php echo form_close() ?>
  </div>
  </div>


</div>

