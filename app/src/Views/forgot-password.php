<main class="auth-page">
    <h1>
        Vocabulate
    </h1>

    <div class="auth-page__auth-box auth-box">
        <h3 class="auth-page__title">
            Forgot password
        </h3>

        <form
            class="auth-form"
            action="/forgot-password"
            method="post"
        >
            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Email*
                </span>

                <input 
                    class="app-field__input <?php echo ( $this->params['errors']['email'] ?? null ? 'app-field__input--invalid' : '' ) ?>"
                    type="email"
                    name="email"
                    maxlength="100"
                    required
                    placeholder="example@domain.com"
                />

                <?php if (( $this->params['errors']['email'] ?? null ) !== null ): ?>
                <span class="app-field__error-txt">
                    <?php echo htmlspecialchars($this->params['errors']['email'][0]); ?>
                </span>
                <?php endif; ?>
            </label>

            <input type="hidden" name="csrf_token" value="<?php echo $this->params['csrf_token'] ?>" />

            <button 
                class="auth-form__btn  app-btn app-btn--primary"
                value="forgotpass"
            >
                Send request
            </button>
        </form>

        <p class="auth-page__sub-txt">
            <a
                href="/login"
                class="app-link"
            >
                Back to login
            </a>
        </p>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>
