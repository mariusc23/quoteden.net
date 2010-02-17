<div class="quote-list">
<?php foreach ($quotes as $quote): ?>
<div class="quote">
<div class="id">
<a href="<?php echo Url::site('quote/id/' . $quote->id); ?>" title="Details about this quote"><?php echo $quote->id; ?></a>
</div>
<div class="quote-inner">
<div class="text">
<?php echo $quote->text; ?>
</div>
<div class="categories">
<?php foreach ($categories[$quote->id] as $category) {
    echo '<a href="' . Url::site('category/id/' . $category['id']) . '">' . $category['name'] . '</a> ';
} ?>
</div>
<div class="author"><a href="<?php echo Url::site('author/id/' . $quote->author->id); ?>" title="More quotes by this author"><?php
$author_name = explode(' ', $quote->author->name, 4);
$last_name = $author_name[count($author_name)-1];
unset($author_name[count($author_name)-1]);
foreach ($author_name as $k => $name) {
    $author_name[$k] = mb_eregi_replace("^([A-Za-z])[A-Za-z]+(.*)$", "\\1.\\2", $name);
}
echo implode(' ', $author_name) . ' ' . $last_name; ?></a></div>
<br class="after-author" />
</div><!-- /.quote-inner -->
</div><!-- /.quote -->
<?php endforeach; ?>
<?php echo $pager; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list -->
