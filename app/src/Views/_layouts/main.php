<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <style>
        <?php require __DIR__ . "/../../../resources/css/index.php" ?>
        </style>

        <title>
            <?php echo $this->params['documentTitle'] ?? 'Vocabulate' ?>
        </title>

        <script src="/js/main.js" defer type="module"></script>
    </head>

    <body>
        <?php
            if (isset($_SESSION['user-id'])) {
                require dirname(__DIR__) . "/_parts/app-overlay.php"; 
            }
        ?>

        <header>
            <div>
            </div>
        </header>

        {{content}}

        <?php require dirname(__DIR__) . "/_parts/svg-sprite.html"; ?>
    </body>
</html>

