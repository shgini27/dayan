<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QueryLogHook
 *
 * @author Shagy
 */
class QueryLogHook {

    function log_queries() {
        $CI = & get_instance();
        $times = $CI->db->query_times;
        $output = NULL;
        $queries = $CI->db->queries;

        if (count($queries) == 0) {
            $output .= "no queries\n";
        } else {
            foreach ($queries as $key => $query) {
                $output .= $query . "\n";
            }
            $took = round(doubleval($times[$key]), 3);
            $output .= "===[took:{$took}]\n\n";
        }

        $CI->load->helper('file');
        if (!write_file(APPPATH . "/logs/queries.log.txt", $output, 'a+')) {
            log_message('debug', 'Unable to write query the file');
        }
    }

}
