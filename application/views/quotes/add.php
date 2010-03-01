<?php
if ($error) {
    if ($action == 'add') {
        print '<div class="error">Error adding quote</div>';
    } else {
        print '<div class="error">Error updating quote</div>';
    }
} elseif($_POST) {
    if ($action == 'add') {
        print '<div class="message">Added quote</div>';
    } else {
        print '<div class="message">Updated quote</div>';
    }
}
?>
<?php if ($action == 'edit'): ?>
<div class="message">
    <a href="<?php print Url::site('quote/delete/' . $quote->id); ?>" title="Delete this quote">Delete</a>
</div>
<?php endif; ?>

<form method="post" accept-charset="UTF-8" action="<?php if ($action == 'add') {
    print Url::site('quote/add');
} else {
    print Url::site('quote/edit/' . $id);
} ?>">
<div class="form">
    <div class="text">
        <label><span><?php if ($action == 'add'): ?>Add quote:<?php else: ?>Edit quote:<?php endif; ?></span>
            <textarea name="text" rows="8" cols="55"><?php print $text ?></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" rows="2" cols="34" name="categories" ><?php print $categories ?></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $author ?>" size="24" name="author" />
        </label>

        <div class="submit">
            <input type="submit" value="Submit" />
        </div>
    </div>
    <br class="clear"/>
</div>
</form>

<br class="clear"/>

<div class="search-status">
Type your quote and watch this area for duplicate quotes
</div>
<div class="qresults">
    <div class="qresult" style="display: none;">
        <span class="id"></span> &mdash;
        <span class="text"></span> &mdash;
        <span class="author"></span>
    </div>
</div>