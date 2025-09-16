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
        <header>
            <fieldset>
                <!-- <label> -->
                <!--     system -->
                <!--     <input id="system-theme-radio" type="radio" name="theme" value="system" checked /> -->
                <!-- </label> -->

                <label>
                    dark
                    <input id="dark-theme-radio" type="radio" name="theme" value="dark" />
                </label>

                <label>
                    light
                    <input id="light-theme-radio" type="radio" name="theme" value="light" />
                </label>
            </fieldset>

            <script>
            // set on load
            const THEME_KEY = "app-theme";
            const themeFromStorage = localStorage.getItem(THEME_KEY);
            if (themeFromStorage) {
                const radio = document.getElementById(`${themeFromStorage}-theme-radio`);
                radio.checked = true;
            }
            // remember
            const radios = document.querySelectorAll("input[name='theme']");
            const createListener = (theme) => (e) => {
                if (e.target.checked) {
                    localStorage.setItem(THEME_KEY, theme);
                }
            };
            Array.from(radios).forEach(r => {
                if (r.id.startsWith("light")) {
                    r.addEventListener("change", createListener("light"));
                } else {
                    r.addEventListener("change", createListener("dark"));
                }
            });
            </script>

            <div>
                <a href="/logout">
                    Logout
                </a>
            </div>
        </header>

