<?php
class Model_Quote extends ORM {
    protected $_belongs_to = array('author' => array('model' => 'author', 'foreign_key' => 'author_id'));
    protected $_has_many = array('categories' => array('model' => 'category', 'through' => 'quote_category'));

    public function __construct($id = NULL) {
        parent::__construct($id);
        $this->_object['categories_list'] = $this->categories
            ->order_by('name', 'asc')
            ->find_all();
    }
}

