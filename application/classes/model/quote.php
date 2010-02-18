<?php
class Model_Quote extends ORM {
    protected $_belongs_to = array('author' => array('model' => 'author', 'foreign_key' => 'author_id'));
    protected $_has_many = array('categories' => array('model' => 'category', 'through' => 'quote_category'));

    public function __construct($id = NULL) {
        parent::__construct($id);
        $categories = $this->categories->find_all();
        $this->_object['categories_list'] = array();
        foreach ($categories as $category) {
            $this->_object['categories_list'][$category->id] = $category;
        }
        usort($this->_object['categories_list'], array('Model_Quote', '_sort_categories'));
    }

    /**
     * Comparison function for sorting categories alphabetically.
     * @param array $a, $b objects for category (needs 'name' property)
     * @return strcmp result for ($a, $b)
     * @see strcmp (php)
     */
    public static function _sort_categories($a, $b) {
        return strcmp($a->name, $b->name);
    }

}

