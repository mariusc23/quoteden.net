<?php
class Controller_Quote extends Controller_Template {
    public $template = 'base/template';

    /**
     * Add action corresponds to /quote/add
     * 
     * Adds quotes and automatically creates authors if they do not exist
     */
    public function action_add() {
        // validate data first
        if (!$this->template->user) {
            Request::instance()->redirect('user/login');
        }
        $post = new Validate($_POST);
        $post
            ->rule('text', 'min_length', array(5))
            ->rule('author', 'min_length', array(5))
            ->rule('author', 'max_length', array(500))
            ->filter(TRUE, 'trim')
        ;
        $view = $this->template->content = new View('quotes/add');

        if ($post->check()) {
            // check author exists
            $author = ORM::factory('author')->where('name', '=', $post['author'])->find();
            $valid_author_id = $author->id;
            if (!$author->loaded()) {
                // create author if does not exist
                $author_controller = new Controller_Author($this->request);
                $valid_author_id = $author_controller->_add(array(
                    'author_name' => $post['author']
                ));
            }

            // create quote
            $quote = new Model_Quote;
            $quote->text = $post['text'];
            $quote->author_id = $valid_author_id;

            if ($quote->save()) {
                // success!
                $view->created = true;
                $this->template->title = 'Quotes saved';
            } else {
                // failure
                $view->error = isset($_POST['text']) ? true : false;
                $this->template->title = 'Error saving';
            }
        }
        else {
            // if data has been submitted, it's not valid
            if ($_POST) {
                $view->data = $_POST;
                $view->error = isset($_POST['text']) ? true : false;
            }

            $this->template->title = 'Add quotes';
        }

    }

    /**
     * Index action, lists quotes
     * 
     * Ordered descendingly by ID.
     * Also handles pagination.
     */
    public function action_index() {
        // count items
        $count = DB::select(DB::expr('COUNT(id) AS count'))->from('quotes')->execute('default')->get('count');

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // get the content
        $view = $this->template->content = View::factory('quotes/quotes');
        $view->quotes = ORM::factory('quote')->order_by('id','desc')
             ->limit($pagination->items_per_page)
             ->offset($pagination->offset)
             ->find_all()
        ;

        // render the pager
        $view->pager = $pagination->render();
    }


    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $quote = new Model_Quote($id);

        $view = $this->template->content = View::factory('quotes/quote');

        if (!$quote->loaded()) {
            return ;
        }

        $view->quote = $quote;

        $view->quotes = array();
        $count = 0;
        foreach($quote->categories_list as $category) {
            if ($count > QUOTES_ITEMS_PER_PAGE) break;
            $quotes = $category->quotes->find_all();
            foreach ($quotes as $q) {
                if ($id == $q->id) continue;
                if ($count > QUOTES_ITEMS_PER_PAGE) break;
                $view->quotes[] = $q;
                $count++;
            }
        }
        $view->count = $count;

        $this->template->title = 'Quote ' . $quote->id;
    }

    public function before() {
        parent::before();
        $this->template->user = Auth::instance()->get_user();
        $this->template->model = 'quote';
        $this->template->action = Request::instance()->action;
   }
}
