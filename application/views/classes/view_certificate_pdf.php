<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $this->settings->info->site_name ?></title>         
        <meta charset="UTF-8" />
        <link href="<?php echo base_url(); ?>styles/certificate.css" rel="stylesheet" type="text/css">


        <!-- CODE INCLUDES -->
    </head>
    <body>
        <div class="document">
            <div class="wrapper">
                <p>
                    Şu şadatnama <b class="text-underline"> <?php echo "$student->first_name $student->last_name $student->fathers_name "; ?> </b>
                    onuň hakykatdan hem <?php echo $period_tm; ?> çenli
                    <b class="text-underline"> DAÝAN hususy kärhanasynda <?php echo strtoupper("$subject->name ($class->name) ") ; ?> </b>
                    <?php echo "$class->hrs"; ?> akademik sagatlyk kursyny <?php echo $total_grade; ?>%* bal bilen üstünlikli tamamlandygy barada berildi.
                </p>
                <p>
                    This is to certify that <b class="text-underline"> <?php echo "$student->first_name_en $student->last_name_en $student->fathers_name_en "; ?> </b>
                    has studied at <b class="text-underline"> DAYAN individual enterprise </b> from <?php echo $period_en; ?> having successfully completed <?php echo "$class->hrs"; ?> academic hours of <b class="text-underline"> <?php echo strtoupper("$subject->name ($class->name) ") ; ?> </b>
                    course with a score of <?php echo $total_grade; ?>%*, has been awarded with this certificate with all rights, honor
                    and privileges.
                </p>
            </div>
        </div>
    </body>
</html>