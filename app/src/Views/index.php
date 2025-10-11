<main>
    <ul class="stack-list">
        <li>
            <div class="stack">
                <div class="stack__card  stack__card--blank">
                    <button 
                        class="stack__add-btn"
                        data-open-dialog
                        type="button"
                    >
                        <svg class="icon" style="--size: 36px;">
                            <use href="#plus-thik"></use>
                        </svg>
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
                    <a href="/stack/<?php echo htmlspecialchars($stack['id']) ?>">
                        <?php echo htmlspecialchars($stack['name']) ?>
                    </a>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

    <dialog 
        class="app-dialog"
        data-dialog
    >
        <div class="app-dialog__inner">
            <form 
                method="post"
                action="/stack/create"
                class="create-stack-form"
            >
                <label class="create-stack-form__field app-field">
                    <span class="app-field__label">
                        Stack's name*
                    </span>

                    <input 
                        class="app-field__input"
                        type="text"
                        name="stack-name"
                        required 
                        maxlength="99"
                    />
                </label>

                <label class="create-stack-form__field  app-field">
                    <span class="app-field__label">
                        Stack's language*
                    </span>

                    <div
                        class="app-field__select-box"
                    >
                        <select 
                            class="app-field__select"
                            name="stack-language"
                            required
                        >
                            <option
                                value=""
                                disabled
                                selected
                            >Please, select language</option>
                            <?php foreach ($this->params['languages'] as $i => $lang): ?>
                            <option value="<?php echo htmlspecialchars($lang->code) ?>">
                                <?php echo htmlspecialchars($lang->name) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </label>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($this->params['csrf_token']) ?>" />

                <div class="create-stack-form__btns-room">
                    <button class="app-btn  app-btn--primary">
                        Create
                    </button>

                    <button 
                        class="app-btn app-btn--secondary"
                        formmethod="dialog"
                        formnovalidate
                    >
                        Cancel
                    </button>
                </div>

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
