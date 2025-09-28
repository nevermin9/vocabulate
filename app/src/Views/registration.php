
<?php require "_parts/head.php"; ?>

<main class="auth-page">
    <h1 class="">
        Vocabulate
    </h1>

    <div
        class="auth-page__auth-box auth-box"
    >
        <h3 class="auth-page__title">
            Sign Up
        </h3>

        <form
            class="auth-form"
            method="POST"
            action="/register"
        >
            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Email*
                </span>

                <input 
                    class="app-field__input"
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($this->params['email'])  ?>"
                    maxlength="100"
                    required
                />
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Password*
                </span>

                <input 
                    class="app-field__input"
                    type="password"
                    name="password"
                    minlength="8"
                    maxlength="50"
                    value="<?php echo htmlspecialchars($this->params['password'])  ?>"
                    required
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" 
                    title="Password must contain: at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long."
                />

                <ul class="auth-form__field-rules">
                    <?php foreach ($this->params['password_rules'] as $ruleId => $rule): ?>
                    <?php if (array_key_exists($ruleId, $this->params['errors']['password'] ?? [])): ?>
                    <li class="auth-form__field-rule  auth-form__field-rule--invalid">
                    <?php else: ?>
                    <li class="auth-form__field-rule">
                    <?php endif; ?>
                        <span>
                            <?php echo htmlspecialchars($rule); ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Confirm password*
                </span>

                <input 
                    class="app-field__input"
                    type="password"
                    name="confirm_password"
                    value="<?php echo htmlspecialchars($this->params['confirm_password'])  ?>"
                    minlength="8"
                    maxlength="50"
                    required
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" 
                    title="Password must contain: at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long."
                />
            </label>

            <label class="app-checkbox">
                <input 
                    id="show-pass-checkbox"
                    class="app-checkbox__input"
                    type="checkbox"
                    title="show passwords"
                />

                <div class="app-checkbox__custom">
                    <svg 
                        class="app-checkbox__svg"
                        viewBox="0 0 24 24"
                    >
                        <path d="M5 12l5 5l10-10"></path>
                    </svg>
                </div>

                <span class="app-checkbox__label">Show password </span>
            </label>

            <input type="hidden" name="_token" value="<?php echo $this->params['token'] ?>" />

            <button 
                class="auth-form__btn  app-btn app-btn--primary"
                value="signup"
            >
                Sign Up
            </button>
        </form>

        <p class="auth-page__sub-txt">
            Already have an account?

            <a 
                class="app-link"
                href="/login"
            >
                Log in here.
            </a>
        </p>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // show/hide password
    const passwordInputs = Array.from(document.querySelectorAll("input[name*='password']"));
    const showPassInput = document.getElementById("show-pass-checkbox");
    const changeTypePassInputs = (type) => {
        for (const input of passwordInputs) {
            input.type = type;
        }
    };
    showPassInput.addEventListener("change", (e) => {
        if (e.target.checked) {
            changeTypePassInputs('text');
            return;
        }
        changeTypePassInputs('password');
    });


    // remove invalid class from rules
    const ruleElements = Array.from(document.querySelectorAll('.auth-form__field-rule'));
    const removeInvalidClass = () => {
        for (const el of ruleElements) {
            el.classList.remove('auth-form__field-rule--invalid');
        }
    };
    for (const input of passwordInputs) {
        input.addEventListener("input", removeInvalidClass, { once: true });
    }
});
</script>

<?php require  "_parts/footer.php"; ?>
