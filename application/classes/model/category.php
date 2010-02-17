<?php
class Model_Category extends ORM {
    protected $_belongs_to = array('quote' => array('model' => 'quote_category', 'foreign_key' => 'quote_id'));
    protected $_has_many = array('quotes' => array('model' => 'quote', 'through' => 'quote_category'));
}

