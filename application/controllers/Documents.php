<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require APPPATH . "third_party/vendor/autoload.php";

use PhpOffice\PhpWord\Element\Section;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Documents
 *
 * @author Shagy
 */
class Documents extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("documents_model");
        if (!$this->user->loggedin) {
            redirect(site_url("login"));
        }

        // If the user does not have premium. 
        // -1 means they have unlimited premium
        if ($this->settings->info->global_premium &&
                ($this->user->info->premium_time != -1 &&
                $this->user->info->premium_time < time())) {
            $this->session->set_flashdata("globalmsg", lang("success_29"));
            redirect(site_url("funds/plans"));
        }

        $this->template->loadData("activeLink", array("documents" => array("general" => 1)));

        if (!$this->common->has_permissions(array("admin", "documents_manager",
                    "documents_viewer"), $this->user)) {
            $this->template->error(lang("error_2"));
        }

        if (!$this->settings->info->documents_section) {
            $this->template->error(lang("error_84"));
        }
    }

    public function index() {
        if (!$this->common->has_permissions(array("admin", "documents_manager"), $this->user)) {
            $this->template->error(lang("error_2"));
        }
        $this->template->loadData("activeLink", array("documents" => array("general" => 1)));

        $this->template->loadContent("documents/index.php", array(
            "page" => "index"
                )
        );
    }

    public function certificate() {
        $this->template->loadData("activeLink", array("documents" => array("certificate" => 1)));

        $this->template->loadContent("documents/certificate.php", array(
            "page" => "certificate"
                )
        );
    }

    public function agreement() {
        $this->template->loadData("activeLink", array("documents" => array("agreement" => 1)));

        $this->template->loadContent("documents/agreement.php", array(
            "page" => "agreement"
                )
        );
    }

    public function generate_doc() {
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        if ($this->input->post("number") !== "" && $this->input->post("order_date") !== "" && $this->input->post("period") !== "") {

            $number = $this->common->nohtml($this->input->post("number"));
            $order_date = $this->common->nohtml($this->input->post("order_date"));
            $period = $this->common->nohtml($this->input->post("period"));

            $filename = 'buyruk.docx';
            $image_logo = base_url() . 'uploads/school_logo.jpg';

            /* Note: any element you append to a document must reside inside of a Section. */

            // Adding an empty Section to the document...
            $section = $phpWord->addSection();
            $section_style = $section->getStyle();
            $position = $section_style->getPageSizeW() - $section_style->getMarginRight() - $section_style->getMarginLeft();
            $phpWord->addParagraphStyle("leftRight", array("tabs" => array(
                    new \PhpOffice\PhpWord\Style\Tab("right", $position)
            )));

            // Add first page header
            $header = $section->addHeader();
            $header->firstPage();
            $table = $header->addTable();
            $table->addRow();
            $cell = $table->addCell(4500);
            $textrun = $cell->addTextRun();
            //$header->addWatermark($image_logo, array('marginTop' => 200, 'marginLeft' => 55));
            $table->addCell(4500)->addImage($image_logo, array('width' => 150, 'height' => 75, 'align' => 'right'));
            $this->printSeparator($section);

            //add some image here
            /* $section->addImage($image_logo, ['alignment' => 'right','width'=>200, 'height'=>100]);
              $this->printSeparator($section); */

            $section->addText(
                    trim($number) . "\t" . trim($order_date), ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], "leftRight");

            $section->addTextBreak(1);
            // Define styles
            $section->addText(
                    "BUÝRUK", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'center']
            );

            $section->addTextBreak(1);

            $section->addText(
                    "Täze okuw tapgyryny açmak hakynda", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'right']
            );
            $section->addTextBreak(2);

            // Adding Text element with font customized using named font style...
            $fontStyleName = 'oneUserDefinedStyle';
            $phpWord->addFontStyle(
                    $fontStyleName, array('name' => 'Cambria (Headings)', 'size' => 14)
            );
            $section->addText(
                    '“Daýan” HK-nyň okuw merkeziniň iňlis, rus dili, suratkeşlik we '
                    . 'kompýuter sowatlylygy boýunça 1-nji tapgyryny talabalaýyk '
                    . 'gurnamak maksady bilen hem-de diňleýjiler bilen baglaşylan '
                    . 'şertnamalar esasynda,', ['name' => 'Cambria (Headings)', 'size' => 14], ['alignment' => 'both'], [
                'space' => ['before' => 360, 'after' => 280],
                'indentation' => ['left' => 540, 'right' => 120]
                    ]
            );

            $section->addTextBreak(1);

            $paragraphStyleName = 'P-Style';
            $phpWord->addParagraphStyle($paragraphStyleName, array('spaceAfter' => 95));

            $predefinedMultilevelStyle = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED);

            $section->addText(
                    "buýurýaryn:", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'center']
            );
            $section->addTextBreak(1);

            $section->addListItem('“Daýan” HK-da, ' . trim($period) . ' aralygyndaky geçiriljek dersleriň başlamagyny gurnamaly.', 0, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(trim($period) . ' seneleri aralygynda okan diňleýjileriň jemi sanyny görkezýän maglumaty, olaryň sanawyny, dürli sebäplere görä okuwyny dowam edip bilmeýänleriň sanawyny, şahadatnamany almaga hukuk gazananlaryň sanawyny we olaryň synag netijeleriniň sanawyny tapgyryň soňunda bukjada birleşdirmeli we dikip möhürlemeli.', 0, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addTextBreak(3);

            $section->addText(
                    "“Daýan” hususy kärhanasynyň direktory\tRejepgulyýew E.A.", ['name' => 'Cambria (Headings)', 'size' => 14], "leftRight");
            $section->addTextBreak(1);

            // Saving the document as OOXML file...
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($filename);


            // send results to browser to download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            flush();
            readfile($filename);
            unlink($filename); // deletes the temporary file
            exit;
        }
    }

    public function generate_cert() {
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        if ($this->input->post("number") !== "" && $this->input->post("order_date") !== "" && $this->input->post("period") !== "") {

            $number = $this->common->nohtml($this->input->post("number"));
            $order_date = $this->common->nohtml($this->input->post("order_date"));
            $period = $this->common->nohtml($this->input->post("period"));

            $filename = 'certificate.docx';

            /* Note: any element you append to a document must reside inside of a Section. */

            // Adding an empty Section to the document...
            $section = $phpWord->addSection();
            $section_style = $section->getStyle();
            $position = $section_style->getPageSizeW() - $section_style->getMarginRight() - $section_style->getMarginLeft();
            $phpWord->addParagraphStyle("leftRight", array("tabs" => array(
                    new \PhpOffice\PhpWord\Style\Tab("right", $position)
            )));

            $this->add_header($section);

            $section->addText(
                    trim($number) . "\t" . trim($order_date), ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], "leftRight");

            $section->addTextBreak(1);
            // Define styles
            $section->addText(
                    "BUÝRUK", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'center']
            );

            $section->addTextBreak(1);

            $section->addText(
                    "Diňleýjilere şahadatnama bermek hakynda", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'right']
            );
            $section->addTextBreak(2);

            // Adding Text element with font customized using named font style...
            $fontStyleName = 'oneUserDefinedStyle';
            $phpWord->addFontStyle(
                    $fontStyleName, array('name' => 'Cambria (Headings)', 'size' => 14)
            );
            $section->addText(
                    '“Daýan” HK-nyň okuw merkezinde kompýuter sowatlylygy, iňlis, rus dilleri we '
                    . 'suratkeşlik boýunça okan diňleýjiler üçin geçirilen synaglaryň netijesine '
                    . 'esaslanyp,', ['name' => 'Cambria (Headings)', 'size' => 14], ['alignment' => 'both'], [
                'space' => ['before' => 360, 'after' => 280],
                'indentation' => ['left' => 540, 'right' => 120]
                    ]
            );

            $section->addTextBreak(1);

            $paragraphStyleName = 'P-Style';
            $phpWord->addParagraphStyle($paragraphStyleName, array('spaceAfter' => 95));

            $predefinedMultilevelStyle = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED);

            $section->addText(
                    "buýurýaryn:", ['name' => 'Cambria (Headings)', 'size' => 14, 'bold' => true], ['alignment' => 'center']
            );
            $section->addTextBreak(1);

            $section->addListItem(
                    '“Daýan” HK-nyň okuw merkezinde kompýuter sowatlylygy, iňlis, rus dilleri '
                    . 'we suratkeşlik dersleri boýunça ' . trim($period) . ' seneleri aralygynda '
                    . 'okan we synaglaryny üstünlikli tabşyran diňleýjilere şahadatnama bermeli.', 0, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $section->addTextBreak(1);
            $section->addText(
                    "Sanaw goşulýar, ____ sahypadan ybarat.", ['name' => 'Cambria (Headings)', 'size' => 14,], ['indent' => 0.5]
            );

            $section->addTextBreak(3);

            $section->addText(
                    "“Daýan” hususy kärhanasynyň direktory\tRejepgulyýew E.A.", ['name' => 'Cambria (Headings)', 'size' => 14], "leftRight");
            $section->addTextBreak(1);

            $this->download($phpWord, $filename);
            exit;
        }
    }

    public function generate_agreement() {
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        if ($this->input->post("tm_name") !== "" && $this->input->post("en_name") !== "" && $this->input->post("week") !== "" && $this->input->post("day") !== "" && $this->input->post("hrs") !== "" && $this->input->post("subject") !== "" && $this->input->post("week_day") !== "" && $this->input->post("time") !== "" && $this->input->post("price") !== "" && $this->input->post("point_1") !== "" && $this->input->post("point_2") !== "" && $this->input->post("agree_week") !== "" && $this->input->post("agree_prcnt") !== "" && $this->input->post("start_date") !== "" && $this->input->post("end_date") !== "" && $this->input->post("lost_fee") !== "" && $this->input->post("late_period") !== "" && $this->input->post("late_fee") !== "") {

            $tm_name = $this->common->nohtml(trim($this->input->post("tm_name")));
            $en_name = $this->common->nohtml(strtoupper(trim($this->input->post("en_name"))));
            $week = $this->common->nohtml(trim($this->input->post("week")));
            $day = $this->common->nohtml(trim($this->input->post("day")));
            $hrs = $this->common->nohtml(trim($this->input->post("hrs")));
            $subject = $this->common->nohtml(trim($this->input->post("subject")));
            $week_day = $this->common->nohtml(trim($this->input->post("week_day")));
            $time = $this->common->nohtml(trim($this->input->post("time")));
            $price = $this->common->nohtml(trim($this->input->post("price")));
            $point_1 = $this->common->nohtml(trim($this->input->post("point_1")));
            $point_2 = $this->common->nohtml(trim($this->input->post("point_2")));
            $agree_week = $this->common->nohtml(trim($this->input->post("agree_week")));
            $agree_prcnt = $this->common->nohtml(trim($this->input->post("agree_prcnt")));
            $start_date = $this->common->nohtml(trim($this->input->post("start_date")));
            $end_date = $this->common->nohtml(trim($this->input->post("end_date")));
            $lost_fee = $this->common->nohtml(trim($this->input->post("lost_fee")));
            $late_period = $this->common->nohtml(trim($this->input->post("late_period")));
            $late_fee = $this->common->nohtml(trim($this->input->post("late_fee")));

            $filename = 'agreement.docx';

            // Adding an empty Section to the document...
            $section = $phpWord->addSection();

            // Define styles
            $section->addText(
                    "ŞERTNAMA", ['name' => 'Cambria', 'size' => 14, 'bold' => true], ['alignment' => 'center']
            );

            $section->addTextBreak(1);

            $textrun = $section->addTextRun(['alignment' => 'both']);
            $textrun->addText("«Daýan» hususy kärhanasynyň müdüri Rejepgulyýew Aşyrdurdy Berdimyradowiçiň "
                    . "adyndan, we raýat ", ['name' => 'Cambria Math', 'size' => 8]);
            $textrun->addText($tm_name, ['name' => 'Cambria', 'size' => 8, 'bold' => true]);
            $textrun->addText(" (", ['name' => 'Cambria Math', 'size' => 8]);
            $textrun->addText($en_name, ['name' => 'Cambria', 'size' => 8, 'bold' => true]);
            $textrun->addText(") (mundan beýläk diňleýji) aşakdakylar barada şu "
                    . "şertnamany baglaşdylar:", ['name' => 'Cambria Math', 'size' => 8]);

            // Adding Text element with font customized using named font style...
            $fontStyleName = 'oneUserDefinedStyle';
            $phpWord->addFontStyle(
                    $fontStyleName, array('name' => 'Cambria Math', 'size' => 8)
            );

            $boldFontStyleName = 'boldOneUserDefinedStyle';
            $phpWord->addFontStyle(
                    $boldFontStyleName, array('name' => 'Cambria', 'size' => 8, 'bold' => true)
            );

            $section->addTextBreak(1);

            $paragraphStyleName = 'P-Style';
            $phpWord->addParagraphStyle($paragraphStyleName, array('spaceAfter' => 95));

            $predefinedMultilevelStyle = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED);


            $section->addListItem(
                    'Diňleýjiniň borçlary', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Bellenen wagtlarda sapaklara doly gatnaşmak.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Sapaklara wagtynda gelmek.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    '«Daýan» okuw merkeziniň emläklerine zeper ýetirmezlik.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Bellenen wagtlarda synaglara gatnaşmak.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Okuw merkezinde tertip-düzgüni saklamak.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    '«Daýan» okuw merkeziniň düzgünlerini doly talabalaýyk ýerine ýetirmek.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Diňleýji sapaga girenlerinde el telefonlaryny öçürmäge borçludyrlar. '
                    . 'Diňleýji el telefonyny öçürmedik halatynda, dolandyryş bölümi tarapyndan '
                    . 'duýduryş beriljekdir.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    '«Daýan» okuw merkeziniň borçlary', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Diňleýjini okuw talaplaryna laýyklykda zerur şertler bilen üpjün etmek.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Sapaklary döwrüň talabyna laýyk we öz wagtynda geçirmek.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Diňleýjini okuwa degişli kitaplar ýa-da elektron ýazgylar bilen üpjün etmek.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Diňleýjini tapgyryň dowamynda ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($week, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" hepde okatmak.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Okuwyň wagtlary', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Okuw merkezinde okuwlar hepdede ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($day, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" gün ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($hrs, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" okuw sagady okadylýar.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Okuw wagtlary diňleýji tarapyndan bir saparlyk bellenilýär.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Okuw bölümi ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($subject, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Okuw wagty hepdäniň ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($week_day, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" günleri ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($time, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(".", ['name' => 'Cambria Math', 'size' => 8]);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Okuw tölegi ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($price, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" manat.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    '«Daýan» okuw merkeziniň düzgünleri', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Synaglaryň netijleriniň orta bahasy iňlis - rus dili kurslarynda okaýan diňleýjiler "
                    . "üçin azyndan ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_1, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" we ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_1, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" - den ýokary bolan diňleýjiler, kompýuter we suratkeşlik kursunda "
                    . "okaýan diňleýjiler üçin azyndan ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_2, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" we ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_2, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" - dan ýokary bolan diňleýjiler Sertifikat "
                    . "tölegini töläp almaga hukuk gazanarlar.", ['name' => 'Cambria Math', 'size' => 8]);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Sapaklarda ýetişigi pes we synaglarynyň bahasy iňlis - rus dili kurslarynda "
                    . "okaýanlar üçin ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_1, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" - den, kompýuter we suratkeşlik kursunda okaýanlar üçin ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($point_2, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText("- dan pes bolan diňleýjilere kurs tamamlanandan soň sertifikat "
                    . "berilmeýär.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Diňleýji synaga sebäpsiz gatnaşmadyk ýagdaýynda, synagdan öň administrasiýa hat '
                    . 'üsti bilen arza ýazyp ýüz tutmaly we administrasiýaň bellän wagtynda synaga '
                    . 'gelmeli.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Diňleýjiniň sapaklara sebäpli ýa-da sebäpsiz gelmedik günleriniň öwezi dolunmaýar. '
                    . 'Iki hepdeden artyk sapaklaryna sebäpsiz gelmedik diňleýji, okuw tölegi '
                    . 'gaýtarylmazdan okuwdan çykarylar.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    '«Daýan» okuw merkeziniň emlägine zeper ýetiren diňleýji ýetiren zyýanyň öwezini doldurmaklyga borçludyr.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Diňleýjileriň ýany bilen getiren goşlarynyň ýitirilen ýa-da zeper ýetirilen '
                    . 'halatlarynda «Daýan» okuw merkezi hiç hili jogapkärçilik çekmeýär.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            //bold and underline section starts
            $section->addListItem(
                    'Diňleýji okuwa mynasyp şekilde geýinmäge borçlydyr. (Köýnekler ýeňli, '
                    . 'ýubkalaryň uzynlygy dyzdan aşak bolmaly).', 1, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Okuw merkezine içgili gelmek, we merkezimizde alkagolly içgiler içmek, '
                    . 'çilim çekmek we nas atmak düýbinden gadagandyr.', 1, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Okuw merkezinde okuwyň dowamynda edep we terbiýe kadalarynyň çäginde hereket etmelidir', 1, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Ene-atalar diňleýjiniň ýetişigi we her ara synaglarynyň netijesi barada '
                    . 'mugallymyndan sorap gyzyklanmalydyrlar. Mugallym diňleýjiniň ýetişigi '
                    . 'barada ene – atasyna habar etmäge borçly däldir!', 1, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            //bold and underlined is end

            $section->addListItem(
                    'Türkmenistan döwleti tarapyndan resmi yglan edilen baýramçylyk günlerinde '
                    . 'okuw merkezimiz işlemeýär, we şol günki sapaklaryň öwezi dolunmaýar.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Diňleýji okuw merkeziniň düzgünlerini bozan we öz borçlaryny ýerine ýetirmedik '
                    . 'ýagdaýynda administrasiýa tarapyndan ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText(" DUÝDURYŞ ", ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" beriler. Diňleýji üç ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText(" DUÝDURYŞ ", ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" alan halatynda okuwdan çykarylar.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Okuw tölegini yzyna gaýtaryp berilmeýär', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $section->addListItem(
                    'Şertnamanyň şertleriniň onuň täzeden baglaşylmasy ýa-da tamamlanmasy', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Şertnama, diňleýji okuwyň dowamynda üç ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText(" DUÝDURYŞ ", ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" alan halatynda okuw merkezi tarapyndan ýatyrlyp biliner.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Şertnamaň başlan möhletinden 1 (bir) hepde geçmän şertnama täzeden baglanyşylyp '
                    . 'biliner (eger diňleýji okuw wagtyny indiki okuw ýazlyşygyna geçirmek islän '
                    . 'ýagdaýynda).', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Şertnamanyň başlanan möhletinden ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($agree_week, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" hepde soň eger diňleýji şertnamany indiki tapgyra geçirmek "
                    . "islän ýagdaýynda okuw töleginiň ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($agree_prcnt, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" tölemelidir.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    "Okuw merkezine içgili ýagdaýda gelmek, ýa-da okuw merkeziniň çäklerinde alkagolly içgiler içilmesi, "
                    . "çilim çekilmesi we nas atylmasy ýagdaýynda şertnama ýatyrylýar.", 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Şertnamanyň möhleti', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Bu şertnama ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($start_date, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" ýyldan - ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($end_date, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" ýyla çenli baglaşyldy.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Şertnama iki tarapyň gol çekmegi bilen güýje girýär.', 1, $fontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);
            $section->addListItem(
                    'Jerimeler', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Sertifikaty gaýtadan almak üçin ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($lost_fee, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" manat jerime tölenmelidir.", ['name' => 'Cambria Math', 'size' => 8]);

            $listItemRun = $section->addListItemRun(1, $predefinedMultilevelStyle);
            $listItemRun->addText("Sertfikat berlip başlan wagtyndan ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($late_period, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" içinde alynmadyk ýagdaýda goşmaça ", ['name' => 'Cambria Math', 'size' => 8]);
            $listItemRun->addText($late_fee, ['name' => 'Cambria Math', 'size' => 8, 'bold' => true]);
            $listItemRun->addText(" manat tölenmelidir.", ['name' => 'Cambria Math', 'size' => 8]);

            $section->addListItem(
                    'Taraplaryň hukuky rekwizitleri', 0, $boldFontStyleName, $predefinedMultilevelStyle, $paragraphStyleName);

            $section_style = $section->getStyle();
            $position = $section_style->getPageSizeW() - $section_style->getMarginRight() - $section_style->getMarginLeft();
            $phpWord->addParagraphStyle("leftRight", array("tabs" => array(
                    new \PhpOffice\PhpWord\Style\Tab("right", $position)
            )));


            $section->addText(
                    "          Diňleýjiniň Familiýasy, ady, atasynyň ady\t“Daýan” H.K.", ['name' => 'Cambria Math', 'size' => 8, 'bold' => true], "leftRight");
            $section->addText(
                    "          ______________________________\tAdres: Aşgabat şäheri, Görogly köçesi, 46-njy jaýy", ['name' => 'Cambria Math', 'size' => 8], "leftRight");
            $section->addText(
                    "          _______________________________\tTel: +99362 125926", ['name' => 'Cambria Math', 'size' => 8], "leftRight");
            $section->addText(
                    "          Goly:__________________________\tE.A. Rejepgulyýew", ['name' => 'Cambria Math', 'size' => 8], "leftRight");
            $section->addText(
                    "                                         \tGoly:_____________", ['name' => 'Cambria Math', 'size' => 8], "leftRight");
            $section->addText(
                    "                                   \tM.Ýe.", ['name' => 'Cambria Math', 'size' => 8, 'bold' => true], "leftRight");
            $section->addTextBreak(3);

            $this->download($phpWord, $filename);
            exit;
        }
    }

    private function download(PhpOffice\PhpWord\PhpWord $word, $filename) {

        // Saving the document as WORD 2007 file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
        $objWriter->save($filename);


        // send results to browser to download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        flush();
        readfile($filename);
        unlink($filename); // deletes the temporary file
    }

    private function add_header(Section $section) {
        $image_logo = base_url() . 'uploads/school_logo.jpg';
        // Add first page header
        $header = $section->addHeader();
        $header->firstPage();
        $table = $header->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        //$header->addWatermark($image_logo, array('marginTop' => 200, 'marginLeft' => 55));
        $table->addCell(4500)->addImage($image_logo, array('width' => 150, 'height' => 75, 'align' => 'right'));
        $this->printSeparator($section);
    }

    private function printSeparator(Section $section) {
        $lineStyle = array('weight' => 2, 'width' => 450, 'height' => 1, 'align' => 'center');
        $section->addLine($lineStyle);
    }

    public function record_page($page) {
        $this->load->library("datatables");

        $this->datatables->set_default_order("behaviour_records.ID", "desc");

        // Set page ordering options that can be used
        $this->datatables->ordering(
                array(
                    0 => array(
                        "users.username" => 0
                    ),
                    1 => array(
                        "rules.name" => 0
                    ),
                    2 => array(
                        "behaviour_records.timestamp" => 0
                    ),
                    3 => array(
                        "users2.username" => 0
                    )
                )
        );

        if ($page == "index") {
            if (!$this->common->has_permissions(array("admin", "behaviour_manager"), $this->user)) {
                $this->template->error(lang("error_2"));
            }
            $this->datatables->set_total_rows(
                    $this->behaviour_model
                            ->get_records_total()
            );
            $records = $this->behaviour_model->get_records($this->datatables);
        } elseif ($page == "your") {
            $this->datatables->set_total_rows(
                    $this->behaviour_model
                            ->get_records_total_user($this->user->info->ID)
            );
            $records = $this->behaviour_model->get_records_user($this->user->info->ID, $this->datatables);
        }



        foreach ($records->result() as $r) {

            $options = "";
            if ($this->common->has_permissions(array("admin", "behaviour_manager"), $this->user)) {
                $options = ' <a href="' . site_url("behaviour/edit_record/" . $r->ID) . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_55") . '"><span class="glyphicon glyphicon-cog"></span></a> <a href="' . site_url("behaviour/delete_record/" . $r->ID . "/" . $this->security->get_csrf_hash()) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'' . lang("ctn_317") . '\')" data-toggle="tooltip" data-placement="bottom" title="' . lang("ctn_57") . '"><span class="glyphicon glyphicon-trash"></span></a>';
            }

            $this->datatables->data[] = array(
                $this->common->get_user_display(array("username" => $r->username, "avatar" => $r->avatar, "online_timestamp" => $r->online_timestamp, "first_name" => $r->first_name, "last_name" => $r->last_name)),
                $r->name,
                date($this->settings->info->date_format, $r->timestamp),
                $this->common->get_user_display(array("username" => $r->username2, "avatar" => $r->avatar2, "online_timestamp" => $r->online_timestamp2, "first_name" => $r->first_name2, "last_name" => $r->last_name2)),
                $options
            );
        }

        echo json_encode($this->datatables->process());
    }

}
