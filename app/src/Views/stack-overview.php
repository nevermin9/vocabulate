
<main>
    <h1>
        <a href="/">to main</a>

        name of the stack
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
        <?php foreach ($this->params['flashcards'] as $i => $flashcard): ?>
        <li>
            <div class="stack" data-stack-index="<?php echo $i ?>">
                <div class="stack__card  stack__card--bottom"></div>
                <div class="stack__card  stack__card--middle"></div>

                <div class="stack__card  stack__card--top">
                    <h3>
                        <?php echo htmlspecialchars($flashcard['word']); ?>
                    </h3>

                    <h4>
                        <?php echo htmlspecialchars($flashcard['translation']); ?>
                    </h4>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

    <dialog
        data-dialog
    >
        <form
            class="flashcard-form"
            method="post"
            action="/stack/<?php echo htmlspecialchars($this->params['stackId']) ?>/add-flashcard"
        >
            <label>
                Word*

                <input type="text" name="word" required />
            </label>

            <label>
                Translation*

                <input type="text" name="translation" required />
            </label>

            <label>
                Usage example

                <input type="text" name="example-usage" />
            </label>

            <label>
                Translation of usage example

                <input type="text" name="example-usage-translation" />
            </label>

            <button formmethod="dialog" formnovalidate>
                cancel
            </button>

            <button>
                submit
            </button>
        </form>
    </dialog>
</main>

<script>
const dialogBtn = document.querySelector("[data-open-dialog]");
const dialog = document.querySelector("dialog[data-dialog]");
dialogBtn.addEventListener("click", () => {
    dialog.showModal();
});

dialog.addEventListener("click", (e) => {
    if (e.target.matches("dialog[data-dialog]")) {
        dialog.close();
    }
});
</script>
