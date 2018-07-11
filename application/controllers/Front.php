<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Front
 *
 * @author Shagy
 */
class Front extends CI_Controller {
    var $CI = null;
    public function __construct() {
        parent::__construct();
        if (defined('REQUEST') && REQUEST == "external") {
            return;
        }
        $this->CI =& get_instance();
    }
    
    public function index() {
        
        /*$this->template->loadExternal(
                '<link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
                <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
                <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>'
        );*/
        
        $this->template->loadFrontContent("front/home/index.php", ["front" => "Welcome to Front"]);
    }
}
