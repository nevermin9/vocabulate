
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
                    maxlength="50"
                    required
                />
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Repeat password*
                </span>

                <input 
                    class="app-field__input"
                    type="password"
                    name="repeat_password"
                    maxlength="50"
                    required
                />
            </label>

            <button 
                class="auth-form__btn  app-btn app-btn--primary"
                value="signup"
            >
                Sign up
            </button>
        </form>

        <p class="auth-page__sub-txt">
            Already have an account?

            <a 
                class="app-link"
                href="/login"
            >
                Sign in here.
            </a>
        </p>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>

<?php require  "_parts/footer.php"; ?>
