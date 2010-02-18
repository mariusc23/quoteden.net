<?php if (!isset($category)): ?>
<h1 class="category-header">
Unknown category
</h1>
<?php return;
endif; ?>
<div class="category-header">
<h1>
<?php print $category->name . ' (' . $quotes_count;
if ($quotes_count > 1) print ' quotes)';
else print ' quote)';
?>
</h1>
<h3><a href="<?php print Url::site('category'); ?>">See all categories</a></h3>
</div>
<div class="quote-list">
<?php foreach ($quotes as $quote): ?>
<?php include('quote_single.php'); ?>
<?php endforeach; ?>
<?php print $pager; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list -->
