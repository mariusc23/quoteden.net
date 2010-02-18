<?php if (!isset($quote)): ?>
<h1 class="quote-header">
Unknown author
</h1>
<?php return;
endif; ?>
<div class="quote-header">
<?php include('quote_single.php'); ?>
<h1>Related quotes</h1>
</div>
<div class="quote-list quote-list-quote">
<?php foreach ($quotes as $quote): ?>
<?php include('quote_single.php'); ?>
<?php endforeach; ?>
<div class="feed-icons">
    <a href="/rss.xml"><img width="16" height="16" title="Quote feed" alt="Syndicate content" src="/img/feed.png"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list-author -->
