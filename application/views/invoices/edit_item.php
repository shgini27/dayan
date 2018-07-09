<div class="white-area-content">

    <div class="db-header db-header-nomargin clearfix">
        <div class="page-header-title"> <span class="glyphicon glyphicon-credit-card"></span> <?php echo lang("ctn_890") ?></div>
        <div class="db-header-extra">
        </div>
    </div>

    <hr>

    <?php echo form_open(site_url("invoices/edit_item_pro/" . $item->ID), array("class" => "form-horizontal", "id" => "invoice_form")) ?>
    <?php if(intval($item->class_id) > 0): ?>
    <div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_532") ?></label>
        <div class="col-md-8">
            <select id="class_select" name="class_id" class="form-control">
                <option value="" class="text-center"><?php echo lang('ctn_1029'); ?></option>
                <?php foreach ($classes->result() as $r) : ?>
                    <?php
                    if ($r->class_days === 'odd') {
                        $day = '1,3,5';
                    } elseif ($r->class_days === 'even') {
                        $day = '2,4,6';
                    } else {
                        $day = '1-6';
                    }
                    ?>
                    <?php if($item->class_id === $r->ID): ?>
                    <option value="<?php echo $r->ID ?>" selected><?php echo $r->name . " / " . $day . " / " . substr($r->start_hour, 0, 5) . " - " . substr($r->end_hour, 0, 5) ?></option>
                    <?php else: ?>
                    <option value="<?php echo $r->ID ?>"><?php echo $r->name . " / " . $day . " / " . substr($r->start_hour, 0, 5) . " - " . substr($r->end_hour, 0, 5) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php else: ?>
    <div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_81") ?></label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="name" value="<?php echo $item->name ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_271") ?></label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="description" value="<?php echo $item->description ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_652") ?></label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="quantity" value="<?php echo $item->quantity ?>">
        </div>
    </div>
    <?php endif; ?>
    <div class="form-group">
        <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_893") ?></label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="price" value="<?php echo $item->price ?>">
        </div>
    </div>
    <input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_13") ?>">
    <?php echo form_close() ?>

</div>