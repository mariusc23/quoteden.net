<?php
class Controller_Quote extends Controller_Template {
    public $template = 'quotes/template';

    /**
     * Add action corresponds to /quote/add
     * 
     * Adds quotes and automatically creates authors if they do not exist
     */
    public function action_add() {
        // validate data first
        $post = new Validate($_POST);
        $post
            ->rule('quote_text', 'min_length', array(5))
            ->rule('quote_author', 'min_length', array(5))
            ->rule('quote_author', 'max_length', array(500))
            ->filter(TRUE, 'trim')
        ;

        if ($post->check()) {
            // check author exists
            $author = ORM::factory('author')->where('name', '=', $post['quote_author'])->find();
            $valid_author_id = $author->id;
            if (!$author->loaded()) {
                // create author if does not exist
                $author_controller = new Controller_Author($this->request);
                $valid_author_id = $author_controller->_add(array(
                    'author_name' => $post['quote_author']
                ));
            }

            // create quote
            $quote = new Model_Quote;
            $quote->text = $post['quote_text'];
            $quote->author_id = $valid_author_id;

            if ($quote->save()) {
                // success!
                $view=new View('quotes/add');
                $view->created = true;

                $this->template->title = 'Quotes saved';
                $this->template->content = $view;
            } else {
                // failure
                $view=new View('quotes/add');
                $view->error = isset($_POST['quote_text']) ? true : false;

                $this->template->title = 'Error saving';
                $this->template->content = $view;
            }
        }
        else {
            $view=new View('quotes/add');
            // if data has been submitted, it's not valid
            if ($_POST) {
                $view->data = $_POST;
                $view->error = isset($_POST['quote_text']) ? true : false;
            }

            $this->template->title = 'Add quotes';
            $this->template->content = $view;
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
        $view = View::factory('quotes/quotes');
        $view->quotes = ORM::factory('quote')->order_by('id','desc')
             ->limit($pagination->items_per_page)
             ->offset($pagination->offset)
             ->find_all()
        ;

        // render the pager
        $view->pager = $pagination->render();

        // get the categories for each quote
        $view->categories = array();
        foreach($view->quotes as $quote) {
            $categories = $quote->categories->find_all();
            $view->categories[$quote->id] = array();
            foreach ($categories as $category) {
                $view->categories[$quote->id][] = array(
                    'id'   => $category->id,
                    'name' => $category->name,
                );
            }
            // sort them alphabetically
            usort($view->categories[$quote->id], array('Controller_Quote', '_sort_categories'));
        }

        $this->template->content = $view;
    }

    public static function _sort_categories($a, $b) {
        return strcmp($a['name'], $b['name']);
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
            $this->template->styles = array();
            $this->template->scripts = array();
        }
    }

    /**
     * Add default css and js
     */
    public function after() {
        if ($this->auto_render) {
            // css
            $styles = array(
                'css/reset.css' => 'screen',
                'css/main.css'  => 'screen',
                'css/print.css' => 'print',
            );

            // js
            $scripts = array(
                'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js',
                'js/main.js',
            );

            $this->template->styles = array_merge( $this->template->styles, $styles );
            $this->template->scripts = array_merge( $this->template->scripts, $scripts );
        }
        parent::after();
    }
}
