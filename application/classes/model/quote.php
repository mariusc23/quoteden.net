<?php
class Model_Quote extends ORM {
    protected $_belongs_to = array('author' => array('model' => 'author', 'foreign_key' => 'author_id'));
    protected $_has_many = array('categories' => array('model' => 'category', 'through' => 'quote_category'));
}

