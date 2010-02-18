<?php
class Controller_Search extends Controller_Template {
    public $template = 'quotes/template';
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
        $count = $results['total'];
        $results = $results['matches'];

        $view->quotes = array();
        foreach ($results as $sphinx_quote) {
            $view->quotes[] = new Model_Quote($sphinx_quote['id']);
        }
        // count items

        // create pagination object
        $pagination = Pagination::factory(array(
            'current_page'   => array('source' => 'query_string', 'key' => 'p'),
            'total_items'    => $count,
            'items_per_page' => QUOTES_ITEMS_PER_PAGE,
        ));

        // render the pager
        $view->pager = $pagination->render();

        $this->template->content = $view;
    }


    /**
     * Shows the author page, lists some quotes
     */
    public function action_id() {
        $id = $this->request->param('id');
        $quote = new Model_Quote($id);

        $view = View::factory('quotes/quote');

        if (!$quote->loaded()) {
            $this->template->content = $view;
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
