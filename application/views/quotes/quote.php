<?php if (!isset($quote)): ?>
<h1 class="quote-header">
Unknown author
</h1>
<?php return;
endif; ?>
<?php include('quote_single.php'); ?>
<div class="quote-header<?php if ($quotes): ?> border<?php endif; ?>">
Looking for other great quotes?
<?php if ($quotes): ?>
Check out the related quotes below or our <a href="<?php print Url::site('top'); ?>">top related quotes</a>.
<?php else: ?>
Check out our <a href="<?php print Url::site('top'); ?>">top related quotes</a>.
<?php endif; // if ($quotes) ?>
</div>
<div class="quote-list quote-list-quote">
<?php foreach ($quotes as $quote): ?>
<?php include('quote_single.php'); ?>
<?php endforeach; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list-author -->
