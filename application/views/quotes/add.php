<?php
if (isset($data)) {
    $text = $data['text'];
    $author = $data['author'];
    $categories = $data['categories'];
    if ($error) {
        print 'Error occured.';
        print '<br/>';
    }
} else {
    $text = '';
    $author = '';
    $categories = '';
}

?>
<div id="add-forms">
<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
    <div class="text">
        <label><span>Quote 1:</span>
            <textarea name="text[]" rows="8" cols="55"></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" value="<?php print $categories ?>" rows="2" cols="34" name="categories[]" ></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $categories ?>" size="24" name="author[]" />
        </label>
        <div class="submit">
            <input type="submit" value="Submit" /> or
            <a href="<?php print Url::site('quote/delete') ?>">delete</a>
        </div>
    </div>
</form>

<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
    <div class="text">
        <label><span>Quote 2:</span>
            <textarea name="text[]" rows="8" cols="55"></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" value="<?php print $categories ?>" rows="2" cols="34" name="categories[]" ></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $categories ?>" size="24" name="author[]" />
        </label>
        <div class="submit">
            <input type="submit" value="Submit" /> or
            <a href="<?php print Url::site('quote/delete') ?>">delete</a>
        </div>
    </div>
</form>

<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
    <div class="text">
        <label><span>Quote 3:</span>
            <textarea name="text[]" rows="8" cols="55"></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" value="<?php print $categories ?>" rows="2" cols="34" name="categories[]" ></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $categories ?>" size="24" name="author[]" />
        </label>
        <div class="submit">
            <input type="submit" value="Submit" /> or
            <a href="<?php print Url::site('quote/delete') ?>">delete</a>
        </div>
    </div>
</form>

</div>

<a id="quote-add-more" href="#">add</a>
<br class="clear"/>

