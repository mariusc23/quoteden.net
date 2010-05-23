<?php
require_once APPPATH . 'classes/helpers.php';

foreach ($quotes as $quote):

// make the top 5 longest words in the text be categories
$categories = Helper::shorten_text($quote->text, 5, ', ');

?>
<div class="original">
    <strong>Original:</strong> <em style="display: none">(Click quote to cycle)</em><br/>
    <span><?php print nl2br(str_replace(" ", "&nbsp;", $quote->original)) ?></span>
</div>
<form class="quote-form" method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
<div class="form">
    <input type="hidden" name="id" value="<?php print $quote->id; ?>"/>
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
            <input type="submit" value="Approve" /> or <a class="delete" href="<?php print Url::site('quote/delete_q/' . $quote->id); ?>" title="Remove quote from queue">delete</a>
        </div>
    </div>
    <br class="clear"/>

</div>
</form>
<?php endforeach;
print $pager; ?>
