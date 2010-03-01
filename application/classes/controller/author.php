<?php
class Controller_Author extends Controller_Template {
    public $template = 'base/template';

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
        $view = $this->template->content = new View('quotes/add_author');

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

    }

    /**
     * lists all categories
     */
    public function action_index() {

        // get the content
        $view = $this->template->content = View::factory('quotes/authors');
        $view->authors = ORM::factory('author')
            ->order_by('name', 'asc')
            ->find_all();

        $this->template->title = 'Authors';
    }

    /**
     * lists all categories for a letter
     */
    public function action_letter() {
        $letter = $this->request->param('id');
        // get the content
        $view = $this->template->content = View::factory('quotes/authors_letter');
        $view->authors = ORM::factory('author')
            ->where('name', 'LIKE', mb_strtolower($letter) . '%')
            ->order_by('name', 'asc')
            ->find_all();

        $view->letter = $letter;
        $view->count_split = count($view->authors) / 4 + 1;

        $this->template->title = $letter . ' authors';
    }

    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $author = new Model_Author($id);

        $view = $this->template->content = View::factory('quotes/author');

        if (!$author->loaded()) {
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
        $view->quotes = $author->quotes
            ->order_by('id','desc')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
        ;

        // top rated quotes
        $top_limit = max(QUOTES_MIN_TOP_RATED, QUOTES_ITEMS_PER_PAGE - ($view->quotes_count % QUOTES_ITEMS_PER_PAGE));
        $top_voteaverages = ORM::factory('voteaverage')->order_by('average','desc')
             ->limit(QUOTES_ITEMS_PER_PAGE * $top_limit)
             ->offset(0)
             ->find_all()
        ;

        $view->top_quotes = array();
        foreach ($top_voteaverages as $voteaverage) {
            $view->top_quotes[] = $voteaverage->quote;
        }

        shuffle($view->top_quotes);
        $view->top_quotes = array_slice($view->top_quotes, 0, $top_limit);

        // render the pager
        $view->last_page = $pagination->next_page ? null : true;
        $view->pager = $pagination->render();

        $this->template->title = $author->name;
    }

    /**
     * List authors in JSON format
     */
    public function action_jsonlist() {
        $starts_with = trim($this->request->param('id'));
        $starts_with = filter_var($starts_with, FILTER_SANITIZE_STRING);
        if (!$starts_with) {
            header("HTTP/1.0 400 Bad Request");
            die;
        }

        $callback = trim($_GET['callback']);
        if ($callback) {
            header('Content-type: application/x-javascript; charset=utf-8');

            if (!Controller_Search::jsonp_is_valid($callback)) {
                header("HTTP/1.0 400 Bad Request");
                die;
            }

        } else {
            header('Content-type: application/json; charset=utf-8');
        }

        $authors = ORM::factory('author')
            ->where('name', 'LIKE', $starts_with . '%')
            ->order_by('name', 'asc')
            ->limit(AUTHORS_LIST_COUNT)
            ->find_all();

        $authors_json = array();
        $count = 0;
        if (isset($authors)) foreach ($authors as $author) {
            $author_json = array(
                'id' => $author->id,
                'name' => $author->name,
            );
            $authors_json[] = $author_json;
            $count++;
        }

        $json = array(
            'results' => $authors_json,
            'total' => $count,
        );

        if ($count == 0) {
            $json['message'] = 'No results';
        }

        if ($callback) {
            echo $callback . '(' . json_encode($json) . ');';
        } else {
            echo json_encode($json);
        }
        die;
    }

    public function before() {
        parent::before();
        $this->template->user = Auth::instance()->get_user();
        $this->template->model = 'author';
        $this->template->action = Request::instance()->action;
   }
}
