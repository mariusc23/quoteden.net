<?php
class Controller_Category extends Controller_Template {
    public $template = 'base/template';

    /**
     * lists all categories
     */
    public function action_index() {

        // get the content
        $view = $this->template->content = View::factory('quotes/categories');
        $view->categories = ORM::factory('category')
            ->order_by('name', 'asc')
            ->find_all();

        $this->template->title = 'Categories';
    }


    /**
     * lists all categories for a letter
     */
    public function action_letter() {
        $letter = $this->request->param('id');
        // get the content
        $view = $this->template->content = View::factory('quotes/categories_letter');
        $view->categories = ORM::factory('category')
            ->where('name', 'LIKE', mb_strtolower($letter) . '%')
            ->order_by('name', 'asc')
            ->find_all();

        $view->letter = $letter;
        $view->count_split = count($view->categories) / 4 + 1;

        $this->template->title = $letter . ' categories';
    }

    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $category = new Model_Category($id);

        $view = $this->template->content = View::factory('quotes/category');

        if (!$category->loaded()) {
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
        $view->last_page = $pagination->next_page ? null : true;
        $view->pager = $pagination->render();

        $this->template->title = $category->name . ' (category)';
    }

    public function before() {
        parent::before();
        $this->template->user = Auth::instance()->get_user();
        $this->template->model = 'category';
        $this->template->action = Request::instance()->action;
   }
}
