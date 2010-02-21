<div class="quote-list">
<?php foreach ($quotes as $quote): ?>
<?php include('quote_single.php'); ?>
<?php endforeach; ?>
<?php print $pager; ?>
<div class="feed-icons">
    <a href="/rss.xml"></a>
</div><!-- /.feed-icons -->
</div><!-- /.quote-list -->
