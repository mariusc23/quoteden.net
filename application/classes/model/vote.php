<?php
class Model_Vote extends ORM {
    protected $_belongs_to = array('quote' => array('model' => 'vote', 'foreign_key' => 'quote_id')
                                 , 'voteaverage' => array('model' => 'vote_verage', 'foreign_key' => 'quote_id')
            );

    public static function check_numeric($rating) {
        if (!$rating) return false;
        if (!ctype_digit($rating)) {
            return false;
        }
        if ($rating != intval($rating)) {
            return false;
        }
        if (0 > $rating || 100 < $rating) {
            return false;
        }
        return true;
    }
}
