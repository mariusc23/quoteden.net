<?php if (!isset($author)): ?>
<h1 class="author-header">
Unknown author
</h1>
<?php return;
endif; ?>
<div class="author-header">
<h1>
<?php print $author->name . ' (' . $quotes_count;
if ($quotes_count > 1) print ' quotes)';
else print ' quote)';
?>
</h1>
<h3><a href="<?php print Url::site('author'); ?>">See all authors</a></h3>
</div>
<?php if ($author->bio): ?>
<div class="author-bio">
<?php print $author->bio; ?>
</div>
<?php endif; ?>
<div class="quote-list quote-list-author">
<?php foreach ($quotes as $quote): ?>
<div class="quote">
<div class="id">
<a href="<?php print Url::site('quote/id/' . $quote->id); ?>" title="Details about this quote"><?php print $quote->id; ?></a>
</div>
<div class="quote-inner">
<div class="text">
<?php print $quote->text; ?>
</div>
<div class="categories">
<?php foreach ($quote->categories_list as $category) {
    print '<a href="' . Url::site('category/id/' . $category->id) . '">' . $category->name . '</a> ';
} ?>
</div>
<br class="after-author" />
</div><!-- /.quote-inner -->
</div><!-- /.quote -->
<?php endforeach; ?>
<?php print $pager; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list-author -->
