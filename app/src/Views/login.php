
<?php require "_parts/head.php"; ?>

<main>
    <h1 class="">
        Vocabulate
    </h1>

    <div class="login-box">
        <h3>
            Sign in
        </h3>

        <form
            class="auth-form"
            action="/login"
            method="post"
        >
            <label class="app-field">
                <span class="app-field__label">
                    Email
                </span>

                <input 
                    class="app-field__input"
                    type="email"
                    name="email"
                    maxlength="100"
                />
            </label>

            <label class="app-field">
                <span class="app-field__label">
                    Password
                </span>

                <input 
                    class="app-field__input"
                    type="password"
                    name="password"
                    maxlength="50"
                />
            </label>

            <button
                class="app-btn app-btn--primary"
                value="login"
            >
                Login
            </button>
        </form>

        <p>
            Don't have an account yet?
            You can

            <a href="/registration">
                sign up here.
            </a>
        </p>
    </div>

    <aside class="theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>


<?php require  "_parts/footer.php"; ?>
