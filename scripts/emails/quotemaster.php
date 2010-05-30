<?php
class Quotemaster {

    public static function build_original($quote_text, $quote_author, $overview) {
        return trim($quote_text) . "\n    -- " . $quote_author .
                ' (email subject: ' . $overview[0]->subject . ')';
    }

    // processes the html version of the content
    public static function process_content_html($content, $overview) {
        $q_arr = array();
        if (preg_match('/<quotemaster@verbose.twistedhistory.com>$/', $overview[0]->from) &&
            preg_match('/Studs/', $overview[0]->subject) &&
            preg_match('/All from/', $content)) {
            // cleanup and encode content
            $content = trim(preg_replace('/^.*<b>Today\'s Quotes:<\/b>/s', '', $content));
            $content = strip_tags($content);
            $content = trim(preg_replace('/Your Subscription:.+/s', '', $content));
            $content = trim(preg_replace('/&nbsp;/', ' ', $content));
            $content = trim(preg_replace('/[' . chr(13) . chr(10) . ']/s', "\n", $content));

            // split up into quotes
            $content = preg_split('/All from /', $content);
            $author = trim(preg_replace("/\n/s", ' ', $content[1]));
            $author = trim(preg_replace('/,.+/s', '', $author));
            $author = htmlspecialchars_decode($author);
            $quotes = explode("\n\n\n\n", $content[0]);
            // put those together in an array
            foreach ($quotes as $quote) {
                $q = preg_split('/-- /', $quote);
                $q = array('text' => htmlspecialchars_decode(trim($quote)),
                        'author' => $author,
                        'original' => self::build_original($quote, $author, $overview));
                $q_arr[] = $q;
            }
        }
        // return the array of quotes
        return $q_arr;
    }


    public static function process_content($content, $overview) {
        $separator = str_repeat('_', 59);

        $q_arr = array();
        if (preg_match('/<quotemaster@verbose.twistedhistory.com>$/', $overview[0]->from)) {

        // single author
        if (preg_match('/All from/', $content)) { return $q_arr;
            // cleanup and encode content
            $content = str_replace("\r\n", "\n", $content);
            $content = trim(preg_replace('/^.*Today\'s Quotes:/s', '', $content));
            $content = trim(preg_replace('/' . $separator . '.+$/s', '', $content));

            // split up into quotes
            $content = preg_split('/All from /', $content);
            $author = trim(preg_replace('/,.+/', '', $content[1]));
            $quotes = explode("\n\n", $content[0]);
            // put those together in an array
            foreach ($quotes as $quote) {
                $q = array('text' => trim($quote),
                        'author' => $author,
                        'original' => self::build_original($quote, $author, $overview));
                $q_arr[] = $q;
            }
        } else {
            // multiple authors
            // cleanup and encode content
            $content = str_replace("\r\n", "\n", $content);
            $content = trim(preg_replace('/^.*Today\'s Quotes:/s', '', $content));
            $content = trim(preg_replace('/' . $separator . '.+$/s', '', $content));

            // split up into quotes
            $quotes = explode("\n\n", $content);
            // put those together in an array
            foreach ($quotes as $quote) {
                $q = preg_split('/     - /', $quote);
                $author = trim(preg_replace('/,.+/', '', $q[1]));
                $q = array('text' => trim($q[0]),
                        'author' => $author,
                        'original' => self::build_original(trim($q[0]), $author, $overview));
                $q_arr[] = $q;
            }
        }

        } // end wrapper if
        // return the array of quotes
        return $q_arr;
    }
}
