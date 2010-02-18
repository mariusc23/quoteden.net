<?php
define('SITE_NAME', 'Quote Den');
if ($title) {
    $title = $title . SITE_SEPARATOR . SITE_NAME;
} else {
    $title = SITE_NAME;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo Url::site('rss.xml') ?>" title="<?php echo SITE_NAME; ?> feed" type="application/rss+xml" rel="alternate" />
<link type="image/x-icon" href="<?php echo Url::site('img/favicon.ico') ?>" rel="shortcut icon">
<link type="text/css" rel="stylesheet" href="<?php echo Url::site('min/?g=css'); ?>" rel="stylesheet" media="screen" />
</head>
<body>
<div id="header">
<div id="header-inner">

<h1 id="logo"><a rel="home" title="<?php echo SITE_NAME; ?> - Home Page" href="/"><?php echo SITE_NAME; ?></a></h1>

<div class="quote">
    <div class="text"><p>One man can make a difference and every man should try.</p></div>
    <div class="author"><a href="/author/id/1">Jacqueline Kennedy Onassis</a></div>
</div><!-- /.quote -->

<a title="Add quotes" href="<?php echo Url::site('quote/add'); ?>" class="add-quotes">Add quotes</a>

<div id="login">
    <form id="user-login-form" method="post" accept-charset="UTF-8" action="/login">
        <input type="text" value="" size="15" name="user" maxlength="60" title="Username" />
        <input type="password" size="15" maxlength="60" name="pass" title="Password" />
        <input type="submit" value="Log in" name="login" />
        <a title="Reset your password" href="/user/password">Forgot password?</a>
    </form>
</div>

<a href="/top" title="Top Rated Quotes" class="top-rated"><img src="<?php echo Url::site('img/top.png') ?>" alt="Top Rated"/></a>

<form id="search" method="post" accept-charset="UTF-8" action="/">
    <input type="text" title="Search this site" value="" size="15" name="q" maxlength="255" />
    <input type="submit" value="Search" id="edit-submit" name="search" />
</form>

</div></div><!-- /#header-inner, /#header -->
<div id="content">
<div id="content-inner">
<?php echo $content ?>
</div><!-- /#content-inner -->
</div><!-- /#content -->

<div id="footer">
<div id="footer-inner">
Copyright &copy; 2009 - <?php echo date('Y'); ?> <a href="/"><?php echo SITE_NAME; ?></a>
</div><!-- /#footer-inner -->
</div><!-- /#footer -->

<script type="text/javascript" src="<?php echo Url::site('min/?g=js'); ?>"></script>
</body>
</html>

