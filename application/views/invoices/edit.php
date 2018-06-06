<script src="<?php echo base_url();?>scripts/custom/get_usernames.js"></script>
<div class="white-area-content">

<div class="db-header db-header-nomargin clearfix">
    <div class="page-header-title"> <span class="glyphicon glyphicon-credit-card"></span> <?php echo lang("ctn_614") ?></div>
    <div class="db-header-extra"> <a href="<?php echo site_url("invoices/add") ?>" class="btn btn-success btn-sm"><?php echo lang("ctn_643") ?></a> <a href="<?php echo site_url("invoices/view/" . $invoice->ID . "/" . $invoice->hash) ?>" class="btn btn-info btn-sm"><?php echo lang("ctn_552") ?></a>
</div>
</div>

</div>

<?php echo form_open(site_url("invoices/edit_invoice_pro/" . $invoice->ID), array("id" => "invoice_form")) ?>
<div class="white-area-content content-separator clearfix" id="invoice-area">
<ul class="nav nav-tabs invoice-heading" role="tablist">
    <li role="presentation" class="active invoice-tab"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang("ctn_615") ?></a></li>
    <li role="presentation" class=" invoice-tab"><a href="#notes" aria-controls="notes" role="tab" data-toggle="tab"><?php echo lang("ctn_495") ?></a></li>
    <li role="presentation" class=" invoice-tab"><a href="#themes" aria-controls="themes" role="tab" data-toggle="tab"><?php echo lang("ctn_873") ?></a></li>
    <li role="presentation" class=" invoice-tab"><a href="#payments" id="payments-tab" aria-controls="payments" role="tab" data-toggle="tab"><?php echo lang("ctn_897") ?></a>
    </li>
    <li role="presentation" class=" invoice-tab"><a href="#tax" id="tax-tab" aria-controls="payments" role="tab" data-toggle="tab"><?php echo lang("ctn_661") ?></a>
    </li>
  </ul>

  <div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="home">

<div class="col-md-6">
	<div class="form-group">
	    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_11") ?></label>
	    <input type="text" class="form-control input-sm" name="title" value="<?php echo $invoice->title ?>" placeholder="<?php echo lang("ctn_693") ?> ...">
	  </div>
	<div class="row">
		<div class="col-md-8">
		<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_644") ?></label>
		    <input type="text" class="form-control input-sm" name="invoice_id" value="<?php echo $invoice->invoice_id ?>">
		  </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_617") ?></label>
		    <select name="status" class="form-control input-sm">
	            <option value="1"><?php echo lang("ctn_619") ?></option>
	            <option value="2" <?php if($invoice->status == 2) echo "selected" ?>><?php echo lang("ctn_620") ?></option>
	            <option value="3" <?php if($invoice->status == 3) echo "selected" ?>><?php echo lang("ctn_621") ?></option>
	            <option value="4" <?php if($invoice->status == 4) echo "selected" ?>><?php echo lang("ctn_874") ?></option>
	            </select>
		  </div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
		<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_510") ?></label>
		    <input type="text" name="due_date" class="form-control datepicker input-sm" value="<?php echo date($this->settings->info->date_picker_format,$invoice->due_date) ?>">
		  </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_898") ?></label>
		    <input type="checkbox" name="remind" class="form-control input-sm" value="1">
		  </div>
		</div>
	</div>
	
</div>
<div class="col-md-6">
	<div class="form-group" id="client-area">
	    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_671") ?></label>
	    <select name="clientid" id="client" class="form-control input-sm">
                <option value="0"><?php echo lang("ctn_875") ?> ...</option>
                <option value="-1" <?php if(!empty($invoice->guest_name) && !empty($invoice->guest_email)) echo "selected" ?>><?php echo lang("ctn_876") ?></option>
                <option value="-2" <?php if(isset($invoice->client_username)) echo "selected" ?>><?php echo lang("ctn_493") ?> ...</option>
        </select>
	</div>
	<div class="row" id="client-guest" style="<?php if(empty($invoice->guest_name) && empty($invoice->guest_email)) : ?>display: none<?php endif; ?>">
		<div class="col-md-6">
			<div class="form-group">
				<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_879") ?></label>
				<input type="text" name="guest_name" class="form-control input-sm" placeholder="<?php echo lang("ctn_877") ?>" value="<?php echo $invoice->guest_name ?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_880") ?></label>
				<input type="text" name="guest_email" class="form-control input-sm" placeholder="<?php echo lang("ctn_878") ?>" value="<?php echo $invoice->guest_email ?>">
			</div>
		</div>
	</div>
	<div class="form-group" id="username-area" style="<?php if(!isset($invoice->client_username)) : ?>display: none<?php endif; ?>">
	    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_671") ?></label>
	    <input type="text" name="client_username" class="form-control input-sm" id="username-search" placeholder="<?php echo lang("ctn_493") ?>" value="<?php if(isset($invoice->client_username)) echo $invoice->client_username ?>">
	</div>
	<div class="row">
		<div class="col-md-8">
		<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_649") ?></label>
		    <select name="paying_accountid" class="form-control input-sm">
		    	<option value="0"><?php echo lang("ctn_356") ?> ...</option>
		    	<option value="-1" <?php if($invoice->pa_userid > 0) echo "selected" ?>><?php echo lang("ctn_881") ?></option>
            <?php foreach($accounts->result() as $r) : ?>
                <option value="<?php echo $r->ID ?>" <?php if($r->ID == $invoice->paying_accountid) echo "selected" ?>><?php echo $r->name ?></option>
            <?php endforeach; ?>
            </select>
		  </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_646") ?></label>
		     <select name="currencyid" class="form-control input-sm">
            <?php foreach($currencies->result() as $r) : ?>
                <option value="<?php echo $r->ID ?>" <?php if($r->ID == $invoice->currencyid) echo "selected" ?>><?php echo $r->symbol ?> - <?php echo $r->name ?></option>
            <?php endforeach; ?>
            </select>
		  </div>
		</div>
	</div>
</div>

</div>

<div role="tabpanel" class="tab-pane" id="notes">

<div class="row">
<div class="col-md-6">
	<div class="form-group">
		<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_495") ?></label>
	<textarea name="notes" class="form-control" placeholder="<?php echo lang("ctn_882") ?> ..." rows="4"><?php echo $invoice->notes ?></textarea>
	</div>
</div>
<div class="col-md-6">
	<div class="form-group">
		<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_883") ?></label>
	<textarea name="term_notes" class="form-control" placeholder="<?php echo lang("ctn_884") ?> ..." rows="4"><?php echo $invoice->term_notes ?></textarea>
	</div>
</div>
<div class="col-md-6">
	<div class="form-group">
		<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_885") ?></label>
	<textarea name="hidden_notes" class="form-control" placeholder="<?php echo lang("ctn_886") ?>" rows="4"><?php echo $invoice->hidden_notes ?></textarea>
	</div>
</div>
</div>

</div>

<div role="tabpanel" class="tab-pane" id="themes">

<input type="hidden" name="themeid" id="themeid" value="<?php echo $invoice->themeid ?>">
<?php $default_theme_id = 1; ?>
<?php foreach($themes->result() as $r) : ?>
<div class="invoice-theme <?php if($invoice->themeid == $r->ID) : ?>invoice-theme-active<?php endif; ?>" id="theme-id-<?php echo $r->ID ?>">
<img src="<?php echo base_url() ?>images/invoices/<?php echo $r->image ?>" width="150" onclick="set_invoice_theme(<?php echo $r->ID ?>)">
<p><?php echo $r->name ?></p>
</div>
<?php endforeach; ?>

</div>


<div role="tabpanel" class="tab-pane" id="payments">

<p><input type="button" class="btn btn-info btn-sm" value="Add Payment"  data-toggle="modal" data-target="#addPaymentModal"></p>

<table class="table table-bordered table-hover table-striped">
<thead>
<tr class="table-header"><td><?php echo lang("ctn_292") ?></td><td><?php echo lang("ctn_378") ?></td><td><?php echo lang("ctn_495") ?></td><td><?php echo lang("ctn_293") ?></td><td><?php echo lang("ctn_902") ?></td><td><?php echo lang("ctn_52") ?></td></tr>
</thead>
<tbody>
	<?php foreach($payments->result() as $r) : ?>
		<?php $types = array(0 => "PayPal", 1 => "Stripe", 2 => "2Checkout", 3 => lang("ctn_899"), 4 => lang("ctn_900"), 5 => lang("ctn_901")); ?>
	<?php
	if(isset($types[$r->processor])) {
		$type = $types[$r->processor];
	} else {
		$type = $r->processor;
	}
	?>
<tr><td><?php echo $invoice->symbol ?><?php echo $r->amount ?></td><td><?php echo $type ?></td><td><?php echo $r->notes ?></td><td><?php echo date($this->settings->info->date_format, $r->timestamp) ?></td><td><?php echo $r->email ?></td><td><button type="button" class="btn btn-warning btn-xs" onclick="edit_invoice_payment(<?php echo $r->ID ?>)"><span class="glyphicon glyphicon-edit"></span></button> <a href="<?php echo site_url("invoices/delete_invoice_payment/" . $r->ID . "/" . $this->security->get_csrf_hash()) ?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a></td></tr>
	<?php endforeach; ?>
</tbody>
</table>

</div>

<div role="tabpanel" class="tab-pane" id="tax">
	
<div class="row">
<div class="col-md-12">

	<div class="row">
		<div class="col-md-8">
		<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_887") ?></label>
		    <input type="text" name="tax_name_1" id="tax_name_1" class="form-control" value="<?php echo $invoice->tax_name_1 ?>">
		  </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_888") ?></label>
		    <input type="text" name="tax_rate_1" id="tax_rate_1" class="form-control" placeholder="<?php echo lang("ctn_656") ?>" value="<?php echo $invoice->tax_rate_1 ?>">
		  </div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
		<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_889") ?></label>
		    <input type="text" name="tax_name_2" id="tax_name_2" class="form-control" value="<?php echo $invoice->tax_name_2 ?>">
		  </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		    <label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_888") ?></label>
		    <input type="text" name="tax_rate_2" id="tax_rate_2" class="form-control" placeholder="<?php echo lang("ctn_656") ?>" value="<?php echo $invoice->tax_rate_2 ?>">
		  </div>
		</div>
	</div>
</div>
</div>

</div>

</div>



</div>

<div class="white-area-content content-separator clearfix" id="invoice-items-area">
	<h3 class="invoice-heading"><?php echo lang("ctn_890") ?></h3>

	<div id="invoice-items">
		<?php $item_count = 0; ?>
		<?php foreach($items->result() as $item) : ?>
			<?php $item_count++; ?>
			<div class="invoice-item small-text" id="invoice-item-<?php echo $item_count ?>">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
						<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_891") ?></label>
						<p><input type="text" name="item_name_<?php echo $item_count ?>" id="item_name_<?php echo $item_count ?>" class="form-control input-sm" placeholder="<?php echo lang("ctn_651") ?>" value="<?php echo $item->name ?>"></p>
						<p><input type="text" name="item_desc_<?php echo $item_count ?>" id="item_desc_<?php echo $item_count ?>" class="form-control input-sm" placeholder="<?php echo lang("ctn_892") ?>" value="<?php echo $item->description ?>"></p>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_893") ?></label>
							<input type="text" name="item_price_<?php echo $item_count ?>" id="item_price_<?php echo $item_count ?>" class="form-control itemchange input-sm" placeholder="0.00" value="<?php echo $item->amount ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_652") ?></label>
							<input type="text" name="item_quantity_<?php echo $item_count ?>" id="item_quantity_<?php echo $item_count ?>" class="form-control itemchange input-sm" placeholder="0.00" value="<?php echo $item->quantity ?>">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_653") ?></label>
							<p id="item_total_<?php echo $item_count ?>">0.00</p>
							<?php if($this->common->has_permissions(array("admin", "project_admin", "invoice_manage"), $this->user)) : ?><p><input type="checkbox" name="save_<?php echo $item_count ?>" id="save_<?php echo $item_count ?>" value="1"> <?php echo lang("ctn_894") ?> </p><?php endif; ?>
							<p><button type="button" class="btn btn-danger btn-xs" onclick="remove_item(<?php echo $item_count ?>)"><span class="glyphicon glyphicon-trash"></span></button></p>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		
	</div>

	<div id="item_error_count"></div>

	<input type="hidden" name="items_count" id="items_count" value="<?php echo $item_count ?>">

	<hr>

	<p><button class="btn btn-default btn-sm" id="add_item"><span class="glyphicon glyphicon-plus"></span></button> <?php if($this->common->has_permissions(array("admin", "invoice_manage"), $this->user)) : ?><button id="add_itemdb" class="btn btn-info btn-sm"><?php echo lang("ctn_895") ?></button><?php endif; ?>
</div>

<div class="white-area-content content-separator clearfix">
	<h3 class="invoice-heading"><?php echo lang("ctn_659") ?></h3>


<div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_659") ?></label>
                    <div class="col-md-8">
                        <table class="table table-bordered table-hover">
                        <tr><td><strong><?php echo lang("ctn_660") ?></strong></td><td><div id="sub_total">0.00</div></td></tr>
                        <tr><td><strong><div id="tax_name_1_area"><?php echo lang("ctn_887") ?></strong></div></td><td><div id="tax_amount_1">0%</div><div id="tax_total_amount_1">0.00</div></td></tr>
                        <tr><td><strong><div id="tax_name_2_area"><?php echo lang("ctn_889") ?></div></strong></td><td><div id="tax_amount_2">0%</div><div id="tax_total_amount_2">0.00</div></td></tr>
                        <tr><td><strong><?php echo lang("ctn_653") ?></strong></td><td><div id="total_payment">0.00</div></td></tr>
                     	<?php if($payments_total > 0) : ?>
	                        <tr><td><strong><?php echo lang("ctn_903") ?></strong></td><td><div id="total_paid"></div></td></tr>
	                        <tr><td><strong><?php echo lang("ctn_904") ?></strong></td><td><div id="total_due"></div></td></tr>
	                    <?php endif; ?>
                        </table>
                    </div>
            </div>

</div>


<hr>

<input type="submit" class="btn btn-primary form-control" value="<?php echo lang("ctn_13") ?>" id="update-button">
<?php echo form_close() ?>

</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="add_item_db_area">
      
    </div>
  </div>
</div>


<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-folder-open"></span> <?php echo lang("ctn_905") ?></h4>
      </div>
      <div class="modal-body">
         <?php echo form_open(site_url("invoices/add_invoice_payment/" . $invoice->ID), array("class" => "form-horizontal")) ?>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_292") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="amount" placeholder="0.00">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_906") ?></label>
                    <div class="col-md-8 ui-front">
                        <select name="type" class="form-control">
                        	<option value="0">PayPal</option>
                        	<option value="1">Stripe</option>
                        	<option value="2">2Checkout</option>
                        	<option value="3"><?php echo lang("ctn_899") ?></option>
                        	<option value="4"><?php echo lang("ctn_900") ?></option>
                        	<option value="5"><?php echo lang("ctn_901") ?></option>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_907") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control" name="email">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_293") ?></label>
                    <div class="col-md-8 ui-front">
                        <input type="text" class="form-control datepicker" name="date">
                    </div>
            </div>
            <div class="form-group">
                    <label for="p-in" class="col-md-4 label-heading"><?php echo lang("ctn_495") ?></label>
                    <div class="col-md-8 ui-front">
                        <textarea name="notes" rows="4" class="form-control"></textarea>
                    </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang("ctn_60") ?></button>
        <input type="submit" class="btn btn-primary" value="<?php echo lang("ctn_905") ?>">
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="edit-invoice-modal">
     
      
    </div>
  </div>
</div>
<script type="text/javascript">
var payments_total = parseFloat(<?php echo $payments_total ?>);
$(document).ready(function() {

	$('#total_paid').text("" + payments_total.toFixed(2));

	<?php if(isset($_GET['tab']) && $_GET['tab'] == 4) : ?>
	$('#payments-tab').tab('show');
	<?php endif; ?>

	var projectid = 0;
	calculate_total();

	$('body').on("change", ".itemchange", function() {
		calculate_total();
	});

	$('body').on("focus", "#invoice-items input", function() {
		clearerrors();
	});

	$('body').on("click", "#add_item_to_invoice_items", function() {
		var itemid = $('#item-itemdb').val();
		// Get item data
		$.ajax({
			url: global_base_url + "invoices/get_itemdb_item/" + itemid,
			type: "GET",
			dataType: 'json',
			success: function(data) 
			{
				if(data.error) {
					alert(data.error_msg);
					return;
				}
				add_item(data);
			}
		})
	});

	$('#add_itemdb').on("click", function(event) {
		event.preventDefault();
		$.ajax({
			url: global_base_url + "invoices/get_itemdb_items",
			data : {
			},
			dataType: 'json',
			success: function(data) 
			{
				$('#add_item_db_area').html(data.html);
				$('#addModal').modal('show');
			}
		});
	});

	$('#add_item').on("click", function(event) {
		event.preventDefault();
		add_item();
	});

	$('#client-area').on("change","#client", function() {
		var client = $('#client').val();
		if(client == -1) {
			$('#client-guest').fadeIn(100);
			$('#username-area').fadeOut(100);
		} else if(client == -2) {
			$('#username-area').fadeIn(100);
			$('#client-guest').fadeOut(100);
		} else {
			$('#username-area').fadeOut(100);
			$('#client-guest').fadeOut(100);
		}
	});

	$('#tax_name_1').change(function() {
		$('#tax_name_1_area').text($('#tax_name_1').val());
	});
	$('#tax_name_2').change(function() {
		$('#tax_name_2_area').text($('#tax_name_2').val());
	});

	$('#tax_rate_1').change(function() {
		$('#tax_amount_1').text($('#tax_rate_1').val() + "%");
		$('#tax_name_1_area').text($('#tax_name_1').val());
		calculate_total();
	});

	$('#tax_rate_2').change(function() {
		$('#tax_amount_2').text($('#tax_rate_2').val() + "%");
		$('#tax_name_2_area').text($('#tax_name_2').val());
		calculate_total();
	});

	var form = "invoice_form";
	$('#'+form + ' input').on("focus", function(e) {
      clearerrors();
    });

	$('#'+form).on("submit", function(e) {

      e.preventDefault();
      // Ajax check
      var data = $(this).serialize();
      $.ajax({
        url : global_base_url + "invoices/add_check",
        type : 'POST',
        data : {
          formData : data,
          '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash() ?>'
        },
        dataType: 'JSON',
        success: function(data) {
          if(data.error) {
            $('#'+form).prepend('<div class="form-error">'+data.error_msg+'</div>');
          }
          if(data.success) {
            // allow form submit
            $('#'+form).unbind('submit').submit();
          }
          if(data.field_errors) {
          	$('#update-button').effect( "shake" );
            var errors = data.fieldErrors;
            for (var property in errors) {
                if (errors.hasOwnProperty(property)) {
                	// Custom handlers for this page
                	if(property == "items_count") {
                		$('#item_error_count').html('<div class="form-error-no-margin">'+errors[property]+'</div>');
                		$('#invoice-items').addClass("errorField");
                	} else {
	                    // Find form name
	                    console.log("Looking for input ... " + property);
	                    var field_name = ' input[name="'+property+'"]';
	                    if(!$(field_name).length) {
	                    	// Check for select
	                    	console.log("looking for select ..." + property);
	                    	var field_name = '#' + form + ' select[name="'+property+'"]';
	                    	if(!$(field_name.length)) {
	                    		// Check for something else?
	                    	}
	                    }
	                    $(field_name).addClass("errorField");
	                    if(errors[property]) {
		                    // Get input group of field
		                    $(field_name).parent().closest('.form-group').after('<div class="form-error-no-margin">'+errors[property]+'</div>');
		                }
		                $('html, body').animate({
						    scrollTop: ($(field_name).offset().top)
						},500);
		            }
                    

                }
            }
          }
        }
      });

      return false;


    });
});

function add_item(data=null) 
{
	var item_name = "";
	var item_desc = "";
	var item_price = 0.00;
	var item_quantity = 0.00;
	if(data instanceof Object) {
		item_name = data.item_name;
		item_desc = data.item_desc;
		item_price = data.item_price;
		item_quantity = data.item_quantity;
	}
	var items_count = $('#items_count').val();
		items_count++;
		$('#items_count').val(items_count);

		var html = '<div class="invoice-item" id="invoice-item-'+items_count+'">'+
			'<div class="row">'+
				'<div class="col-md-4">'+
					'<div class="form-group">'+
					'<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_891") ?></label>'+
					'<p><input type="text" name="item_name_'+items_count+'" id="item_name_'+items_count+'" class="form-control" placeholder="<?php echo lang("ctn_651") ?>" value="'+item_name+'"></p>'+
					'<p><input type="text" name="item_desc_'+items_count+'" id="item_desc_'+items_count+'" class="form-control" placeholder="<?php echo lang("ctn_892") ?>" value="'+item_desc+'"></p>'+
					'</div>'+
				'</div>'+
				'<div class="col-md-3">'+
					'<div class="form-group">'+
					'<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_893") ?></label>'+
					'<input type="text" name="item_price_'+items_count+'" id="item_price_'+items_count+'" class="form-control itemchange" placeholder="0.00" value="'+item_price+'">'+
					'</div>'+
				'</div>'+
				'<div class="col-md-3">'+
					'<div class="form-group">'+
					'<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_652") ?></label>'+
					'<input type="text" name="item_quantity_'+items_count+'" id="item_quantity_'+items_count+'" class="form-control itemchange" placeholder="0.00" value="'+item_quantity+'">'+
					'</div>'+
				'</div>'+
				'<div class="col-md-2">'+
					'<div class="form-group">'+
					'<label for="exampleInputEmail1" class="light-label"><?php echo lang("ctn_653") ?></label>'+
					'<p id="item_total_'+items_count+'">0.00</p>'+
					'<p><input type="checkbox" name="save_'+items_count+'" id="save_'+items_count+'" value="1"> <?php echo lang("ctn_894") ?> </p>'+
					'<p><button type="button" class="btn btn-danger btn-xs" onclick="remove_item('+items_count+')"><span class="glyphicon glyphicon-trash"></span></button></p>'+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div>';
		$('#invoice-items').append(html);
		calculate_total();
}

function remove_item(id) 
{
	$('#invoice-item-' +id).remove();
	var items_count = $('#items_count').val();
	items_count--;
	$('#items_count').val(items_count);
	calculate_total();

}

function calculate_total() 
{
	var total = 0;
	var items_count = $('#items_count').val();
	console.log(items_count);
	for(var i=1;i<=items_count;i++) {
		console.log("Loop: " + i);
		// Get values
		var price = convert_number(parseFloat($('#item_price_'+i).val()));
		var quantity = convert_number(parseFloat($('#item_quantity_'+i).val()));

		console.log(price);
		console.log(quantity);

		// Total
		var item_total = parseFloat(price * quantity);
		total = parseFloat(total + item_total);

		// Display
		item_total = item_total.toFixed(2);
		$('#item_total_' + i).html(item_total);

	}

	var sub_total = total.toFixed(2);
	$('#sub_total').html(sub_total);
	// Tax

	var tax = update_tax($('#tax_rate_1').val(), $('#tax_name_1').val(),1, total);
	var tax2 = update_tax($('#tax_rate_2').val(), $('#tax_name_2').val(),2, total);

	total = parseFloat(tax) + parseFloat(tax2) + total;

	// Total due
	var total_due = total - payments_total;
	$('#total_due').html(total_due.toFixed(2));

	total = total.toFixed(2);
	$('#total_payment').html(total);


	return;
}

function update_tax(tax_rate,name,id, sub_total) {
	var t = sub_total;
	var tax_rate = parseFloat(tax_rate);
	$('#tax_amount_' + id).text(tax_rate + "%");
	$('#tax_name_' + id).text(name);

	if(t> 0 && tax_rate > 0) {
		var bit = parseFloat(t/100*tax_rate);
		bit = bit.toFixed(2);
		$('#tax_total_amount_'+id).text("" + bit);
		return bit;
	}
	return 0;
}

function convert_number(digit) {
	return Number(digit.toString().match(/^\d+(?:\.\d{0,2})?/)).toFixed(2);
}



function clearerrors() 
  {
    console.log("Called");
    $('.form-error').remove();
    $('.form-error-no-margin').remove();
    $('.errorField').removeClass('errorField');
  }


  function set_invoice_theme(themeid) 
{
	$('#themeid').val(themeid);
	$('.invoice-theme-active').removeClass("invoice-theme-active");
	$('#theme-id-'+themeid).addClass("invoice-theme-active");
}

function edit_invoice_payment(id) 
{
	$.ajax({
			url: global_base_url + "invoices/edit_invoice_payment/" + id,
			type: "GET",
			success: function(data) 
			{
				// Add modal data
				$('#edit-invoice-modal').html(data);
				// Show modal
				$('#editInvoiceModal').modal('show');
			}
		});
}
</script>