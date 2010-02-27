<?php
function print_form($data = array('text' => '', 'categories' => '', 'author' => '')) {
    echo '<div class="form">
    <div class="text">
        <label><span>Add quote:</span>
            <textarea name="text" rows="8" cols="55">' . $data['text'] . '</textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" value="' . $data['categories'] . '" rows="2" cols="34" name="categories" ></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="' . $data['author'] . '" size="24" name="author" />
        </label>
        <div class="submit">
            <input type="submit" value="Submit" /> or
            <a class="delete" href="#">delete</a>
        </div>
    </div>
    <br class="clear"/>
</div>';
}
if ($error) {
    print '<div class="error">Error adding quote</div>';
    $text = $_POST['text'];
    $author = $_POST['author'];
    $categories = $_POST['categories'];
} elseif($_POST) {
    print '<div class="message">Added quote</div>';
    $text = '';
    $author = '';
    $categories = '';
}
?>
<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">

<?php 
print_form(array(
    'text' => $text,
    'categories' => $author,
    'author' => $categories,
));
?>
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