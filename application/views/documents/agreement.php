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
    #en-name {
        text-transform: uppercase;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="white-area-content">
    <!-- DOCUMENT STYLE STARTS -->
    <div class="db-header clearfix">
        <div class="page-header-title"> 
            <span class="glyphicon glyphicon-paperclip"></span> <?php echo lang("ctn_955") ?>
        </div>
    </div>
    <div class="row" ng-app="">
        <!-- FORM STARTS-->
        <?php echo form_open(site_url("documents/generate_agreement"), array("class" => "form-horizontal", "id" => "agreement_form", "onSubmit" => "return validate(this);")) ?>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_959") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="tm_name" name="tm_name" placeholder="Ady Familiýasy Atasynyň ady">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_960") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="en_name" name="en_name" placeholder="Firstname Lastname Fathersname">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_961") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="week" name="week" placeholder="0">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_962") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="day" name="day" placeholder="0">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_963") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="hrs" name="hrs" placeholder="0  akademik">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_964") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="subject" name="subject" placeholder="Rus dili - tapgyr 1.">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_965") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="week_day" name="week_day" placeholder="2,4,6">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_966") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="time" name="time" placeholder="15:00 - da.">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_967") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="price" name="price" placeholder="0.00">
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_968") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="point_1" name="point_1" placeholder="0">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_969") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="point_2" name="point_2" placeholder="0">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_970") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="agree_week" name="agree_week" placeholder="0">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_971") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="agree_prcnt" name="agree_prcnt" placeholder="0%">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_972") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="start_date" name="start_date" placeholder="01.01.1001">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_973") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="end_date" name="end_date" placeholder="01.01.1001">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_974") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="lost_fee" name="lost_fee" placeholder="0.00">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_975") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="late_period" name="late_period" placeholder="0 aý">
                </div>
            </div>
            <div class="form-group">
                <label for="p-in" class="col-md-5 label-heading"><?php echo lang("ctn_976") ?></label>
                <div class="col-md-7 ui-front">
                    <input type="text" class="form-control" ng-model="late_fee" name="late_fee" placeholder="0.00">
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div id="page-content" class="panel panel-default"> <!-- style="width:756px" -->
                <div class="panel-body">
                    <div class="col-md-12 mgn-top-4">
                        <h3 class="text-center"><b>ŞERTNAMA</b></h3>
                    </div>
                    <div class="col-md-12 mgn-top-4">
                        <p>
                            «Daýan» hususy kärhanasynyň müdüri Rejepgulyýew Aşyrdurdy Berdimyradowiçiň 
                            adyndan, we raýat <b>{{ tm_name }}</b> (<b id="en-name">{{ en_name }}</b>)
                            (mundan beýläk diňleýji) aşakdakylar barada şu şertnamany baglaşdylar: 

                        </p>
                        <ol class="mgn-top-4">
                            <li>
                                <b>Diňleýjiniň borçlary</b>
                                <ol>
                                    <li>Bellenen wagtlarda sapaklara doly gatnaşmak. </li>
                                    <li>Sapaklara wagtynda gelmek.</li>
                                    <li>«Daýan» okuw merkeziniň emläklerine zeper ýetirmezlik.</li>
                                    <li>Bellenen wagtlarda synaglara gatnaşmak.</li>
                                    <li>Okuw merkezinde tertip-düzgüni saklamak.</li>
                                    <li>«Daýan»  okuw merkeziniň düzgünlerini doly talabalaýyk 
                                        ýerine ýetirmek.</li>
                                    <li>Diňleýji sapaga girenlerinde el telefonlaryny öçürmäge
                                        borçludyrlar. Diňleýji el telefonyny öçürmedik halatynda, 
                                        dolandyryş bölümi tarapyndan duýduryş beriljekdir.</li>
                                </ol>
                            </li>
                            <li>
                                <b>«Daýan» okuw merkeziniň borçlary </b>
                                <ol>
                                    <li>Diňleýjini okuw talaplaryna laýyklykda zerur şertler bilen üpjün etmek.</li>
                                    <li>Sapaklary döwrüň talabyna laýyk we öz wagtynda geçirmek.</li>
                                    <li>Diňleýjini okuwa degişli kitaplar ýa-da elektron ýazgylar  bilen üpjün etmek.</li>
                                    <li>Diňleýjini tapgyryň dowamynda <b>{{ week }}</b> hepde okatmak .</li>
                                </ol>
                            </li>
                            <li>
                                <b>Okuwyň wagtlary </b>
                                <ol>
                                    <li>Okuw merkezinde okuwlar hepdede <b>{{ day }}</b> gün <b>{{ hrs }}</b>   okuw sagady okadylýar. </li>
                                    <li>Okuw wagtlary diňleýji tarapyndan bir saparlyk bellenilýär. </li>
                                    <li>Okuw bölümi <b>{{ subject }}</b></li>
                                    <li>Okuw wagty hepdäniň <b>{{ week_day }}</b> günleri <b>{{ time }}</b>.</li>
                                    <li>Okuw  tölegi <b>{{ price }}</b> manat. </li>
                                </ol>
                            </li>
                            <li>
                                <b>«Daýan» okuw merkeziniň düzgünleri </b>
                                <ol>
                                    <li>
                                        Synaglaryň netijleriniň orta bahasy iňlis - rus dili kurslarynda 
                                        okaýan diňleýjiler üçin azyndan <b>{{ point_1 }}</b> we <b>{{ point_1 }}</b> - den ýokary bolan 
                                        diňleýjiler, kompýuter we suratkeşlik kursunda okaýan diňleýjiler 
                                        üçin azyndan <b>{{ point_2 }}</b> we <b>{{ point_2 }}</b> - dan ýokary bolan diňleýjiler Sertifikat 
                                        tölegini töläp almaga hukuk gazanarlar.
                                    </li>
                                    <li>
                                        Sapaklarda ýetişigi pes we synaglarynyň bahasy iňlis - rus dili 
                                        kurslarynda okaýanlar üçin <b>{{ point_1 }}</b> - den, kompýuter we suratkeşlik 
                                        kursunda okaýanlar üçin <b>{{ point_2 }}</b> - dan pes bolan diňleýjilere kurs 
                                        tamamlanandan soň  sertifikat   berilmeýär.
                                    </li>
                                    <li>
                                        Diňleýji synaga sebäpsiz gatnaşmadyk ýagdaýynda, synagdan öň administrasiýa hat üsti 
                                        bilen arza ýazyp  ýüz tutmaly we administrasiýaň bellän wagtynda synaga gelmeli.
                                    </li>
                                    <li>
                                        Diňleýjiniň sapaklara sebäpli ýa-da sebäpsiz gelmedik günleriniň öwezi dolunmaýar. 
                                        Iki hepdeden artyk sapaklaryna sebäpsiz gelmedik diňleýji, okuw tölegi gaýtarylmazdan 
                                        okuwdan çykarylar. 
                                    </li>
                                    <li>
                                        «Daýan» okuw merkeziniň emlägine zeper ýetiren diňleýji ýetiren zyýanyň öwezini 
                                        doldurmaklyga borçludyr.
                                    </li>
                                    <li>
                                        Diňleýjileriň ýany bilen getiren goşlarynyň ýitirilen ýa-da zeper ýetirilen halatlarynda 
                                        «Daýan»  okuw merkezi hiç hili jogapkärçilik çekmeýär.
                                    </li>
                                    <li>
                                        <b><i><u>Diňleýji okuwa mynasyp şekilde geýinmäge borçlydyr. (Köýnekler ýeňli, ýubkalaryň 
                                        uzynlygy dyzdan aşak bolmaly).</u></i></b>
                                    </li>
                                    <li>
                                        <b><i><u>
                                            Okuw merkezine içgili gelmek, we merkezimizde alkagolly içgiler içmek, çilim çekmek 
                                            we nas atmak düýbinden gadagandyr. 
                                                </u></i></b>
                                    </li>
                                    <li>
                                        <b><i><u>
                                        Okuw merkezinde okuwyň dowamynda edep we terbiýe kadalarynyň çäginde hereket etmelidir
                                        </u></i></b>
                                    </li>
                                    <li>
                                        <b><i><u>
                                        Ene-atalar diňleýjiniň  ýetişigi we her ara synaglarynyň netijesi barada mugallymyndan 
                                        sorap  gyzyklanmalydyrlar. Mugallym diňleýjiniň ýetişigi barada ene – atasyna habar etmäge 
                                        borçly däldir! 
                                        </u></i></b>
                                    </li>
                                    <li>
                                        Türkmenistan döwleti tarapyndan resmi yglan edilen baýramçylyk günlerinde okuw merkezimiz 
                                        işlemeýär, we şol günki sapaklaryň öwezi dolunmaýar. 
                                    </li>
                                    <li>
                                        4.12   Diňleýji okuw merkeziniň düzgünlerini bozan we öz borçlaryny ýerine ýetirmedik 
                                        ýagdaýynda administrasiýa tarapyndan <b>DUÝDURYŞ</b> beriler. Diňleýji üç <b>DUÝDURYŞ</b> 
                                        alan halatynda okuwdan çykarylar. 
                                    </li>
                                    <li>
                                        <b><i><u>
                                        Okuw tölegini yzyna gaýtaryp berilmeýär
                                        </u></i></b>
                                    </li>
                                </ol>
                            </li>
                            <li>
                                <b>Şertnamanyň şertleriniň onuň täzeden baglaşylmasy ýa-da tamamlanmasy</b>
                                <ol>
                                    <li>Şertnama, diňleýji okuwyň dowamynda üç <b>DUÝDURYŞ</b> alan halatynda okuw merkezi tarapyndan 
                                        ýatyrlyp biliner.
                                    </li>
                                    <li>
                                        Şertnamaň başlan möhletinden 1 (bir) hepde geçmän şertnama täzeden baglanyşylyp biliner 
                                        (eger diňleýji okuw wagtyny indiki okuw ýazlyşygyna geçirmek islän ýagdaýynda). 
                                    </li>
                                    <li>
                                        Şertnamanyň başlanan möhletinden <b>{{ agree_week }} hepde</b> soň eger diňleýji şertnamany  indiki tapgyra 
                                        geçirmek islän ýagdaýynda okuw töleginiň <b>{{ agree_prcnt }}</b> tölemelidir. 
                                    </li>
                                    <li>
                                        Okuw merkezine içgili ýagdaýda gelmek, ýa-da okuw merkeziniň çäklerinde alkagolly içgiler içilmesi, 
                                        çilim çekilmesi we nas atylmasy ýagdaýynda şertnama ýatyrylýar.
                                    </li>
                                </ol>
                            </li>
                            <li>
                                <b>Şertnamanyň möhleti</b>
                                <ol>
                                    <li>Bu şertnama {{start_date}} ýyldan - {{ end_date }} ýyla çenli baglaşyldy.</li>
                                    <li>Şertnama iki tarapyň gol çekmegi bilen güýje girýär.</li>
                                </ol>
                            </li>
                            <li>
                                <b>Jerimeler</b>
                                <ol>
                                    <li>Sertifikaty gaýtadan almak üçin <b>{{ lost_fee }}</b> manat jerime tölenmelidir. </li>
                                    <li>
                                        Sertfikat berlip başlan wagtyndan <b>{{ late_period }}</b> içinde alynmadyk ýagdaýda 
                                        goşmaça <b>{{ late_fee }}</b> manat tölenmelidir.
                                    </li>
                                </ol>
                            </li>
                            <li>
                                <b>Taraplaryň hukuky rekwizitleri</b>
                            </li>
                        </ol>
                        <div class="col-md-6" style="padding-left: 40px;">
                            <p>Diňleýjiniň Familiýasy, ady, atasynyň ady</p>
                            <p>_________________________________</p>
                            <p>_________________________________</p>
                            <p>Goly:_____________________________</p>
                        </div>
                        <div class="col-md-6" style="padding-left: 40px;">
                            <p><b>“Daýan” H.K.</b></p>
                            <address>Adres: Aşgabat şäheri, Görogly köçesi, 46-njy jaýy</address>
                            <p>Tel: +99362 125926</p>
                            <p>E.A. Rejepgulyýew </p>
                            <p>Goly:_____________</p>
                            <p><b>M.Ýe.</b></p>
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
            var tm_name = $('#agreement_form input[name=\'tm_name\']').val();
            var en_name = $('#agreement_form input[name=\'en_name\']').val();
            var week = $('#agreement_form input[name=\'week\']').val();
            var day = $('#agreement_form input[name=\'day\']').val();
            var hrs = $('#agreement_form input[name=\'hrs\']').val();
            var subject = $('#agreement_form input[name=\'subject\']').val();
            var week_day = $('#agreement_form input[name=\'week_day\']').val();
            var time = $('#agreement_form input[name=\'time\']').val();
            var price = $('#agreement_form input[name=\'price\']').val();
            var point_1 = $('#agreement_form input[name=\'point_1\']').val();
            var point_2 = $('#agreement_form input[name=\'point_2\']').val();
            var agree_week = $('#agreement_form input[name=\'agree_week\']').val();
            var agree_prcnt = $('#agreement_form input[name=\'agree_prcnt\']').val();
            var start_date = $('#agreement_form input[name=\'start_date\']').val();
            var end_date = $('#agreement_form input[name=\'end_date\']').val();
            var lost_fee = $('#agreement_form input[name=\'lost_fee\']').val();
            var late_period = $('#agreement_form input[name=\'late_period\']').val();
            var late_fee = $('#agreement_form input[name=\'late_fee\']').val();
            
            if (
                    $.trim(tm_name) === "" || $.trim(en_name) === "" || $.trim(week) === ""
                    || $.trim(day) === "" || $.trim(hrs) === "" || $.trim(subject) === ""
                    || $.trim(week_day) === "" || $.trim(time) === "" || $.trim(price) === ""
                    || $.trim(point_1) === "" || $.trim(point_2) === "" || $.trim(agree_week) === ""
                    || $.trim(agree_prcnt) === "" || $.trim(start_date) === "" || $.trim(end_date) === ""
                    || $.trim(lost_fee) === "" || $.trim(late_period) === "" || $.trim(late_fee) === "") {
                alert('Please fill out all the fields!');
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            }
        }
        //--></script>
    <!-- DOCUMENT STYLE ENDS -->
</div>
