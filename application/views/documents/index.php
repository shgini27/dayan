<style type="text/css">
    body, h3, h4, h5 {
        font-family: "Cambria (Headings)", Times, serif;
    }
    .word-icon {
        font-family:"Helvetica", sans-serif;
        font-size: 24px;
        font-weight: bold;
        background-color: #0054a6;
        color: white;
        padding: 2px 5px;
        vertical-align: middle;
    }
    #logo {
        border-bottom: solid 2px black;
    }
    .mgn-top-4 {
        margin-top: 4%;
    }
    #top-btm-10 {
        margin-top:10%;
        margin-bottom:10%;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="white-area-content">
    <!-- DOCUMENT STYLE STARTS -->
    <div class="db-header clearfix">
        <div class="page-header-title"> 
            <span class="glyphicon glyphicon-paperclip"></span> <?php echo lang("ctn_953") ?>
        </div>
    </div>
    <div class="row" ng-app="">
        <!-- FORM STARTS-->
        <?php echo form_open(site_url("documents/generate_doc"), array("class" => "form-horizontal", "id" => "order_form", "onSubmit" => "return validate(this);")) ?>
        <div class="col-xs-12">
            <div class="form-group">
                <label for="p-in" class="col-md-2 label-heading"><?php echo lang("ctn_956") ?></label>
                <div class="col-md-5 ui-front">
                    <input type="text" class="form-control" ng-model="number" name="number" placeholder="No:1 - G/1">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-2 label-heading"><?php echo lang("ctn_957") ?></label>
                <div class="col-md-5 ui-front">
                    <input type="text" class="form-control" ng-model="order_date" name="order_date" placeholder="28.05.2018">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-2 label-heading"><?php echo lang("ctn_958") ?></label>
                <div class="col-md-5 ui-front">
                    <input type="text" class="form-control" ng-model="date" name="period" placeholder="28.05.2018 - 28.06.2018">
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div id="page-content" class="panel panel-default" style="width:656px">
                <div class="panel-body">
                    <div id="logo" class="col-md-12">
                        <img class="img-responsive pull-right" src="<?php echo base_url(); ?>uploads/dayan.png" alt="Dayan Logo" title="logo" />
                    </div>
                    <div class="col-md-6">
                        <h4 class="pull-left"><b>{{ number}}</b></h4>
                    </div>
                    <div class="col-md-6">
                        <h4 class="pull-right"><b>{{ order_date}}</b></h4>
                    </div>
                    <div class="col-md-12 mgn-top-4">
                        <h3 class="text-center"><b>BUÝRUK</b></h3>
                    </div>
                    <div class="col-md-offset-8 col-md-4 mgn-top-4">
                        <p><b>Täze okuw tapgyryny açmak hakynda</b></p>
                    </div>
                    <div class="col-md-12 mgn-top-4">
                        <p>
                            “Daýan” HK-nyň okuw merkeziniň iňlis, rus dili, suratkeşlik we kompýuter sowatlylygy boýunça 1-nji 
                            tapgyryny talabalaýyk gurnamak maksady bilen hem-de diňleýjiler bilen baglaşylan şertnamalar esasynda,
                        </p>
                        <ol class="mgn-top-4">
                            <h5 class="text-center"><b>buýurýaryn:</b></h5>
                            <li>
                                “Daýan” HK-da, {{date}} aralygyndaky geçiriljek dersleriň başlamagyny gurnamaly.
                                <input type="hidden" name="year2" value="{{name}}"/>
                            </li>
                            <li>
                                {{date}} seneleri aralygynda okan diňleýjileriň jemi sanyny görkezýän maglumaty, 
                                olaryň sanawyny, dürli sebäplere görä okuwyny dowam edip bilmeýänleriň sanawyny, şahadatnamany 
                                almaga hukuk gazananlaryň sanawyny we olaryň synag netijeleriniň sanawyny tapgyryň soňunda bukjada 
                                birleşdirmeli we dikip möhürlemeli.
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-12" id="top-btm-10">
                        <div class="col-md-5">
                            <p class="pull-left">“Daýan” hususy kärhanasynyň direktory</p>
                        </div>
                        <div class="col-md-6">
                            <p class="pull-right">Rejepgulyýew E.A.</p>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <button type="submit" id="word_document" class="btn btn-info pull-right">
                            <i class="fa fa-download"></i> Download word file
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FORM ENDS-->
        <?php echo form_close() ?>

    </div>
    <script type="text/javascript"><!--
        function validate(form) {
            var number = $('#order_form input[name=\'number\']').val();
            var date = $('#order_form input[name=\'order_date\']').val();
            var period = $('#order_form input[name=\'period\']').val();
            if ($.trim(number) === "" || $.trim(date) === "" || $.trim(period) === "") {
                alert('Please fill out all the fields!');
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            }
        }
        //--></script>
    <!-- DOCUMENT STYLE ENDS -->
</div>
