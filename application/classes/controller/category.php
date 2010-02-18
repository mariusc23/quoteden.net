<?php
class Controller_Category extends Controller_Template {
    public $template = 'quotes/template';

    public function action_index() {
        /*/ create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));*/

        // get the content
        $view = View::factory('quotes/categories');
        $view->categories = ORM::factory('category')
            ->order_by('name', 'asc')
            ->find_all();

        /*$view->authors = ORM::factory('author')
            ->order_by('id','desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
        ;*/

        // render the pager
        //$view->pager = $pagination->render();

        $this->template->title = 'Categories';
        $this->template->content = $view;
    }

    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $category = new Model_Category($id);

        $view = View::factory('quotes/category');

        if (!$category->loaded()) {
            $this->template->content = $view;
            return ;
        }

        $view->category = $category;

        $view->quotes_count = DB::select(DB::expr('COUNT(quote_id) AS count'))->from('quote_category')->where('category_id', '=', $id)->execute('default')->get('count');

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $view->quotes_count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // count the number of quotes for each author
        $view->quotes = $category->quotes
            ->order_by('id','desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
        ;

        // render the pager
        $view->pager = $pagination->render();

        $this->template->title = $category->name . ' (category)';
        $this->template->content = $view;
    }

    /**
     * Initialize template values
     */
    public function before() {
        parent::before();
        if ($this->auto_render) {
            // Initialize empty values
            $this->template->title   = '';
            $this->template->content = '';
        }
    }
}
