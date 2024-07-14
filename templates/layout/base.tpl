<html lang="en">
<head>
    <title><?= $this->e($title) ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/app.css"/>
</head>
<body>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/about">About</a></li>
        <li><a href="/hello">Hello</a></li>
    </ul>
</nav>
<main>
    <?= $this->section('content') ?>
</main>
<footer>
    <?= date('Y') ?>
</footer>
</body>
</html>
