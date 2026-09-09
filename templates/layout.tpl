<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}Smarty Demo Blog{/block}</title>
</head>
<body>
    <header>
        <a href="/">Smarty Demo Blog</a>
    </header>
    <main>
        {block name="content"}{/block}
    </main>
    <footer>
        <p>&copy; {$smarty.now|date_format:"%Y"} Smarty Demo Blog</p>
    </footer>
</body>
</html>
