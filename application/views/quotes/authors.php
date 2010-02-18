<div class="author-list">
<?php foreach ($authors as $author): ?>
<h1 class="author">
<a href="<?php print Url::site('author/id/' . $author->id); ?>" title="See quotes by this author"><?php print $author->name; ?> (<?php
print $quotes_count[$author->id];
if ($quotes_count[$author->id] > 1) print ' quotes';
else print ' quote';
?>)</a>
</h1><!-- /.author -->
<?php endforeach; ?>

<?php print $pager; ?>
