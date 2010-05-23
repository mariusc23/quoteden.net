<?php
class Starlingtech {
    public static $start_string = "----------------------------------";
    public static $start_len = 35;  // this must be updated as above ^

    public static function cleanup_original($quote_text) {
        return trim($quote_text);
    }

    public static function cleanup_text($quote_text) {
        $quote_text = preg_replace('/\n\s+/', ' ', $quote_text);
        $quote_text = preg_replace('/\s+/', ' ', $quote_text);
        return trim($quote_text);
    }

    public static function cleanup_author($quote_author) {
        if (strpos($quote_author, ',') !== false) {
            $quote_author = substr($quote_author, 0,
                                   strpos($quote_author, ','));
        }
        return trim($quote_author);
    }

    public static function process_content($content, $overview) {
        $q_arr = array();
        if (preg_match('/@starlingtech.com>$/', $overview[0]->from)) {
            // cleanup and encode content
            $content = str_replace("\r\n", "\n",
                mb_convert_encoding($content, 'utf8', 'windows-1252'));

            // trim off the top part
            $quotes_start = strpos($content, self::$start_string) + self::$start_len;
            if ($quotes_start !== false) {
                $content = substr($content, $quotes_start);

                // split up into quotes, expecting 4
                $quotes = array_slice(explode("\n\n", $content, 5), 0, 4);
                // put those together in an array
                foreach ($quotes as $quote) {
                    $q = preg_split('/-- /', $quote);
                    $q = array('text' => self::cleanup_text($q[0]),
                            'author' => self::cleanup_author($q[1]),
                            'original' => self::cleanup_original($quote));
                    $q_arr[] = $q;
                }
            }
        }
        // return the array of quotes
        return $q_arr;
    }
}
