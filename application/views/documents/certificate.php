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
            <span class="glyphicon glyphicon-paperclip"></span> <?php echo lang("ctn_954") ?>
        </div>
    </div>
    <div class="row" ng-app="">
        <!-- FORM STARTS-->
        <?php echo form_open(site_url("documents/generate_cert"), array("class" => "form-horizontal", "id" => "cert_form", "onSubmit" => "return validate(this);")) ?>
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
                        <p><b>Diňleýjilere şahadatnama bermek hakynda</b></p>
                    </div>
                    <div class="col-md-12 mgn-top-4">
                        <p>
                            “Daýan” HK-nyň okuw merkezinde <i>kompýuter sowatlylygy, iňlis, rus dilleri we 
                            suratkeşlik</i> boýunça okan diňleýjiler üçin geçirilen synaglaryň netijesine esaslanyp,
                        </p>
                        <ol class="mgn-top-4">
                            <h5 class="text-center"><b>buýurýaryn:</b></h5>
                            <li>
                                “Daýan” HK-nyň okuw merkezinde kompýuter sowatlylygy, 
                                iňlis, rus dilleri we suratkeşlik dersleri boýunça {{ order_date}} 
                                seneleri aralygynda okan we synaglaryny üstünlikli tabşyran diňleýjilere 
                                şahadatnama bermeli.
                                
                            </li>
                            <p class="mgn-top-4">Sanaw goşulýar, ____ sahypadan ybarat.</p>
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
        
        <?php echo form_open(site_url("documents/test_exel"), array("class" => "form-horizontal", "id" => "excel_form")) ?>
                    
                    <div class="col-xs-12">
                        <button type="submit" id="excel_document" class="btn btn-info pull-right">
                            <i class="fa fa-download"></i> Download excel file
                        </button>
                    </div>
                    <?php echo form_close() ?>

    </div>
    <script type="text/javascript"><!--
        function validate(form) {
            var number = $('#cert_form input[name=\'number\']').val();
            var date = $('#cert_form input[name=\'order_date\']').val();
            var period = $('#cert_form input[name=\'period\']').val();
            if ($.trim(number) === "" || $.trim(date) === "" || $.trim(period) === "") {
                alert('Please fill out all the fields!');
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            }
        }
        $(document).ready(function(){
            $("excel_document").click(function(){
                $("excel_form").submit(function(){
                    alert("Submitted");
                });
            });
        });
        //--></script>
    <!-- DOCUMENT STYLE ENDS -->
</div>
