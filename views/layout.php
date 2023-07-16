<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Phone book application">
    <meta name="author" content="Vladimir Ivanov">
    <link rel="icon" href="/favicon.ico?v2">

    <title><?=(isset($title) ? $title : 'PhoneBook')?></title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body role="document">

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Phonebook</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li<?=(preg_match('/^\/$/', Flight::request()->url) ? ' class="active"' : '')?>><a href="/">Home</a></li>
                <?php if (\stz184\session\Session::isLoggedIn()): ?>
                <li<?=(preg_match('/^\/phonebook\b/', Flight::request()->url) ? ' class="active"' : '')?>><a href="/phonebook/">Phonebook</a></li>
                <li class="dropdown">
                    <a href="#" class="" data-toggle="dropdown" role="button" aria-expanded="false">Profile <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li<?=(preg_match('/^\/change-password[\b\/]{1}/', Flight::request()->url) ? ' class="active"' : '')?>><a href="/change-password/">Change Password</a></li>
                        <li><a href="/logout">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li<?=(preg_match('/^\/login[\b\/]{1}/', Flight::request()->url) ? ' class="active"' : '')?>><a href="/login">Login</a></li>
                <li<?=(preg_match('/^\/signup[\b\/]{1}/', Flight::request()->url) ? ' class="active"' : '')?>><a href="/signup">Signup</a></li>
                <?php endif; ?>

            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container theme-showcase" role="main">
<div id="alerts-placeholder">
<?=\Flight::alerter()->getMessages()?>
</div>
<?=(isset($content) ? $content : '')?>
</div> <!-- /container -->

<footer class="footer">
    <div class="container">
        <p class="text-muted">The request took <?=round(microtime(true) - $start, 3)?>s. and <?=formatBytes(memory_get_usage(true))?> memory</p>
    </div>
</footer>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/docs.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/js/ie10-viewport-bug-workaround.js"></script>
<?php if (isset($scripts) && is_array($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="<?=$script?>"></script>
    <?php endforeach; ?>
<?php endif;?>
</body>
</html>
