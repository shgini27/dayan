<div class="white-area-content">

<div class="db-header clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-piggy-bank"></span> <?php echo lang("ctn_606") ?></div>
    <div class="db-header-extra"> <a href="<?php echo site_url("finance/add_finance") ?>" class="btn btn-primary btn-sm"><?php echo lang("ctn_607") ?></a>
</div>
</div>

<div class="panel panel-default">
<div class="panel-body">
<?php echo form_open(site_url("finance/add_finance_pro"), array("class" => "form-horizontal")) ?>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_11") ?></label>
        <div class="col-md-8 ui-front">
            <input type="text" class="form-control" name="title" value="">
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_608") ?></label>
        <div class="col-md-8">
            <textarea name="notes" id="notes"></textarea>
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_477") ?></label>
        <div class="col-md-8">
            <select name="catid" class="form-control">
            <?php foreach($categories->result() as $r) : ?>
            	<option value="<?php echo $r->ID ?>"><?php echo $r->name ?></option>
            <?php endforeach; ?>
            </select>
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_292") ?></label>
        <div class="col-md-8">
            <input type="text" name="amount" class="form-control" value="0.00">
        </div>
</div>
<div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_293") ?></label>
        <div class="col-md-8">
            <input type="text" name="date" class="form-control datepicker">
        </div>
</div>
<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_607") ?>">
<?php echo form_close() ?>
</div>
</div>


</div>

<script type="text/javascript">
$(document).ready(function() {
CKEDITOR.replace('notes', { height: '100'});
});
</script>