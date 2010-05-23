<?php

class Helper {
    public static $EXCLUDE_LIST = array(
        'something', 'therefore', 'sometimes', 'could', 'without'
    );

    /**
    * @param string $text quote's text
    * @param int $num_words the number of words to return
    * @param boolean $exclude exclude some common words
    */
    public static function shorten_text($text, $num_words, $separator = ' ', $exclude = true) {
        $text = array_unique(
            preg_split('/[\s,.!@#$%^&*\(\)_\+;:\'"\[\]{},\/<>?]+/',
                        $text, -1, PREG_SPLIT_NO_EMPTY));
        $text = array_map(strtolower, $text);
        $text = array_diff($text, self::$EXCLUDE_LIST);
        usort($text, array('Helper', 'by_strlen'));
        $text = array_slice($text, 0, $num_words);
        $text = implode($separator, $text);
        return $text;
    }

    private static function by_strlen($a, $b) {
        $a = strlen($a);
        $b = strlen($b);
        if ($a === $b) {
            return 0;
        }
        return ($a > $b) ? -1 : 1;
    }
}