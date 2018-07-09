<!DOCTYPE html>
<?php if ($enable_rtl) : ?>
    <html dir="rtl">
    <?php else : ?>
        <html lang="en">
        <?php endif; ?>
        <head>
        <head>
            <title><?php echo $this->settings->info->site_name ?></title>         
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <!-- CODE INCLUDES -->
            <link href="<?php echo base_url(); ?>styles/invoice_dayan_original_pdf.css" rel="stylesheet" type="text/css">
        </head>
        <body>
            <div class="document">
                <div style="width:210mm; height:150mm;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%" style="padding-right:10px;">
                                <table width="100%">
                                    <tr>
                                        <td width="80%">
                                            <h1>Daýan hususy kärhanasy</h1>
                                            <b>kärhana, gurama</b>
                                            <br>
                                            <small>предприятие организация</small>
                                        </td>
                                        <td valign="top" style="text-align:right;">
                                            NKO-1 Forma<br>
                                            форма N KO-1
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <h1 style="border-bottom:0px;">GIRDEJI KASSA ORDERI N <div class="box"><?php echo $invoice->invoice_id ?></div></h1>
                                <small>Приходный кассовой ордер</small>
                                <div class="clear"></div>
                                <table width="40%" class="border" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="border-top:1px solid black;">Güni<br><small>цисло</small></td>
                                        <td style="border-top:1px solid black;">Aýy<br><small>месяц</small></td>
                                        <td style="border:0px;"></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo date("d", $invoice->timestamp) ?></td>
                                        <td><?php echo date("m", $invoice->timestamp) ?></td>
                                        <td style="border:0px;"><?php echo date("Y", $invoice->timestamp) ?> ý</td>
                                    </tr>
                                </table>
                                <table width="100%" style="border-top:1px solid black; margin-top:5px;" class="border" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="10%"><span style="color:white;">.</span></td>
                                        <td style="width:18%; font-size:14px;">Başga ýurtdaky hasap, goşmaça hasap<br><small>Корреспондирующий счет, субсчет</small></td>
                                        <td style="width:18%; font-size:14px;">Analitiki hasaba almagyň şifri<br><small>Шифир аналитического учета</small></td>
                                        <td style="width:18%; font-size:14px;">Jemi pul<br><small>Сумма</small></td>
                                        <td style="width:18%; font-size:14px;">Maksatly ulanylyşyň şifri<br><small>Шифир целевого назначения</small></td>
                                    </tr>
                                    <tr>
                                        <td><span style="color:white;">.</span></td>
                                        <td><span style="color:white;">.</span></td>
                                        <td><span style="color:white;">.</span></td>
                                        <td>VS_TOLENEN</td>
                                        <td><span style="color:white;">.</span></td>
                                    </tr>
                                </table>
                                <br>
                                <table class="padding" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:30%; text-align:left; border-bottom:1px solid black;">
                                            Alyndy
                                        </td>
                                        <td style="text-align:left; border-bottom:1px solid black;">
                                            <?php if(!empty($invoice->client_first_name)) : ?><strong><?php echo $invoice->client_first_name ?> <?php echo $invoice->client_last_name ?></strong><br /><?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;"><small>Принято от</small></td>
                                    </tr>

                                    <tr>
                                        <td style="width:30%; text-align:left; border-bottom:1px solid black;">
                                            Esas
                                        </td>
                                        <td style="text-align:left; border-bottom:1px solid black;">
                                            <?php echo $invoice->title ?> <b>okuwy üçin</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;"><small>Основание</small></td>
                                    </tr>
                                </table>
                                <table class="padding" cellpadding="0" cellspacing="0" width="100%">
                                    <tr valign="bottom">
                                        <td style="text-align:center; width:80%; border-bottom:1px solid black; font-weight:bold;">VS_SOZBILEN</td>
                                        <td style="width:100px; text-align:center; padding:0px;">man.</td>
                                        <td style="text-align:center; width:50px; border-bottom:1px solid black;">00</td>
                                        <td style="text-align:center; padding:0px; width:50px;">teň.</td>
                                    </tr>
                                    <tr valign="top">
                                        <td style="text-align:left; width:80%;"></td>
                                        <td style="width:100px; text-align:center; padding:0px;"><small>ман</small></td>
                                        <td style="text-align:left; width:50px;"></td>
                                        <td style="text-align:center; padding:0px;"><small>тен.</small></td>
                                    </tr>
                                </table>
                                <table class="padding" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:30%; text-align:left; border-bottom:1px solid black;">
                                            Goşmaça
                                        </td>
                                        <td style="text-align:left; border-bottom:1px solid black;">
                                            <span style="color:white;">.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;"><small>Приложение</small></td>
                                    </tr>
                                </table>
                                <br>
                                <table style="border-left:2px solid black;" cellpadding="5">
                                    <tr>
                                        <td style="text-align:left;">Baş (uly) hasapçy<br><small>Главный (старший) бухгалтер</small></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;">Alan kassir<br><small>Получил кассир</small></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="border-right:1px solid black; border-left:1px solid black; width:1%;">
                                <span style="color:white;">.</span>
                            </td>
                            <td width="40%" valign="top">

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h1>Daýan hususy kärhanasy</h1>
                                            <b>kärhana, gurama</b>
                                            <br>
                                            <small>предприятие организация</small>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <h1 style="border-bottom:0px;">GIRDEJI KASSA ORDERINIŇ<br>KWITANSIÝASY N <div class="box" style="border:0px; border-bottom:1px solid black;"><?php echo $invoice->invoice_id ?></div></h1>
                                <small>Квитанция к приходному кассовому ордеру</small>
                                <br><br><br>
                                <table class="padding" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="width:30%; text-align:left; border-bottom:1px solid black;">
                                            Alyndy
                                        </td>
                                        <td style="text-align:left; border-bottom:1px solid black;">
                                            <?php if(!empty($invoice->client_first_name)) : ?><strong><?php echo $invoice->client_first_name ?> <?php echo $invoice->client_last_name ?></strong><br /><?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;"><small>Принято от</small></td>
                                    </tr>

                                    <tr>
                                        <td style="width:30%; text-align:left; border-bottom:1px solid black;">
                                            Esas
                                        </td>
                                        <td style="text-align:left; border-bottom:1px solid black;">
                                            <?php echo $invoice->title ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;"><small>Основание</small></td>
                                    </tr>
                                    <tr>
                                        <td colspan=2 style="text-align:left; border-bottom:1px solid black;"><span style="color:white;">.</span></td>
                                    </tr>
                                    <tr>
                                        <td colspan=2 style="text-align:center; border-bottom:1px solid black; font-weight:bold;">VS_SOZBILEN</td>
                                    </tr>
                                    <tr>
                                        <td colspan=2 style="text-align:center; border-bottom:1px solid black; padding-bottom:20px;">söz bilen <small style="padding-left:10px;">прописью</small></td>
                                    </tr>

                                </table>
                                <table class="padding" cellpadding="0" cellspacing="0" width="100%">
                                    <tr valign="bottom">
                                        <td style="text-align:center; width:80%; border-bottom:1px solid black; font-weight:bold;">VS_TOLENEN</td>
                                        <td style="width:100px; text-align:center; padding:0px;">man.</td>
                                        <td style="text-align:center; width:50px; border-bottom:1px solid black;">00</td>
                                        <td style="text-align:center; padding:0px; width:50px;">teň.</td>
                                    </tr>
                                    <tr valign="top">
                                        <td style="text-align:left; width:80%;"></td>
                                        <td style="width:100px; text-align:center; padding:0px;"><small>ман</small></td>
                                        <td style="text-align:left; width:50px;"></td>
                                        <td style="text-align:center; padding:0px;"><small>тен.</small></td>
                                    </tr>
                                </table>
                                <table style="width:60%; margin-top:20px; float:right;" class="padding">
                                    <tr>
                                        <td style="border-bottom:1px solid black;">
                                            <?php echo date("d", $invoice->timestamp) ?>
                                        </td>
                                        <td style="border-bottom:1px solid black;">
                                            <?php echo date("m", $invoice->timestamp) ?>
                                        </td>
                                        <td style="border-bottom:1px solid black;">
                                            <?php echo date("Y", $invoice->timestamp) ?> ý
                                        </td>
                                    </tr>
                                </table>
                                <div class="clear"></div>
                                <br>
                                <table cellpadding="10">
                                    <tr>
                                        <td style="text-align:left;">M.Ýe.</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;">Baş (uly) hasapçy<br><small>Главный (старший) бухгалтер</small></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left;">Alan kassir<br><small>Получил кассир</small></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </body>
    </html>