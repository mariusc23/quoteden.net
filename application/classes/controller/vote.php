<?php
class Controller_Vote extends Controller {

    /**
     * Top action corresponds to /vote/add
     * 
     * Top rated quotes
     */
    public function action_add() {
        $quote_id = $this->request->param('id');
        $voter_id = $_SERVER['REMOTE_ADDR'];

        if (!ctype_digit($quote_id)) {
            // bad request, invalid data
            header("HTTP/1.0 400 Bad Request");
            die;
        }

        $quote = ORM::factory('quote')->where('id', '=', $quote_id)->find();
        if (!$quote->loaded()) {
            // bad request, invalid data
            header("HTTP/1.0 400 Bad Request");
            die;
        }
        $user = Auth::instance()->get_user();
        if ($user) {
            // check if user already voted on this
            $user_id = $user->id;
            $vote = ORM::factory('vote')
                    ->where('user_id', '=', $user->id)
                    ->where('quote_id', '=', $quote_id)
                    ->find();
        } else {
            // check by IP
            $user_id = null;
            $vote = ORM::factory('vote')
                    ->where('voter', '=', $voter_id)
                    ->where('quote_id', '=', $quote_id)
                    ->find();
        }

        // now we have a valid quote, and a valid vote, check rating
        $post = new Validate($_POST);
        $post->rule('rating', 'Model_Vote::check_numeric');


        if ($post->check()) {
            if ($vote->loaded()) {
                // update vote
                $old_rating = $vote->rating;
                $vote->rating = $post['rating'];

                if ($vote->save()) {
                    $vote_average = ORM::factory('voteaverage')->where('quote_id', '=', $quote_id)->find();
                    if ($vote_average->loaded()) {
                        // update the average
                        $vote_average->average =
                            ($vote_average->average * $vote_average->count + $post['rating'] - $old_rating)
                            / ($vote_average->count);
                    } else {
                        // internal server error
                        header("HTTP/1.0 500 Internal Server Error");
                        die;
                    }

                    if (!$vote_average->save()) {
                        // internal server error
                        header("HTTP/1.0 500 Internal Server Error");
                        die;
                    }
                } else {
                    // internal server error
                    header("HTTP/1.0 500 Internal Server Error");
                    die;
                }

                print $vote_average->average;
                die;
            }

            // create quote
            $vote = new Model_Vote;
            $vote->quote_id = $quote_id;
            $vote->voter = $voter_id;
            $vote->user_id = $user_id;
            $vote->rating = $post['rating'];

            if ($vote->save()) {
                $vote_average = ORM::factory('voteaverage')->where('quote_id', '=', $quote_id)->find();
                if ($vote_average->loaded()) {
                    // update the average
                    $vote_average->average =
                        ($vote_average->average * $vote_average->count + $post['rating'])
                        / ($vote_average->count + 1);
                    $vote_average->count++;
                } else {
                    // vote average not loaded
                    $vote_average = new Model_Voteaverage;
                    $vote_average->quote_id = $quote_id;
                    $vote_average->average = $post['rating'];
                    $vote_average->count = 1;

                }

                if (!$vote_average->save()) {
                    // internal server error
                    header("HTTP/1.0 500 Internal Server Error");
                    die;
                }
                // success!
                header("HTTP/1.0 201 Created");
                print $vote_average->average;
                die;
            } else {
                // internal server error
                header("HTTP/1.0 500 Internal Server Error");
                die;
            }
        }
        else {
            // bad request, invalid data
            header("HTTP/1.0 400 Bad Request");
            die;
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

}
