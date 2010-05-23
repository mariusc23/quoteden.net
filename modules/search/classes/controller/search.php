<?php
require_once APPPATH . 'classes/helpers.php';

class Controller_Search extends Controller_Template {
    public $template = 'base/template';
    public $sphinxclient = null;

    /**
     * Index action, lists quotes
     * 
     * Ordered descendingly by ID.
     * Also handles pagination.
     */
    public function action_index() {
        $search_query = isset($_GET['q']) ? $_GET['q'] : '';
        $dupes_search = isset($_GET['d']) ? true : false;
        $search_offset = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $search_offset = QUOTES_ITEMS_PER_PAGE * ($search_offset - 1);
        if (!$search_offset) $search_offset = 0;
        if ($dupes_search) {
            $search_query = Helper::shorten_text($search_query, 5);
        }

        $view = View::factory('quotes/search');

        if (isset($search_query) || $search_query !== '') {
            $search_query = mb_convert_encoding($search_query, 'UTF-8', 'auto');
            $search_query = str_replace(array(' OR ',' AND ',' NOT '), array(' | ',' & ',' !'), $search_query);
        } else {
            $this->template->content = $view;
            return ;
        }

        require_once('sphinxapi.php');
        $this->sphinxclient = new SphinxClient();
        $this->sphinxclient->SetServer(SPHINX_HOST, SPHINX_PORT);
        $this->sphinxclient->SetLimits($search_offset, QUOTES_ITEMS_PER_PAGE, SPHINX_MAXRESULTS);
        $this->sphinxclient->SetMatchMode(SPH_MATCH_EXTENDED2);
        $this->sphinxclient->SetRankingMode(SPHINX_RANKER);
        $this->sphinxclient->SetArrayResult(true);
        //$this->sphinxclient->SetSortMode(SPH_SORT_ATTR_DESC, 'id');
        //$this->sphinxclient->ResetFilters();

        $results = $this->sphinxclient->Query(mb_ereg_replace('-', '\-', $search_query)
                        , SPHINX_INDEX);
        if ($results) {
            $count = $results['total'];
            $results = $results['matches'];

            $view->quotes = array();

            if (isset($results)) foreach ($results as $sphinx_quote) {
                $view->quotes[] = new Model_Quote($sphinx_quote['id']);
            }
        } elseif ($_GET['format'] != 'json') {
            $this->template->content = $view;
            return ;
        }

        if ($_GET['format'] == 'json') {
            $short_name = $_GET['short'];
            $callback = trim($_GET['callback']);
            if ($callback) {
                header('Content-type: application/x-javascript; charset=utf-8');

                if (!self::jsonp_is_valid($callback)) {
                    header("HTTP/1.0 400 Bad Request");
                    die;
                }

            } else {
                header('Content-type: application/json; charset=utf-8');
            }

            $quotes = array();
            if (isset($view->quotes)) foreach ($view->quotes as $quote) {
                if ($short_name) {
                    $author_name = explode(' ', $quote->author->name);
                    $last_name = $author_name[count($author_name)-1];
                    unset($author_name[count($author_name)-1]);
                    foreach ($author_name as $k => $name) {
                        $author_name[$k] = mb_eregi_replace("^([A-Za-z])[A-Za-z]+(.*)$", "\\1.\\2", $name);
                    }
                    for($i=count($author_name)-2; $i>=0; $i--) {
                        unset($author_name[$i]);
                    }
                    $author_name = implode(' ', $author_name) . ' ' . $last_name;
                }
                $quote_json = array(
                    'id' => $quote->id,
                    'text' => $quote->text,
                    'author_name' => $short_name ? $author_name : $quote->author->name,
                    'author_id' => $quote->author->id,
                    'categories' => array(),
                );
                if ($quote->categories_list) foreach ($quote->categories_list as $category) {
                    $quote_json['categories'][] = array(
                        'name' => $category->name,
                        'id' => $category->id,
                    );
                }
                $quotes[] = $quote_json;
            }

            $json = array(
                'results' => $quotes,
                'total' => $count,
                'query' => $search_query,
                'search_url' => Url::site('search', 'http') . '?q=' . urlencode($search_query)
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
        self::bold_query($this->sphinxclient, $view, $view->quotes, array($search_query));

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // top rated quotes
        $top_limit = max(QUOTES_MIN_TOP_RATED, QUOTES_ITEMS_PER_PAGE - ($count % QUOTES_ITEMS_PER_PAGE));
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

        $this->template->content = $view;
    }

    /**
     * Bolds queries $q in properties $fields for array objects
     *
     * @param array $quotes array of $quote objects
     * @param string $q array of queries to bold
     * @param array $fields (optional) fields per row to affect
     * @see format_results
     */
    public static function bold_query($sphinxclient, $view, $quotes, $q = array()) {
        $view->categories_list_bolded = array();

        // start with the quote content
        $docs = array();
        foreach ($view->quotes as $quote) {
            $docs[] = $quote->text;
        }
        // build the excerpts
        $excerpts = $sphinxclient->BuildExcerpts($docs, SPHINX_INDEX, implode(' ', $q),
            array('before_match' => '<em>', 'after_match' => '</em>', 'limit' => 2000)
        );
        // reassign the bolded content
        foreach ($docs as $id => $doc) {
            $view->quotes[$id]->text = $excerpts[$id];
        }

        // now categories
        $docs = array();
        foreach ($quotes as $quote) {
            $view->categories_list_bolded[$quote->id] = array();
            foreach ($quote->categories_list as $k => $category) {
                $docs[] = $category->name;
            }
        }
        // build the excerpts
        $excerpts = $sphinxclient->BuildExcerpts($docs, SPHINX_INDEX, implode(' ', $q),
            array('before_match' => '<em>', 'after_match' => '</em>', 'limit' => 2000)
        );
        // reassign the bolded content
        $j = 0;
        foreach ($quotes as $quote) {
            foreach ($quote->categories_list as $k => $category) {
                $category->name = $excerpts[$j];
                $view->categories_list_bolded[$quote->id][] = $category;
                $j++;
            }
        }

        // finally authors
        $docs = array();
        foreach ($quotes as $quote) {
            $docs[] = $quote->author->short_name;
        }
        // build the excerpts
        $excerpts = $sphinxclient->BuildExcerpts($docs, SPHINX_INDEX, implode(' ', $q),
            array('before_match' => '<em>', 'after_match' => '</em>', 'limit' => 2000)
        );
        // reassign the bolded content
        $j = 0;
        foreach ($quotes as $quote) {
            $quote->author->short_name = $excerpts[$j];
            $j++;
        }
    }

    /**
     * Initialize template values
     */
    public function before() {
        parent::before();
        $this->template->user = Auth::instance()->get_user();
        $this->template->model = 'search';
        $this->template->action = Request::instance()->action;
    }

    /**
     * Validates json function name
     */
    public static function jsonp_is_valid($callback) {
        return (bool)preg_match('/^[a-zA-Z_\\$][a-zA-Z0-9_\\$]*(\\[[a-zA-Z0-9_\\$]*\\])*'
                . '(\\.[a-zA-Z0-9_\\$]+(\\[[a-zA-Z0-9_\\$]*\\])*)*$/', $callback);
    }

}

