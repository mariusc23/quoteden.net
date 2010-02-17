<?php
class Model_Author extends ORM {
    protected $_has_many = array('quotes' => array('model' => 'quote', 'foreign_key' => 'author_id'));
}

