<?php require "_parts/head.php"; ?>

<main>
    <h1>
        Welcome to Vocabulate
    </h1>

    <ul class="stack-list">
        <?php for ($i=0; $i < 10; $i++): ?>
        <li>
            <div class="stack" data-stack-index="<?php echo $i ?>">
                <div class="stack__card  stack__card--bottom"></div>
                <div class="stack__card  stack__card--middle"></div>

                <div class="stack__card  stack__card--top">
                    <h3>
                        name of the stack
                    </h3>
                </div>
            </div>
        </li>
        <?php endfor ?>
    </ul>

    <script>
    const stacks = document.querySelectorAll("[data-stack-index]");
    const generateRandomRotation = () => {
        return {
            bottom: (Math.random() * 5 - 6).toFixed(2),
            middle: (Math.random() * 5 + 1).toFixed(2)
        };
    };

    for (const stack of Array.from(stacks)) {
        const { bottom, middle } = generateRandomRotation();
        stack.style.setProperty("--bottom-rotate", `${bottom}deg`);
        stack.style.setProperty("--middle-rotate", `${middle}deg`);
    }


    </script>
</main>

<?php require  "_parts/footer.php"; ?>
