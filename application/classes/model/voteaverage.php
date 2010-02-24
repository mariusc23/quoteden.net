<?php
class Model_Voteaverage extends ORM {
    protected $_primary_key = 'quote_id';
    protected $_belongs_to = array('quote' => array('model' => 'quote', 'foreign_key' => 'quote_id'));
    protected $_has_many = array('votes' => array('model' => 'vote', 'foreign_key' => 'quote_id'));
}
