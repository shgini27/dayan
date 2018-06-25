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
                    onuň hakykatdan hem 2018-nji ýylyň 14-nji fewralyndan 2018-nji ýylyň 7-nji maýyna çenli
                    <b class="text-underline"> DAÝAN hususy kärhanasynda <?php echo strtoupper("$subject->name ($class->name) ") ; ?> </b>
                    <?php echo "$category->hrs"; ?> akademik sagatlyk kursyny 74%* bal bilen üstünlikli tamamlandygy barada berildi.
                </p>
                <p>
                    This is to certify that <b class="text-underline"> <?php echo "$student->first_name $student->last_name $student->fathers_name "; ?> </b>
                    has studied at <b class="text-underline"> DAYAN individual enterprise </b> from 14th of February 2018 to 7th of 
                    May 2018 having successfully completed <?php echo "$category->hrs"; ?> academic hours of <b class="text-underline"> <?php echo strtoupper("$subject->name ($class->name) ") ; ?> </b>
                    course with a score of 74%*, has been awarded with this certificate with all rights, honor
                    and privileges.
                </p>
            </div>
        </div>
    </body>
</html>