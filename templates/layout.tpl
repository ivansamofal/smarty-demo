<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}Smarty Demo Blog{/block}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="site-header__logo" href="/">Smarty Demo Blog</a>
        </div>
    </header>
    <main class="container site-main">
        {block name="content"}{/block}
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; {$smarty.now|date_format:"%Y"} Smarty Demo Blog</p>
        </div>
    </footer>
</body>
</html>
