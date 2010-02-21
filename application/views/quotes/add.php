<?php
function print_form($id, $data = array('text' => '', 'categories' => '', 'author' => '')) {
    echo '<div class="form">
    <div class="text">
        <label><span>Quote ' . $id . ':</span>
            <textarea name="text[]" rows="8" cols="55">' . $data['text'] . '</textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" value="' . $data['categories'] . '" rows="2" cols="34" name="categories[]" ></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="' . $data['author'] . '" size="24" name="author[]" />
        </label>
        <div class="submit">
            <input type="submit" value="Submit" /> or
            <a class="delete" href="#">delete</a>
        </div>
    </div>
    <br class="clear"/>
</div>';
}
if ($quotes || $error) {
    print '<ul class="messages">';
}
if ($quotes) {
    print '<li>Added quotes ';

    $first = true;
    foreach ($quotes as $quote) {
        if (!$first) print ', ';
        $first = false;
        print '<a href="' . Url::site('quote/id/' . $quote->id) . '" title="quote by ' . $quote->author->name . '">'
            . $quote->id . ' (' . $quote->author->name . ')</a>, ';
    }
    print '</li>';
}
if ($error) {
    print '<li class="error">Error saving ' . $error . ' quotes. Auto-filled below</li>';
}

if ($quotes || $error) {
    print '</ul>';
}
?>
<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">

<?php if (!$error): for($i=1; $i<=3; $i++):
    print_form($i);
endfor;
else:
    $i = 0;
    foreach ($_POST['text'] as $k => $text) {
        $i++;
        print_form($i, array(
            'text' => $text,
            'categories' => $_POST['categories'][$k],
            'author' => $_POST['author'][$k],
        ));
    }
endif; ?>
</form>

<a id="quote-add-more" href="#">add</a>
<br class="clear"/>

