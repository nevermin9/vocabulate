
    <script>
    window.addEventListener('pageswap', (e) => {
        if (!e.viewTransition) {
            return;
        }
        console.log({ pageswap: e });
        console.log({ pageswapV: e.viewTransition });
    });

    </script>

    <?php require "svg-sprite.html"; ?>
    </body>
</html>
