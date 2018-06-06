<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-home"></span> <?php echo lang("ctn_931") ?></div>
    <div class="db-header-extra form-inline">

 <a href="<?php echo site_url("hostel/rooms") ?>" class="btn btn-default btn-sm"><?php echo lang("ctn_931") ?></a> <a href="<?php echo site_url("hostel/hostel_room_types") ?>" class="btn btn-default btn-sm"><?php echo lang("ctn_933") ?></a>
  

</div>
</div>

<?php echo form_open(site_url("hostel/add_hostel_room_type"), array("class" => "form-inline")) ?>
<div class="form-group">
    <input type="text" class="form-control" id="exampleInputName2" name="name" placeholder="<?php echo lang("ctn_934") ?> ...">
  </div>
  <button type="submit" class="btn btn-primary"><?php echo lang("ctn_935") ?></button>
<?php echo form_close() ?>
<hr>


<div class="table-responsive">
<table id="hostel-table" class="table table-bordered table-striped table-hover">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_549") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
  <?php foreach($types->result() as $r) : ?>
    <tr><td><?php echo $r->name ?></td><td><a href="<?php echo site_url("hostel/delete_hostel_room_type/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs"><?php echo lang("ctn_57") ?></a></td></tr>
  <?php endforeach; ?>
</tbody>
</table>
</div>

</div>