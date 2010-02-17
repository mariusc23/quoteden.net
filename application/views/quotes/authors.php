<?php

foreach($authors as $author)
{
    echo '<h1>'.$author->name.' ('.$quotes_count[$author->id].')</h1>';
    echo '<div>'.$author->bio.'</div>';
    echo '<br/>';
    echo '<br/>';
}

echo $pager;

echo '<a href="' . Url::site('quote/add') . '">Add author</a>';