<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
<div class="form">
    <div class="text">
        <label><span>Add quote:</span>
            <textarea name="text" rows="8" cols="55"><?php print $text ?></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" rows="2" cols="34" name="categories" ><?php print $categories ?></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $author ?>" size="24" name="author" autocomplete="off" />
        </label>

        <div class="submit">
            <input type="submit" value="Submit" />
            <?php if ($action == 'edit'): ?>or <a href="<?php print Url::site('quote/delete/' . $quote->id); ?>" title="Delete this quote">delete</a><?php endif; ?>
        </div>
    </div>
    <br class="clear"/>
</div>
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


<?php
$message = "         ***
Motivational Quotes of the Day for April 26, 2010
http://www.quotationspage.com/mqotd.html
----------------------------------
I’m telling you, things are getting out of hand. Or maybe I’m
discovering that things were never in my hands.
           -- Real Live Preacher, RealLivePreacher.com Weblog, August 2, 2003

You cannot live a perfect day without doing something for someone who
will never be able to repay you.
           -- John Wooden

Best wide-angle lens? Two steps backward. Look for the 'ah-ha'.
           -- Ernst Haas, Comment in workshop, 1985

Nobody can tell you if what you're doing is good, meaningful or
worthwhile. The more compelling the path, the more lonely it is.
           -- Hugh Macleod, How To Be Creative: 5, 08-22-04

Visit our new site - The Literature Page: Read hundreds of classic
books, plays, and poems online FREE! - New titles added daily.
http://quotationspage.com/mailclicks.php?i=60
*****
For more quotes, visit The Quotations Page...
http://www.quotationspage.com/
Read books, plays, and poems online, FREE, at The Literature Page...
http://www.literaturepage.com/
******


NOTE: You are receiving the Motivational Quotes of the Day because you
have subscribed to our mailing list. This list is sent one message
daily, plus Quotations Page announcements less than once a month. Your
email address is not available to spammers or anyone else.

Another note: These quotations are selected automatically each day by
computer from Laura Moncur's collection of motivational quotations.
These quotations do not always represent our opinions. If you disagree
with them or are in any
way offended, we apologize; your only recourse is to unsubscribe.
--^----------------------------------------------------------------
This email was sent to: gewissen@gmail.com

EASY UNSUBSCRIBE click here: http://topica.com/u/?b1dhFB.bP2xmm.Z2V3aXNz
Or send an email to: mqotd-unsubscribe@topica.com

For Topica's complete suite of email marketing solutions visit:
http://www.topica.com/?p=TEXFOOTER
--^----------------------------------------------------------------
";

$start_string = "\n----------------------------------\n";
$quotes_start = strpos($message, $start_string) + strlen($start_string);
if ($quotes_start !== false) {
    $message = substr($message, $quotes_start);
    $quotes = array_slice(explode("\n\n", $message, 5), 0, 4);
    $q_arr = array();
    foreach ($quotes as $quote) {
        $q = preg_split('/\n\s+-- /', $quote);
        $q = array('text' => cleanup_text($q[0]),
                   'author' => cleanup_author($q[1]),
                   'original' => $quote);
        $q_arr[] = $q;
    }
}

foreach ($q_arr as $q): ?>
<div class="original">
    <strong>Original:</strong><br/>
    <pre><?php print $q['original'] ?></pre>
</div>
<form method="post" accept-charset="UTF-8" action="<?php print Url::site('quote/add') ?>">
<div class="form">
    <div class="text">
        <label><span>Add quote:</span>
            <textarea name="text" rows="8" cols="55"><?php print $q['text'] ?></textarea>
        </label>
    </div>

    <div class="meta">
        <label><span>Categories (comma separated):</span>
            <textarea type="text" rows="2" cols="34" name="categories" ><?php print $categories ?></textarea>
        </label>

        <label class="author"><span>Author:</span>
            <input type="text" value="<?php print $q['author'] ?>" size="24" name="author" autocomplete="off" />
        </label>

        <div class="submit">
            <input type="submit" value="Submit" />
        </div>
    </div>
    <br class="clear"/>

</div>
</form>
<?php endforeach;

function cleanup_text($quote_text) {
    $quote_text = preg_replace('/\n\s+/', ' ', $quote_text);
    return trim($quote_text);
}

function cleanup_author($quote_author) {
    if (strpos($quote_author, ',') !== false) {
        $quote_author = substr($quote_author, 0, strpos($quote_author, ','));
    }
    return trim($quote_author);
}

die('<pre>'.$message.'</pre>');