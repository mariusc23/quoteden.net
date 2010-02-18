<?php if (!isset($quote)): ?>
<h1 class="quote-header">
Unknown author
</h1>
<?php return;
endif; ?>
<div class="quote-header">
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
<h1>Related quotes</h1>
</div>
<div class="quote-list quote-list-quote">
<?php foreach ($quotes as $q): ?>
<div class="quote">
<div class="id">
<a href="<?php print Url::site('quote/id/' . $q->id); ?>" title="Details about this quote"><?php print $q->id; ?></a>
</div>
<div class="quote-inner">
<div class="text">
<?php print $q->text; ?>
</div>
<div class="categories">
<?php if ($count > 0) foreach ($q->categories_list as $category) {
    print '<a href="' . Url::site('category/id/' . $category->id) . '">' . $category->name . '</a> ';
} ?>
</div>
<br class="after-author" />
</div><!-- /.quote-inner -->
</div><!-- /.quote -->
<?php endforeach; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list-author -->
