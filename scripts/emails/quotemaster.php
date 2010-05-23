<?php
// TODO: this is a stub
class Quotemaster {
    public static function process_content($content, $overview) {
        $q_arr = array();
        if (preg_match('/<quotemaster@verbose.twistedhistory.com>$/', $overview[0]->from)) {
        }
        // return the array of quotes
        return $q_arr;
    }
}
