<?php
class Controller_Author extends Controller_Template {
    public $template = 'quotes/template';

    /**
     * Adds an author
     * @param array $data array of (field_name, field_value) pairs
     * @return newly inserted id on success
     *      OR null on failure to save
     *      OR false on invalid supplied data
     */
    public function _add($data) {
        $post = new Validate($data);
        // validate data first
        $post
            ->rule('author_name', 'min_length', array(5))
            ->rule('author_name', 'max_length', array(500))
            ->filter(TRUE, 'trim')
        ;

        if($post->check()) {
            // add author, bio optional
            $author = new Model_Author;
            $author->name = $post['author_name'];
            if (isset($post['author_bio'])) {
                $author->bio = $post['author_bio'];
            }

            if($author->save()) {
                return $author->id;
            } else {
                return null;
            }
        } else {
            return false;
        }

    }

    /**
     * Add action corresponds to /author/add
     * 
     * Adds authors.
     */
    public function action_add() {
        $view = new View('quotes/add_author');

        $result = $this->_add($_POST);
        if ($result) {
            $this->template->title = 'Author added';
        }
        elseif (false === $result) {
            $this->template->title = 'Error adding author';
        }
        elseif ($_POST) {
            $this->template->title = 'Error adding author';
        }
        else {
            $this->template->title = 'Add author';
        }

        $this->template->content = $view;
    }

    public function action_index() {
        // count items
        $count = DB::select(DB::expr('COUNT(id) AS count'))->from('authors')->execute('default')->get('count');

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // get the content
        $view = View::factory('quotes/authors');
        $view->authors = ORM::factory('author')
            ->order_by('id','desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
        ;

        // render the pager
        $view->pager = $pagination->render();

        // count the number of quotes for each author
        $view->quotes_count = array();
        foreach($view->authors as $author) {
            $view->quotes_count[$author->id] = DB::select(DB::expr('COUNT(id) AS count'))->from('quotes')->where('author_id', '=', $author->id)->execute('default')->get('count');
        }


        $this->template->title = 'Authors';
        $this->template->content = $view;
    }

    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $author = ORM::factory('author')->where('id', '=', $id)->find();

        $view = View::factory('quotes/author');

        if (!$author->loaded()) {
            $this->template->content = $view;
            return ;
        }

        $view->author = $author;

        $view->quotes_count = DB::select(DB::expr('COUNT(id) AS count'))->from('quotes')->where('author_id', '=', $id)->execute('default')->get('count');

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $view->quotes_count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // count the number of quotes for each author
        $quotes = $author->quotes
            ->order_by('id','desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
        ;

        // render the pager
        $view->pager = $pagination->render();

        $view->quotes = array();
        $view->categories = array();
        foreach($quotes as $quote) {
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
            $view->quotes[] = $quote;
        }


        $this->template->title = $author->name;
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
