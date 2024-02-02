<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $this->title; ?></title>
    <link rel="stylesheet" href="skin/screen.css">
</head>
<body>
    <nav class="menu">
        <ul>
            <?php foreach ($this->menu as $url => $text) : ?>
                <li><a href="<?php echo $url; ?>"><?php echo $text; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <main>
        <div class="feedback">
            <?php echo $this->feedback; ?>
        </div>
        <h1 class="titre">
            <?php echo $this->title; ?>
        </h1>
        <div class="contenu">
            <?php echo $this->content; ?>
        </div>
    </main>
</body>
</html>