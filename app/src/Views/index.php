<?php require "_parts/head.php"; ?>

<main>
    <h1>
        Welcome to Vocabulate
    </h1>

    <ul class="stack-list">
        <li>
            <div class="stack">
                <div class="stack__card  stack__card--top">
                    <button 
                        data-open-dialog
                        type="button"
                    >
                        +
                    </button>
                </div>
            </div>
        </li>
        <?php foreach ($this->params['stacks'] as $i => $stack): ?>
        <li>
            <div class="stack" data-stack-index="<?php echo $i ?>">
                <div class="stack__card  stack__card--bottom"></div>
                <div class="stack__card  stack__card--middle"></div>

                <div class="stack__card  stack__card--top">
                    <a href="/stack-overview">
                        <?php echo htmlspecialchars($stack['name']) ?>
                    </a>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

    <dialog data-dialog>
        <div>
            <form class="create-stack-form">
                <label>
                    Stack's name
                    <input type="text" name="stack-name" required maxlength="99" />
                </label>

                <label>
                    Words language

                    <select name="stack-language" required>
                        <option
                            value=""
                            disabled
                            selected
                        >Please, select language</option>
                        <?php foreach ($this->params['languages'] as $i => $lang): ?>
                        <option value="<?php echo htmlspecialchars($lang['code']) ?>">
                            <?php echo htmlspecialchars($lang['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <button formmethod="dialog" formnovalidate>
                    cancel
                </button>

                <button>
                    submit
                </button>
            </form>
        </div>
    </dialog>

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

    <script>
    const openDialogBtn = document.querySelector("[data-open-dialog]");
    const dialog = document.querySelector("[data-dialog]");

    openDialogBtn.addEventListener("click", () => {
        dialog.showModal();
    });

    dialog.addEventListener("click", (e) => {
        if (e.target.matches("dialog[data-dialog]")) {
            dialog.close();
        }
    });
    </script>
</main>

<?php require  "_parts/footer.php"; ?>
