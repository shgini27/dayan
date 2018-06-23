<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require APPPATH . "third_party/vendor/autoload.php";

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
        $this->load->model("classes_model");
        $this->load->model("students_model");
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

    public function download_attendance($class_id) {
        $classes = $this->classes_model->get_class(intval($class_id));
        if ($classes->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }
        $class = $classes->row();

        //$category = $this->classes_model->get_category($class->categoryid)->row();

        $this->attendance_sheet($class);
    }

    private function attendance_sheet($class) {
        $filename = 'attendance.xlsx';
        $spreadsheet = new Spreadsheet();

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        //set font style and size
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        //alignment settings here
        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setWrapText(true);

        //$class_students = $this->classes_model->get_students_from_class_only($class->ID);
        $attendance_sheets = $this->classes_model->get_attendance_sheet_by_class($class->ID);

        $counter = 1;
        foreach ($attendance_sheets->result() as $attendance_sheet) {
            $event = $this->classes_model->get_class_event($attendance_sheet->eventid)->row();
            $attendance_sheet_entries = $this->classes_model->get_attendance_sheet_entries($class->ID, $attendance_sheet->ID);


            if ($counter === 1) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            $sheet->setTitle("ATTENDANCE-$counter");

            $sheet->getStyle('A1:A2')
                    ->getFont()
                    ->setSize(14);

            // merging cells
            $sheet->mergeCells('A1:C1');
            $sheet->mergeCells('A2:C2');
            $sheet->mergeCells('A3:C3');
            $sheet->mergeCells('A4:C4');
            $sheet->mergeCells('A5:C5');
            $sheet->mergeCells('A6:C6');

            //bordering cells
            $sheet->getStyle('A1:C1')->applyFromArray($styleArray);
            $sheet->getStyle('A2:C2')->applyFromArray($styleArray);
            $sheet->getStyle('A3:C3')->applyFromArray($styleArray);
            $sheet->getStyle('A4:C4')->applyFromArray($styleArray);
            $sheet->getStyle('A5:C5')->applyFromArray($styleArray);
            $sheet->getStyle('A6:C6')->applyFromArray($styleArray);

            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(40);

            //setting cells values for header part
            $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
            $sheet->setCellValue('A2', $attendance_sheet->name . " Room: $event->room");
            $sheet->setCellValue('A3', "Belligi Allan: $attendance_sheet->first_name $attendance_sheet->last_name");
            $sheet->setCellValue('A4', "$event->start - $event->end"); //course period 28.08.2017 - 18.11.2017(1)
            $sheet->setCellValue('A5', "Gatnaşyk derejesi: $attendance_sheet->attendance %");


            $sheet->setCellValue('A7', 'Ady we Familiýasy'); //order number
            $sheet->getStyle('A7')->applyFromArray($styleArray);

            $sheet->setCellValue('B7', 'Gatnaşygy'); //order number
            $sheet->getStyle('B7')->applyFromArray($styleArray);

            $sheet->setCellValue('C7', 'Sebäbi'); //order number
            $sheet->getStyle('C7')->applyFromArray($styleArray); //attendance_date

            $row = 8;
            foreach ($attendance_sheet_entries->result() as $class_student) {
                $sheet->getStyle("A$row")->getFont()->setBold(false);
                $sheet->getStyle("B$row")->getFont()->setBold(false);
                $sheet->getStyle("C$row")->getFont()->setBold(false);

                $sheet->getStyle("A$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("B$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $sheet->setCellValue("A$row", "$class_student->first_name $class_student->last_name"); //order number
                $sheet->getStyle("A$row")->applyFromArray($styleArray);

                $present = 'Bellenmedi';
                if (intval($class_student->present) === 1) {
                    $present = 'Geldi';
                }

                if (intval($class_student->absent) === 1) {
                    $present = 'Gelmedi';
                }

                if (intval($class_student->late) === 1) {
                    $present = 'Gija Galyp Geldi';
                }

                $sheet->setCellValue("B$row", $present); //order number
                $sheet->getStyle("B$row")->applyFromArray($styleArray);

                $sheet->setCellValue("C$row", "$class_student->notes"); //order number
                $sheet->getStyle("C$row")->applyFromArray($styleArray);

                $row++;
            }

            $counter++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $this->download($filename);
    }

    public function close_category($category_id) {
        $category = $this->classes_model->get_category($category_id)->row(); //$category_id
        $classes = $this->classes_model->get_classes_by_category($category_id); //$category_id

        $filename = "Dayan-Tapgyr-$category->number.xlsx";
        $spreadsheet = new Spreadsheet();

        $this->get_first_sheet($spreadsheet, $category, $classes);

        $this->get_second_sheet($spreadsheet, $category, $classes);

        $this->get_third_sheet($spreadsheet, $category, $classes);

        $this->get_fourth_sheet($spreadsheet, $category, $classes);

        $this->get_fifth_sheet($spreadsheet, $category, $classes);



        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $this->download($filename);
    }

    /**

     * Method to Download file for related category
     * @param type $category_id  id for category   
     * @return	.docx file
     */
    public function download_order($category_id, $type) {
        $cat_data = $this->classes_model->get_category_data($category_id);

        if ($cat_data->num_rows() > 0) {
            $counter = 1;
            $subjects = '';
            $period = '';
            foreach ($cat_data->result() as $data) {
                $subjects .= $data->name;

                $start_date = new DateTime($data->start_date);
                $end_date = new DateTime($data->end_date);

                if ($counter === ($cat_data->num_rows() - 1)) {
                    $subjects .= ' we ';
                    $period .= $start_date->format('d-m-Y') . ' - ' . $end_date->format('d-m-Y');
                } elseif ($cat_data->num_rows() !== 1) {
                    $subjects .= ', ';
                } else {
                    $period .= $start_date->format('d-m-Y') . ' - ' . $end_date->format('d-m-Y');
                }

                $counter ++;
            }

            if ($type === 'general') {
                $this->draw_order_document($subjects, $period);
            } elseif ($type === 'certificate') {
                $this->draw_certificate_document($subjects, $period);
            }
        }
    }

    public function download_agreement($student_id) {
        $student_id = intval($student_id);
        $class_students = $this->classes_model->get_class_student($student_id);
        if ($class_students->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class_student = $class_students->row();

        $classs = $this->classes_model->get_class($class_student->classid);
        if ($classs->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $classs->row();

        $cat_data = $this->classes_model->get_category_data($class->categoryid);
        $category = $cat_data->row();

        $week = ceil($category->hrs / 6);
        if ($class->class_days === "odd") {
            $week_days = "1, 3, 5";
        } else {
            $week_days = "2, 4, 6";
        }

        $student_data = [
            "tm_name" => "$class_student->first_name $class_student->last_name $class_student->fathers_name",
            "en_name" => "$class_student->first_name $class_student->last_name $class_student->fathers_name",
            "week" => "$week",
            "hrs" => "$category->hrs",
            "subject" => "$category->name",
            "week_day" => $week_days,
            "time" => "$class->start_hour",
            "price" => "700", //check it later for price**********************************!!!!***************************************
            "start_date" => "$category->start_date",
            "end_date" => "$category->end_date",
            "agreement_no" => $class_student->agreement_number
        ];

        $this->draw_agreement_document($student_data);

        exit();
    }

    public function download_student_attendance($student_id) {
        $id = intval($student_id);
        $class_students = $this->classes_model->get_class_student($id);
        if ($class_students->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class_student = $class_students->row();

        $classs = $this->classes_model->get_class($class_student->classid);
        if ($classs->num_rows() == 0) {
            $this->template->error(lang("error_92"));
        }

        $class = $classs->row();

        $this->draw_attendance_sheet($class_student, $class);

        exit();

        /* get excel sheet */
    }

    public function download_certificate_order($category_id) {
        $cat_data = $this->classes_model->get_category_data($category_id);

        if ($cat_data->num_rows() > 0) {
            $counter = 1;
            $subjects = '';
            $period = '';
            foreach ($cat_data->result() as $data) {
                $subjects .= $data->name;

                $start_date = new DateTime($data->start_date);
                $end_date = new DateTime($data->end_date);

                if ($counter === ($cat_data->num_rows() - 1)) {
                    $subjects .= ' we ';
                    $period .= $start_date->format('d-m-Y') . ' - ' . $end_date->format('d-m-Y');
                } elseif ($cat_data->num_rows() !== 1) {
                    $subjects .= ', ';
                } else {
                    $period .= $start_date->format('d-m-Y') . ' - ' . $end_date->format('d-m-Y');
                }

                $counter ++;
            }

            $this->draw_word_document_2($subjects, $period);
        }
    }

    private function draw_order_document($subjects, $period) {
        $filename = 'buyruk.docx';

        //$image_logo = base_url() . 'uploads/school_logo.jpg';

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();
        $section_style = $section->getStyle();
        $position = $section_style->getPageSizeW() - $section_style->getMarginRight() - $section_style->getMarginLeft();
        $phpWord->addParagraphStyle("leftRight", array("tabs" => array(
                new \PhpOffice\PhpWord\Style\Tab("right", $position)
        )));

        $this->add_header($section);

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
                '“Daýan” HK-nyň okuw merkeziniň ' . $subjects . ' boýunça 1-nji tapgyryny talabalaýyk '
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

        // Saving the document as WORD 2007 file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
        $objWriter->save($filename);

        //download the created content
        $this->download($filename);
    }

    private function draw_certificate_document($subjects, $period) {
        $filename = 'sertifikat_buyruk.docx';

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();
        $section_style = $section->getStyle();
        $position = $section_style->getPageSizeW() - $section_style->getMarginRight() - $section_style->getMarginLeft();
        $phpWord->addParagraphStyle("leftRight", array("tabs" => array(
                new \PhpOffice\PhpWord\Style\Tab("right", $position)
        )));

        $this->add_header($section);

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
                '“Daýan” HK-nyň okuw merkezinde ' . $subjects . ' boýunça okan diňleýjiler üçin geçirilen synaglaryň netijesine '
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

        // Saving the document as WORD 2007 file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
        $objWriter->save($filename);

        $this->download($filename);
    }

    private function draw_agreement_document($student_data) {

        $tm_name = $student_data['tm_name'];
        $en_name = $student_data['en_name'];
        $week = $student_data['week']; //total study week
        $day = 3; //3 gun
        $hrs = $student_data['hrs']; //total hrs
        $subject = $student_data['subject'];
        $week_day = $student_data['week_day']; //example (1, 3, 5)
        $time = $student_data['time']; // 15:00'da
        $price = $student_data['price']; // school price
        $point_1 = 70; //point for language
        $point_2 = 60; //point for komputer
        $agree_week = '1 hepde';
        $agree_prcnt = '50%';
        $start_date = $student_data['start_date'];
        $end_date = $student_data['end_date'];
        $lost_fee = '40'; //40 manat
        $late_period = '1 aý';
        $late_fee = '10'; //10 manat

        $agreement_no = $student_data['agreement_no'];



        $filename = "agreement-$agreement_no.docx";

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();

        // Define styles
        $section->addText(
                "ŞERTNAMA ($agreement_no)", ['name' => 'Cambria', 'size' => 14, 'bold' => true], ['alignment' => 'center']
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

        // Saving the document as WORD 2007 file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
        $objWriter->save($filename);

        $this->download($filename);
    }

    private function draw_attendance_sheet($student, $class) {
        $filename = "$student->username-attendance.xlsx";
        $spreadsheet = new Spreadsheet();

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        //set font style and size
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        //alignment settings here
        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setWrapText(true);


        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("ATTENDANCE");

        $sheet->getStyle('A1:A2')
                ->getFont()
                ->setSize(14);

        // merging cells
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->mergeCells('A4:E4');

        //bordering cells
        $sheet->getStyle('A1:E1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:E2')->applyFromArray($styleArray);
        $sheet->getStyle('A3:E3')->applyFromArray($styleArray);
        $sheet->getStyle('A4:E4')->applyFromArray($styleArray);
        
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(20);

        
        //setting cells values for header part
        $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
        $sheet->setCellValue('A2', $class->name);
        $sheet->setCellValue('A3', $student->first_name . " " . $student->last_name);


        $sheet->setCellValue('A5', 'Senesi'); //order number
        $sheet->getStyle('A5')->applyFromArray($styleArray);

        $sheet->setCellValue('B5', 'Wagty'); //order number
        $sheet->getStyle('B5')->applyFromArray($styleArray);
        
        $sheet->setCellValue('C5', 'Gatnaşygy'); //order number
        $sheet->getStyle('C5')->applyFromArray($styleArray);

        $sheet->setCellValue('D5', 'Sebäbi'); //order number
        $sheet->getStyle('D5')->applyFromArray($styleArray); //attendance_date
        
        $sheet->setCellValue('E5', 'Mugallymy'); //order number
        $sheet->getStyle('E5')->applyFromArray($styleArray); //attendance_date
        
        $student_attendance_sheets = $this->classes_model->get_student_attendances($class->ID, $student->userid);

        $counter = 1;
        $row = 6;
        foreach ($student_attendance_sheets->result() as $class_student) {
            $sheet->getStyle("A$row")->getFont()->setBold(false);
            $sheet->getStyle("B$row")->getFont()->setBold(false);
            $sheet->getStyle("C$row")->getFont()->setBold(false);
            $sheet->getStyle("D$row")->getFont()->setBold(false);
            $sheet->getStyle("E$row")->getFont()->setBold(false);

            $sheet->getStyle("A$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $date = substr($class_student->start, 0, 10);
            $time_start = substr($class_student->start, 11, 5);
            $time_end = substr($class_student->end, 11, 5);
            
            $sheet->setCellValue("A$row", $date); //order number
            $sheet->getStyle("A$row")->applyFromArray($styleArray);
            
            $sheet->setCellValue("B$row", "$time_start - $time_end"); //order number
            $sheet->getStyle("B$row")->applyFromArray($styleArray);

            $present = 'Bellenmedi';
            if (intval($class_student->present) === 1) {
                $present = 'Geldi';
                $sheet->getStyle("C$row")->getFont()->setColor(new Color(Color::COLOR_GREEN));
            }

            if (intval($class_student->absent) === 1) {
                $present = 'Gelmedi';
                $sheet->getStyle("C$row")->getFont()->setColor(new Color(Color::COLOR_RED));
            }

            if (intval($class_student->late) === 1) {
                $present = 'Gija Galyp Geldi';
                $sheet->getStyle("C$row")->getFont()->setColor(new Color(Color::COLOR_YELLOW));
            }

            $sheet->setCellValue("C$row", $present); //order number
            $sheet->getStyle("C$row")->applyFromArray($styleArray);

            $sheet->setCellValue("D$row", "$class_student->notes"); //order number
            $sheet->getStyle("D$row")->applyFromArray($styleArray);
            
            $sheet->setCellValue("E$row", "$class_student->first_name $class_student->last_name"); //order number
            $sheet->getStyle("E$row")->applyFromArray($styleArray);

            $row++;
            $counter++;
        }




        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $this->download($filename);
    }

    private function printSeparator(Section $section) {
        $lineStyle = array('weight' => 2, 'width' => 450, 'height' => 1, 'align' => 'center');
        $section->addLine($lineStyle);
    }

    /**

     * Method to generate statistic of class and students activity
     * @param type $spreadsheet
     * @param type $category
     * @param type $classes
     * @return object Will return spreadsheet page    /
     */
    private function get_first_sheet($spreadsheet, $category, $classes) {
        /*         * **************************FIRST PAGE STARTS HERE*************************** */
        $sheet_1 = $spreadsheet->getActiveSheet();
        $sheet_1->setTitle("STATISTIKA");

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        $sheet_1->getStyle('A1:A2')
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(14);


        $sheet_1->mergeCells('A1:J1');
        $sheet_1->mergeCells('A2:J2');

        //merge for column
        $sheet_1->mergeCells('A3:A4');
        $sheet_1->mergeCells('B3:B4');
        $sheet_1->mergeCells('C3:C4');
        $sheet_1->mergeCells('D3:D4');
        $sheet_1->mergeCells('E3:E4');
        $sheet_1->mergeCells('F3:F4');
        $sheet_1->mergeCells('G3:G4');
        $sheet_1->mergeCells('H3:I3');
        //$sheet_1->mergeCells('I3:I4');
        $sheet_1->mergeCells('J3:J4');

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet_1->getStyle('A1')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet_1->getStyle('A2')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet_1->getStyle('A3')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet_1->getStyle('A3')
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet_1->getStyle('J3')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet_1->getStyle('J3')
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        /* $sheet_1->getRowDimension('3')
          ->setRowHeight(40); */
        $sheet_1->getColumnDimension('B')->setWidth(20);
        $sheet_1->getColumnDimension('C')->setWidth(20);
        $sheet_1->getColumnDimension('D')->setWidth(20);
        $sheet_1->getColumnDimension('E')->setWidth(20);
        $sheet_1->getColumnDimension('H')->setWidth(20);
        $sheet_1->getColumnDimension('J')->setWidth(20);
        $sheet_1->getColumnDimension('I')->setWidth(15);

        $sheet_1->getStyle('A3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('B3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('C3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('D3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('E3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('F3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('G3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('H3')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('H4')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('I4')
                ->getAlignment()
                ->setWrapText(true);
        $sheet_1->getStyle('J3')
                ->getAlignment()
                ->setWrapText(true);

        $sheet_1->getStyle('A1:J1')->applyFromArray($styleArray);
        $sheet_1->getStyle('A2:J2')->applyFromArray($styleArray);

        $sheet_1->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
        $sheet_1->setCellValue('A2', $category->start_date . ' - ' . $category->end_date . "($category->number)"); //course period 28.08.2017 - 18.11.2017(1)

        $sheet_1->setCellValue('A3', 'No');
        $sheet_1->getStyle('A3:A4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('B3', 'Kurslar'); //courses
        $sheet_1->getStyle('B3:B4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('C3', 'Kursa ýazylanlar'); //total registered to this course
        $sheet_1->getStyle('C3:C4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('D3', 'Kursdan çykanlar'); //dropped
        $sheet_1->getStyle('D3:D4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('E3', 'Kursy tamamlanlar'); //completed
        $sheet_1->getStyle('E3:E4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('F3', 'Aýal'); //woman
        $sheet_1->getStyle('F3:F4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('G3', 'Erkek'); //man
        $sheet_1->getStyle('G3:G4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('H3', 'Final synagy'); //final exam
        $sheet_1->getStyle('H3')->applyFromArray($styleArray);
        $sheet_1->setCellValue('H4', 'Şahadatnama almaga hukuk gazanan diňleýjiler'); //pass
        $sheet_1->getStyle('H4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('I4', 'Geçmedikler'); //fail
        $sheet_1->getStyle('I4')->applyFromArray($styleArray);
        $sheet_1->setCellValue('J3', 'Final synaga gatnaşmadyklar'); //not attend
        $sheet_1->getStyle('J3:J4')->applyFromArray($styleArray);
        //Header Part is ENDED here
        //set cell values with proper data
        $counter = 1;
        $cell = 5;
        $students = 0;
        $drops = 0;
        $grads = 0;
        $male = 0;
        $female = 0;
        $total_passed = 0;
        $total_failed = 0;
        $total_not_attent = 0;
        foreach ($classes->result() as $class) {
            //$studens_total = $this->classes_model->get_student_count($class->ID);

            $sheet_1->setCellValue('A' . $cell, $counter); //numbering --> auto incrementin
            $sheet_1->getStyle('A' . $cell)->applyFromArray($styleArray);

            $sheet_1->setCellValue('B' . $cell, $class->name); //class name
            $sheet_1->getStyle('B' . $cell)->applyFromArray($styleArray);

            $sheet_1->setCellValue('C' . $cell, $class->students); //registered student in class
            $sheet_1->getStyle('C' . $cell)->applyFromArray($styleArray);

            $dropped_students = $this->students_model->get_dropped_students_by_class($class->ID);

            $gender = $this->count_male_and_female($class->ID);

            $sheet_1->setCellValue('D' . $cell, $dropped_students); //registered student in class
            $sheet_1->getStyle('D' . $cell)->applyFromArray($styleArray);

            $done = intval($class->students) - intval($dropped_students);

            $sheet_1->setCellValue('E' . $cell, $done); //registered student in class
            $sheet_1->getStyle('E' . $cell)->applyFromArray($styleArray);

            $sheet_1->setCellValue('F' . $cell, $gender['female']); //female student in class
            $sheet_1->getStyle('F' . $cell)->applyFromArray($styleArray);

            $sheet_1->setCellValue('G' . $cell, $gender['male']); //male student in class
            $sheet_1->getStyle('G' . $cell)->applyFromArray($styleArray);

            $exam_result = $this->calculate_total_passed_and_failed($class->ID);

            $sheet_1->setCellValue('H' . $cell, $exam_result['total_passed']); //will take certificate
            $sheet_1->getStyle('H' . $cell)->applyFromArray($styleArray);

            $sheet_1->setCellValue('I' . $cell, $exam_result['total_failed']); //failed class
            $sheet_1->getStyle('I' . $cell)->applyFromArray($styleArray);

            $not_attent = intval($done) - intval($exam_result['total_passed']);
            $not_attent -= intval($exam_result['total_failed']);

            $sheet_1->setCellValue('J' . $cell, $not_attent); //not attent exam
            $sheet_1->getStyle('J' . $cell)->applyFromArray($styleArray);

            $sheet_1->getStyle('A' . $cell)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet_1->getStyle('A' . $cell)
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet_1->getStyle('J' . $cell)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet_1->getStyle('J' . $cell)
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet_1->getStyle('I' . $cell)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet_1->getStyle('I' . $cell)
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $students += $class->students;
            $drops += $dropped_students;
            $grads += $done;
            $male += intval($gender['male']);
            $female += intval($gender['female']);
            $total_passed += $exam_result['total_passed'];
            $total_failed += $exam_result['total_failed'];
            $total_not_attent += $not_attent;
            $counter++;
            $cell++;
        }

        //$sheet_1->mergeCells("A$cell:A" . ($cell + 1));
        //$sheet_1->mergeCells("B$cell:B" . ($cell + 1));
        $sheet_1->mergeCells("C$cell:C" . ($cell + 1));
        $sheet_1->mergeCells("D$cell:D" . ($cell + 1));
        $sheet_1->mergeCells("E$cell:E" . ($cell + 1));
        $sheet_1->mergeCells("F" . ($cell + 1) . ":G" . ($cell + 1));
        $sheet_1->mergeCells("H$cell:H" . ($cell + 1));
        $sheet_1->mergeCells("I$cell:I" . ($cell + 1));
        $sheet_1->mergeCells("J$cell:J" . ($cell + 1));

        $sheet_1->mergeCells("A" . $cell . ":B" . ($cell + 1));

        $sheet_1->setCellValue('A' . $cell, 'JEMI:');
        $sheet_1->getStyle("A" . $cell . ":B" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('C' . $cell, $students);
        $sheet_1->getStyle("C$cell:C" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('D' . $cell, $drops);
        $sheet_1->getStyle("D$cell:D" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('E' . $cell, $grads);
        $sheet_1->getStyle("E$cell:E" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('F' . $cell, $female);
        $sheet_1->getStyle("F$cell")->applyFromArray($styleArray);

        $sheet_1->setCellValue('G' . $cell, $male);
        $sheet_1->getStyle('G' . $cell)->applyFromArray($styleArray);

        $sheet_1->setCellValue('F' . ($cell + 1), ($female + $male));
        $sheet_1->getStyle("F" . ($cell + 1) . ":G" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('H' . $cell, $total_passed); //total of student who passed class
        $sheet_1->getStyle("H$cell:H" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('I' . $cell, $total_failed); //total of student who failed class
        $sheet_1->getStyle("I$cell:I" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->setCellValue('J' . $cell, $total_not_attent); //total of student who not attent exam
        $sheet_1->getStyle("J$cell:J" . ($cell + 1))->applyFromArray($styleArray);

        $sheet_1->getStyle('A' . $cell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet_1->getStyle('A' . $cell)
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet_1->getStyle('J' . $cell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet_1->getStyle('J' . $cell)
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet_1->getStyle('I' . $cell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet_1->getStyle('I' . $cell)
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet_1->getStyle('A1:J' . ($cell + 1))
                ->applyFromArray($styleArray);

        /*         * **************************FIRST PAGE IS DONE HERE************************** */

        return $sheet_1;
    }

    /**

     * Method to get Students count with related data
     * @param type $spreadsheet
     * @param type $category
     * @param type $classes
     * @return object spreadsheet sheets     /
     */
    private function get_second_sheet($spreadsheet, $category, $classes) {
        $sheet = $spreadsheet->createSheet();

        $sheet->setTitle("JEMI OKUWCY SANAWY");

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        //set font style and size
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        $sheet->getStyle('A1:A2')
                ->getFont()
                ->setSize(14);

        // merging cells
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        //bordering cells
        $sheet->getStyle('A1:F1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:F2')->applyFromArray($styleArray);

        //alignment settings here
        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setWrapText(true);

        //setting row and coluns sizes
        $sheet->getRowDimension(3)->setRowHeight(20);

        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);

        //setting cells values for header part
        $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
        $sheet->setCellValue('A2', $category->start_date . ' - ' . $category->end_date . "($category->number)"); //course period 28.08.2017 - 18.11.2017(1)

        $sheet->setCellValue('A3', '№');
        $sheet->getStyle('A3')->applyFromArray($styleArray);
        $sheet->setCellValue('B3', 'Şertnama №'); //order number
        $sheet->getStyle('B3')->applyFromArray($styleArray);
        $sheet->setCellValue('C3', 'Ady we Familiýasy'); //first and last names
        $sheet->getStyle('C3')->applyFromArray($styleArray);
        $sheet->setCellValue('D3', 'Atasynyň ady'); //Fathers name
        $sheet->getStyle('D3')->applyFromArray($styleArray);
        $sheet->setCellValue('E3', 'Kursy'); //subject
        $sheet->getStyle('E3')->applyFromArray($styleArray);
        $sheet->setCellValue('F3', 'Synpy'); //Class
        $sheet->getStyle('F3')->applyFromArray($styleArray);

        /*         * *****************Start setting cells (excel body)****************** */
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(false);

        $row = 4;
        $counter = 1;
        foreach ($classes->result() as $class) {
            $class_students = $this->classes_model->get_students_from_class_only($class->ID);
            foreach ($class_students->result() as $class_student) {
                $sheet->setCellValue("A$row", $counter);
                $sheet->getStyle("A$row")->applyFromArray($styleArray);

                $sheet->setCellValue("B$row", "$class_student->agreement_number"); //order number
                $sheet->getStyle("B$row")->applyFromArray($styleArray);

                $sheet->getStyle("C$row")->applyFromArray($styleArray);
                $sheet->setCellValue("C$row", "$class_student->first_name $class_student->last_name"); //first and last names

                $sheet->getStyle("D$row")->applyFromArray($styleArray);
                $sheet->setCellValue("D$row", "$class_student->fathers_name"); //fathers name

                $sheet->getStyle("E$row")->applyFromArray($styleArray);
                $sheet->setCellValue("E$row", "$class->subject_name"); //subject name

                $sheet->getStyle("F$row")->applyFromArray($styleArray);
                $sheet->setCellValue("F$row", "$class->name"); //class name

                $counter++;
                $row++;
            }
        }

        return $sheet;
    }

    /**

     * Method to get dropped students count with related data
     * @param type $spreadsheet
     * @param type $category
     * @param type $classes
     * @return object spreadsheet sheets     /
     */
    private function get_third_sheet($spreadsheet, $category, $classes) {
        $sheet = $spreadsheet->createSheet();

        $sheet->setTitle("KURSDAN CYKAN DINLEYJILER");

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        //set font style and size
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        $sheet->getStyle('A1:A2')
                ->getFont()
                ->setSize(14);

        // merging cells
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        //bordering cells
        $sheet->getStyle('A1:F1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:F2')->applyFromArray($styleArray);
        $sheet->getStyle('A3:F3')->applyFromArray($styleArray);

        //alignment settings here
        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setWrapText(true);

        //setting row and coluns sizes
        $sheet->getRowDimension(3)->setRowHeight(20);

        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);


        //setting cells values for header part
        $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
        $sheet->setCellValue('A2', $category->start_date . ' - ' . $category->end_date . "($category->number)"); //course period 28.08.2017 - 18.11.2017(1)
        $sheet->setCellValue('A3', 'Kursdan çykan diňleýjiler');

        $sheet->setCellValue('A4', '№');
        $sheet->getStyle('A4')->applyFromArray($styleArray);
        $sheet->setCellValue('B4', 'Şertnama №'); //order number
        $sheet->getStyle('B4')->applyFromArray($styleArray);
        $sheet->setCellValue('C4', 'Ady we Familiýasy'); //first and last names
        $sheet->getStyle('C4')->applyFromArray($styleArray);
        $sheet->setCellValue('D4', 'Atasynyň ady'); //Fathers name
        $sheet->getStyle('D4')->applyFromArray($styleArray);
        $sheet->setCellValue('E4', 'Kursy'); //subject
        $sheet->getStyle('E4')->applyFromArray($styleArray);
        $sheet->setCellValue('F4', 'Synpy'); //Class
        $sheet->getStyle('F4')->applyFromArray($styleArray);

        /*         * *****************Start setting cells (excel body)****************** */
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(false);

        $row = 5;
        $counter = 1;
        foreach ($classes->result() as $class) {
            $class_students = $this->students_model->get_all_dropped_students_by_class($class->ID);
            foreach ($class_students->result() as $class_student) {
                $sheet->setCellValue("A$row", $counter);
                $sheet->getStyle("A$row")->applyFromArray($styleArray);

                $sheet->setCellValue("B$row", "$class_student->agreement_number"); //order number
                $sheet->getStyle("B$row")->applyFromArray($styleArray);

                $sheet->getStyle("C$row")->applyFromArray($styleArray);
                $sheet->setCellValue("C$row", "$class_student->first_name $class_student->last_name"); //first and last names

                $sheet->getStyle("D$row")->applyFromArray($styleArray);
                $sheet->setCellValue("D$row", "$class_student->fathers_name"); //fathers name

                $sheet->getStyle("E$row")->applyFromArray($styleArray);
                $sheet->setCellValue("E$row", "$class->subject_name"); //subject name

                $sheet->getStyle("F$row")->applyFromArray($styleArray);
                $sheet->setCellValue("F$row", "$class->name"); //class name

                $counter++;
                $row++;
            }
        }
    }

    /**

     * Method to get sheet of certificate info
     * @param type $spreadsheet
     * @param type $category
     * @param type $classes     /
     */
    private function get_fourth_sheet($spreadsheet, $category, $classes) {
        $sheet = $spreadsheet->createSheet();

        $sheet->setTitle("SADATNAMA ALANLARYN SANAVY");

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        //set font style and size
        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(true)
                ->setName('Times New Roman')
                ->setSize(12);

        $sheet->getStyle('A1:A2')
                ->getFont()
                ->setSize(14);

        // merging cells
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        //bordering cells
        $sheet->getStyle('A1:I1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:I2')->applyFromArray($styleArray);
        $sheet->getStyle('A3:I3')->applyFromArray($styleArray);

        //alignment settings here
        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $spreadsheet->getDefaultStyle()
                ->getAlignment()
                ->setWrapText(true);

        //setting row and coluns sizes
        $sheet->getRowDimension(3)->setRowHeight(20);

        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);

        //setting cells values for header part
        $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
        $sheet->setCellValue('A2', $category->start_date . ' - ' . $category->end_date . "($category->number)"); //course period 28.08.2017 - 18.11.2017(1)
        $sheet->setCellValue('A3', 'Şahadatnama alanlaryň sanawy');

        $sheet->setCellValue('A4', '№');
        $sheet->getStyle('A4')->applyFromArray($styleArray);

        $sheet->setCellValue('B4', 'Şertnama №'); //order number
        $sheet->getStyle('B4')->applyFromArray($styleArray);

        $sheet->setCellValue('C4', 'Ady'); //first name
        $sheet->getStyle('C4')->applyFromArray($styleArray);

        $sheet->setCellValue('D4', 'Familiýasy'); //last name
        $sheet->getStyle('D4')->applyFromArray($styleArray);

        $sheet->setCellValue('E4', 'Atasynyň ady'); //Fathers name
        $sheet->getStyle('E4')->applyFromArray($styleArray);

        $sheet->setCellValue('F4', 'Sertifikat №'); //Certificate no
        $sheet->getStyle('F4')->applyFromArray($styleArray);

        $sheet->setCellValue('G4', 'Kursy'); //Subject
        $sheet->getStyle('G4')->applyFromArray($styleArray);

        $sheet->setCellValue('H4', 'Berlen senesi'); //Issued Date
        $sheet->getStyle('H4')->applyFromArray($styleArray);

        $sheet->setCellValue('I4', 'Goly'); //Signature
        $sheet->getStyle('I4')->applyFromArray($styleArray);

        $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setBold(false);

        $row = 5;
        $counter = 1;
        foreach ($classes->result() as $class) {

            $class_students = $this->classes_model->get_students_from_class_only($class->ID);
            foreach ($class_students->result() as $class_student) {

                $grades = $this->classes_model->get_student_total_grade($class_student->userid);

                $total_grade = 0;
                $final_grade = 0;
                $avarage = 0;
                foreach ($grades->result() as $grade) {
                    $x = ($grade->mark * $grade->weighting) / $grade->max_mark;
                    $total_grade += $x;
                    $avarage += intval($grade->mark);

                    if (intval($grade->type) === 1) {
                        $final_grade += $grade->mark;
                    }
                }

                $letter_grades = $this->classes_model->get_class_grades_all($class->ID);
                $max_score = 100;
                foreach ($letter_grades->result() as $r) {
                    if ($max_score > $r->max_score) {
                        $max_score = $r->max_score;
                    }
                }

                if ($total_grade > $max_score) {
                    $sheet->setCellValue("A$row", $counter);
                    $sheet->getStyle("A$row")->applyFromArray($styleArray);

                    $sheet->setCellValue("B$row", "$class_student->agreement_number"); //order number
                    $sheet->getStyle("B$row")->applyFromArray($styleArray);

                    $sheet->getStyle("C$row")->applyFromArray($styleArray);
                    $sheet->setCellValue("C$row", "$class_student->first_name"); //first name

                    $sheet->getStyle("D$row")->applyFromArray($styleArray);
                    $sheet->setCellValue("D$row", "$class_student->last_name"); //last name

                    $sheet->getStyle("E$row")->applyFromArray($styleArray);
                    $sheet->setCellValue("E$row", "$class_student->fathers_name"); //fathers name

                    $sheet->getStyle("F$row")->applyFromArray($styleArray);
                    $sheet->setCellValue("F$row", "CERT-$counter"); //certificate no

                    $sheet->setCellValue("G$row", "$class->subject_name"); //Subject
                    $sheet->getStyle("G$row")->applyFromArray($styleArray);

                    $sheet->setCellValue("H$row", '00/00/0000'); //Issued Date
                    $sheet->getStyle("H$row")->applyFromArray($styleArray);

                    $sheet->setCellValue("I$row", '*******'); //Signature
                    $sheet->getStyle("I$row")->applyFromArray($styleArray);

                    $counter++;
                    $row++;
                }
            }
        }
    }

    /**

     * Method to get sheet of class exam results
     * @param type $spreadsheet
     * @param type $category
     * @param type $classes     /
     */
    private function get_fifth_sheet($spreadsheet, $category, $classes) {
        $sheet_counter = 1;
        foreach ($classes->result() as $class) {
            $sheet = $spreadsheet->createSheet();

            $sheet->setTitle("$class->name SYNAG NETIJELERI");

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            //set font style and size
            $spreadsheet->getDefaultStyle()
                    ->getFont()
                    ->setBold(true)
                    ->setName('Times New Roman')
                    ->setSize(12);

            $sheet->getStyle('A1:A2')
                    ->getFont()
                    ->setSize(14);

            // merging cells
            $sheet->mergeCells('A1:I1');
            $sheet->mergeCells('A2:I2');
            $sheet->mergeCells('A3:I3');

            //bordering cells
            $sheet->getStyle('A1:I1')->applyFromArray($styleArray);
            $sheet->getStyle('A2:I2')->applyFromArray($styleArray);
            $sheet->getStyle('A3:I3')->applyFromArray($styleArray);

            //alignment settings here
            $spreadsheet->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getDefaultStyle()
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $spreadsheet->getDefaultStyle()
                    ->getAlignment()
                    ->setWrapText(true);

            //setting row and coluns sizes
            $sheet->getRowDimension(3)->setRowHeight(20);

            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(15);

            //setting cells values for header part
            $sheet->setCellValue('A1', '"DAÝAN" HUSUSY KÄRHANASY');
            $sheet->setCellValue('A2', $category->start_date . ' - ' . $category->end_date . "($category->number)"); //course period 28.08.2017 - 18.11.2017(1)
            $sheet->setCellValue('A3', "$class->name synplarynyň synag netijeleri");

            $sheet->setCellValue('A4', '№');
            $sheet->getStyle('A4')->applyFromArray($styleArray);

            $sheet->setCellValue('B4', 'Şertnama №'); //order number
            $sheet->getStyle('B4')->applyFromArray($styleArray);

            $sheet->setCellValue('C4', 'Ady we Familiýasy'); //first and last name
            $sheet->getStyle('C4')->applyFromArray($styleArray);

            $sheet->setCellValue('D4', 'Atasynyň ady'); //fathers name
            $sheet->getStyle('D4')->applyFromArray($styleArray);

            $sheet->setCellValue('E4', 'Synpy'); //Class
            $sheet->getStyle('E4')->applyFromArray($styleArray);

            $sheet->setCellValue('F4', 'Ortaça bahasy'); //Average point
            $sheet->getStyle('F4')->applyFromArray($styleArray);

            $sheet->setCellValue('G4', 'Tamamlaýjy synagy'); //Final exam
            $sheet->getStyle('G4')->applyFromArray($styleArray);

            $sheet->setCellValue('H4', 'Jemi'); //Total
            $sheet->getStyle('H4')->applyFromArray($styleArray);

            $sheet->setCellValue('I4', 'Netije'); //Result
            $sheet->getStyle('I4')->applyFromArray($styleArray);

            $spreadsheet->getDefaultStyle()
                    ->getFont()
                    ->setBold(false);

            $class_students = $this->classes_model->get_students_from_class_only($class->ID);
            $row = 5;
            $counter = 1;
            foreach ($class_students->result() as $class_student) {

                $grades = $this->classes_model->get_student_total_grade($class_student->userid);

                $total_grade = 0;
                $final_grade = 0;
                $avarage = 0;
                foreach ($grades->result() as $grade) {
                    $x = ($grade->mark * $grade->weighting) / $grade->max_mark;
                    $total_grade += $x;
                    $avarage += intval($grade->mark);

                    if (intval($grade->type) === 1) {
                        $final_grade += $grade->mark;
                    }
                }

                $i = $this->classes_model->get_class_assignments_count($class->ID);
                if ($i > 0) {
                    $avarage /= $i;
                }

                $letter_grades = $this->classes_model->get_class_grades_all($class->ID);
                foreach ($letter_grades->result() as $r) {
                    $grades_arr[] = array(
                        "min_score" => $r->min_score,
                        "max_score" => $r->max_score,
                        "grade" => $r->grade
                    );
                }

                $total_letter_grade = lang("ctn_870");
                // Get grade
                foreach ($grades_arr as $grade) {
                    if ($total_grade >= $grade['min_score'] && $total_grade <= $grade['max_score']) {
                        $total_letter_grade = $grade['grade'];
                    }
                }

                $sheet->setCellValue("A$row", $counter);
                $sheet->getStyle("A$row")->applyFromArray($styleArray);

                $sheet->setCellValue("B$row", "$class_student->agreement_number"); //order number
                $sheet->getStyle("B$row")->applyFromArray($styleArray);

                $sheet->setCellValue("C$row", "$class_student->first_name $class_student->last_name"); //first and last name
                $sheet->getStyle("C$row")->applyFromArray($styleArray);

                $sheet->setCellValue("D$row", "$class_student->fathers_name"); //fathers name
                $sheet->getStyle("D$row")->applyFromArray($styleArray);

                $sheet->setCellValue("E$row", "$class->name"); //Class
                $sheet->getStyle("E$row")->applyFromArray($styleArray);

                $sheet->setCellValue("F$row", "$avarage"); //Average point
                $sheet->getStyle("F$row")->applyFromArray($styleArray);

                $sheet->setCellValue("G$row", "$final_grade"); //Final exam
                $sheet->getStyle("G$row")->applyFromArray($styleArray);

                $sheet->setCellValue("H$row", "$total_grade"); //Total
                $sheet->getStyle("H$row")->applyFromArray($styleArray);

                $sheet->setCellValue("I$row", "$total_letter_grade"); //Result
                $sheet->getStyle("I$row")->applyFromArray($styleArray);
            }
        }
    }

    /**

     * Method to get counts of male and female students
     * @param type $class_id
     * @return array Male and Female count     /
     */
    private function count_male_and_female($class_id) {
        $data = array();
        $male_counter = 0;
        $female_counter = 0;

        $class_students = $this->classes_model->get_students_from_class_only($class_id);
        foreach ($class_students->result() as $class_student) {
            $user_sex = $this->user_model->get_student_genger($class_student->userid);
            $flag = false;
            if ($user_sex->num_rows() > 0) {
                $flag = true;
            }

            $gender = $user_sex->row();

            if ($flag && $gender->value === 'Male') {
                $male_counter++;
            }

            if ($flag && $gender->value === 'Female') {
                $female_counter++;
            }
        }

        $dropped_class_students = $this->students_model->get_all_dropped_students_by_class($class_id);
        foreach ($dropped_class_students->result() as $class_student) {
            $user_sex = $this->user_model->get_student_genger($class_student->ID);
            $flag = false;
            if ($user_sex->num_rows() > 0) {
                $flag = true;
            }

            $gender = $user_sex->row();

            if ($flag && $gender->value === 'Male') {
                $male_counter++;
            }

            if ($flag && $gender->value === 'Female') {
                $female_counter++;
            }
        }

        $data['male'] = $male_counter;
        $data['female'] = $female_counter;

        return $data;
    }

    private function calculate_total_passed_and_failed($class_id) {
        $data = array();
        $passed = 0;
        $failed = 0;
        $class_students = $this->classes_model->get_students_from_class_only($class_id);
        foreach ($class_students->result() as $class_student) {
            $grades = $this->classes_model->get_student_total_grade($class_student->userid);
            $total_grade = 0;
            foreach ($grades->result() as $grade) {
                $x = ($grade->mark * $grade->weighting) / $grade->max_mark;
                $total_grade += $x;
            }

            $letter_grades = $this->classes_model->get_class_grades_all($class_id);
            $max_score = 100;
            foreach ($letter_grades->result() as $r) {
                if ($max_score > $r->max_score) {
                    $max_score = $r->max_score;
                }
            }

            if ($total_grade < $max_score) {
                $failed++;
            } else {
                $passed++;
            }
        }

        $data['total_passed'] = $passed;
        $data['total_failed'] = $failed;

        return $data;
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

            // Saving the document as WORD 2007 file...
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
            $objWriter->save($filename);

            $this->download($filename);
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

            // Saving the document as WORD 2007 file...
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
            $objWriter->save($filename);

            $this->download($filename);
        }
    }

    private function download($filename) {

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

        exit();
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

    /* private function printSeparator(Section $section) {
      $lineStyle = array('weight' => 2, 'width' => 450, 'height' => 1, 'align' => 'center');
      $section->addLine($lineStyle);
      } */

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
