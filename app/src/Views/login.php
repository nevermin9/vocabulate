
<?php require "_parts/head.php"; ?>

<main class="login-page">
    <h1 class="">
        Vocabulate
    </h1>

    <div class="login-page__login-box login-box">
        <h3 class="login-page__title">
            Sign In
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
                    class="app-field__input"
                    type="email"
                    name="email"
                    maxlength="100"
                    required
                />
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Password
                </span>

                <input 
                    class="app-field__input"
                    type="password"
                    name="password"
                    maxlength="50"
                    required
                />
            </label>

            <button
                class="auth-form__btn  app-btn app-btn--primary"
                value="login"
            >
                Login
            </button>
        </form>

        <p class="login-page__sub-txt">
            Don't have an account yet?
            You can

            <a 
                class="app-link"
                href="/registration"
            >
                sign up here.
            </a>
        </p>
    </div>

    <aside class="logoin-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>


<?php require  "_parts/footer.php"; ?>
