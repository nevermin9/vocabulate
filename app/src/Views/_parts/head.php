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

        <script blocking="render">
        window.addEventListener('pagereveal', (e) => {
            console.log({ pagereveal: e });
            console.log({ pagerevealV: e.viewTransition });
        });
        </script>

        <script src="/js/main.js" async type="module"></script>
    </head>

    <body>
        <?php require "app-overlay.php"; ?>

        <header>


            <div>
                <a href="/logout">
                    Logout
                </a>
            </div>
        </header>

