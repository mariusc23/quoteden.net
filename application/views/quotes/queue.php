<?php
function by_strlen($a, $b) {
    $a = strlen($a);
    $b = strlen($b);
    if ($a === $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

foreach ($quotes as $quote):

// make the top 5 longest words in the text be categories
$categories = array_unique(preg_split('/[\s,.!@#$%^&*\(\)_\+;:\'"\[\]{},\/<>?]+/',
                  $quote->text, -1, PREG_SPLIT_NO_EMPTY));
$categories = array_map(strtolower, $categories);
usort($categories, by_strlen);
$categories = array_slice($categories, 0, 5);
$categories = implode(', ', $categories);

?>
<div class="original">
    <strong>Original:</strong><br/>
    <span><?php print nl2br(str_replace(" ", "&nbsp;", $quote->original)) ?></span>
</div>
<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
<div class="form">
    <div class="text">
        <label><span>Add quote:</span>
            <textarea name="text" rows="8" cols="55"><?php print $quote->text ?></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" rows="2" cols="34" name="categories" ><?php print $categories ?></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $quote->author ?>" size="24" name="author" autocomplete="off" />
        </label>

        <div class="submit">
            <input type="submit" value="Submit" />
        </div>
    </div>
    <br class="clear"/>

</div>
</form>
<?php endforeach;
print $pager; ?>
