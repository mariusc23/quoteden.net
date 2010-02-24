<?php
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
        $search_offset = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $search_offset = QUOTES_ITEMS_PER_PAGE * ($search_offset - 1);
        if (!$search_offset) $search_offset = 0;

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

        $results = $this->sphinxclient->Query($search_query, SPHINX_INDEX);
        if ($results) {
            $count = $results['total'];
            $results = $results['matches'];

            $view->quotes = array();

            foreach ($results as $sphinx_quote) {
                $view->quotes[] = new Model_Quote($sphinx_quote['id']);
            }
        } else {
            $this->template->content = $view;
            return ;
        }
        $this->bold_query($view, $view->quotes, array($search_query));

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
    public function bold_query($view, $quotes, $q = array()) {
        $view->categories_list_bolded = array();

        $mb_words = array_unique(mb_split("\s", implode($q, " ")));
        if ($mb_words) {
            foreach ($quotes as $i => $quote) {
                foreach ($mb_words as $mb_word) {
                    if (!$mb_word) continue;

                    $preg_repl = "(\b" . preg_quote($mb_word)."\b)";

                    $quotes[$i]->text = mb_eregi_replace($preg_repl, "<em>\\1</em>", $quotes[$i]->text);
                    $quotes[$i]->author->name = mb_eregi_replace($preg_repl, "<em>\\1</em>", $quotes[$i]->author->name);

                    $view->categories_list_bolded[$quote->id] = array();
                    foreach ($quote->categories_list as $k => $category) {
                        $category->name = mb_eregi_replace($preg_repl, "<em>\\1</em>", $category->name);
                        $view->categories_list_bolded[$quote->id][] = $category;
                    }
                }
            }
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
}
