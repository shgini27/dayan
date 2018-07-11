<!DOCTYPE>
<html lang="en">
    <head>
        <?php $theme = 'default';
        $page_name = 'home';
        $school_title = 'Dayan'; ?>
        <title>Front test</title>
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" href="assets/images/favicon.png">
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/img/icons/apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" type="text/css" media="print" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/print.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/magnific-popup.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/owl.carousel.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/owl.theme.default.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/css/responsive.css">

        <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,400i,600,600i,700,700i|Lato:400,700" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/js/vendor/jquery-2.1.4.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkSQBRz8ELzVHMLW8hBYEWByd_icFPQho"></script>
        <script src="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/js/gmap3.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/frontend/<?php echo $theme; ?>/js/vendor/modernizr-2.8.3.min.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php
        $header_logo = 'dayan.png';
        ?>
        <header>
            <div class="logo-area">
                <div class="container">
                    <div class="row justify-content-md-center">
                        <div class="col">
                            <div class="logo-container text-center">
                                <a href="<?php echo base_url(); ?>index.php?home">
                                    <img src="<?php echo base_url(); ?>uploads/frontend/<?php echo $header_logo; ?>" alt="">
                                    <h2><?php echo $school_title; ?></h2>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-area">
                <div class="container">
                    <nav class="navbar navbar-expand-lg">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-md-center" id="navbarSupportedContent">
                            <ul class="navbar-nav ">
                                <li class="nav-item <?php if ($page_name == 'home') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home">
<?php echo 'Home'; ?>
                                    </a>
                                </li>
                                <!--<li class="nav-item <?php if ($page_name == 'noticeboard' || $page_name == 'notice_details') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/noticeboard">
<?php echo 'Noticeboard'; ?>
                                    </a> -->
                                </li>
                                <li class="nav-item <?php if ($page_name == 'event') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/events">
<?php echo 'Events'; ?>
                                    </a>
                                </li>
                                <!-- <li class="nav-item <?php if ($page_name == 'teacher') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/teachers">
<?php echo 'Teachers'; ?>
                                    </a>
                                </li>-->
                                <li class="nav-item <?php if ($page_name == 'gallery' || $page_name == 'gallery_view') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/gallery">
<?php echo 'Gallery'; ?>
                                    </a>
                                </li>
                                <!--<li class="nav-item <?php if ($page_name == 'admission') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/admission">
<?php echo 'Admission'; ?>
                                    </a>
                                </li>-->
                                <li class="nav-item <?php if ($page_name == 'about') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/about">
<?php echo 'About'; ?>
                                    </a>
                                </li>
                                <li class="nav-item <?php if ($page_name == 'contact') echo 'active'; ?>">
                                    <a class="nav-link" href="<?php echo base_url(); ?>index.php?home/contact">
<?php echo 'Contact'; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <?php echo $content ?>

        <?php
        $footer_logo = 'dayan.png';
        $social = '[{"facebook":"http://facebook.com","twitter":"http://twitter.com","linkedin":"http://linkedin.com","google":"http://google.com","youtube":"http://youtube.com","instagram":"http://instagram.com"}]';
        $links = json_decode($social);
        ?>
        <footer>
            <div class="footer-widget-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                            <div class="footer-widget info-widget text-right">
                                <ul>
                                    <li class="address">
                                        <?php echo 'address'; ?><i class="fa fa-map-marker"></i>
                                    </li>
                                    <li class="phone">
                                        <?php echo 'phone'; ?><i class="fa fa-phone"></i>
                                    </li>
                                    <li class="fax">
                                        <?php echo 'fax'; ?><i class="fa fa-fax"></i></li>
                                    <li class="email">
<?php echo 'email'; ?><i class="fa fa-envelope"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="footer-widget logo-widget text-center">
                                <div class="footer-logo-container">
                                    <img src="<?php echo base_url(); ?>uploads/frontend/<?php echo $header_logo; ?>" alt="">
                                </div>
                                <div class="footer-socials">
                                    <ul>
                                        <?php if ($links[0]->facebook != '') { ?>
                                            <li><a href="<?php echo $links[0]->facebook; ?>" target="_blank"><i class="fa fa-facebook-official"></i></a></li>
                                        <?php } ?>
                                        <?php if ($links[0]->twitter != '') { ?>
                                            <li><a href="<?php echo $links[0]->twitter; ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                        <?php } ?>
                                        <?php if ($links[0]->linkedin != '') { ?>
                                            <li><a href="<?php echo $links[0]->linkedin; ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                                        <?php } ?>
                                        <?php if ($links[0]->google != '') { ?>
                                            <li><a href="<?php echo $links[0]->google; ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                                        <?php } ?>
                                        <?php if ($links[0]->youtube != '') { ?>
                                            <li><a href="<?php echo $links[0]->youtube; ?>" target="_blank"><i class="fa fa-youtube"></i></a></li>
                                        <?php } ?>
                                        <?php if ($links[0]->instagram != '') { ?>
                                            <li><a href="<?php echo $links[0]->instagram; ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>
<?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="footer-widget link-widget">
                                <ul>
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php?home/privacy_policy">
<?php echo 'Privacy Policy'; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php?home/terms_conditions">
<?php echo 'Terms & Conditions'; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php?home/contact">
<?php echo 'Contact Us'; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php/home/" target="_blank">
<?php echo 'Admin'; ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-area">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="copyright-text text-center">
                                <p><?php echo 'copyright 2018 Dayan'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <script src="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/js/vendor/popper.min.js"></script>
<script src="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/js/owl.carousel.min.js"></script>
<script src="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/js/scripts.js"></script>
<script src="<?php echo base_url();?>assets/frontend/<?php echo $theme;?>/js/jquery.magnific-popup.min.js"></script>
    </body>
</html>

