
<main class="auth-page">
    <h1 class="">
        Vocabulate
    </h1>

    <div class="auth-page__auth-box auth-box">
        <h3 class="auth-page__title">
            Log In
        </h3>

        <form
            class="auth-form"
            action="/login"
            method="post"
        >
            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Email
                </span>

                <input 
                    class="app-field__input  <?php echo ( $this->params['errors']['email'] ?? null ? 'app-field__input--invalid' : '' ) ?>"
                    type="email"
                    name="email"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($this->params['email'])  ?>"
                    required
                    placeholder="example@domain.com"
                />

                <?php if (( $this->params['errors']['email'] ?? null ) !== null ): ?>
                <span class="app-field__error-txt">
                    <?php echo htmlspecialchars($this->params['errors']['email']); ?>
                </span>
                <?php endif; ?>
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Password
                </span>

                <input 
                    class="app-field__input  <?php echo ( ($this->params['errors']['email'] ?? null ) !== null ? 'app-field__input--invalid' : '' ) ?>"
                    id="password-input"
                    type="password"
                    name="password"
                    value="<?php echo htmlspecialchars($this->params['password'])  ?>"
                    maxlength="50"
                    required
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
                value="login"
            >
                Log In
            </button>
        </form>

        <p class="auth-page__sub-txt">
            Don't have an account yet?
            You can

            <a 
                class="app-link"
                href="/registration"
            >
                sign up here.
            </a>

            <a 
                class="app-link"
                href="/forgot-password"
            >
                Forgot password?
            </a>
        </p>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const CLASS = 'app-field__input--invalid';
    const invalidInputs = document.querySelectorAll(`.${CLASS}`);

    for (const input of Array.from(invalidInputs)) {
        input.addEventListener('input', function() {
            this.classList.remove(CLASS);

            const errorMessage = this.nextElementSibling;

            if (errorMessage && errorMessage.classList.contains('app-field__error-txt')) {
                errorMessage.style.display = 'none';
            }
        });
    }

    const showPassInput = document.getElementById("show-pass-checkbox");
    const passwordInput = document.getElementById("password-input");
    showPassInput.addEventListener("change", (e) => {
        if (e.target.checked) {
            passwordInput.type = "text";
            return;
        }
        passwordInput.type = "password";
    });
});
</script>

