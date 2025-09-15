
<?php require "_parts/head.php"; ?>

<main>
    <form
        class="auth-form"
        method="POST"
        action="/register"
    >
        <label>
            Email*

            <input 
                type="email"
                name="email"
                maxlength="100"
                required
            />
        </label>

        <label>
            Password*

            <input 
                type="password"
                name="password"
                maxlength="50"
                required
            />
        </label>

        <label>
            Repeat password

            <input 
                type="password"
                name="repeat_password"
                maxlength="50"
                required
            />
        </label>

        <button value="Register">
            Register
        </button>
    </form>

    <div>
        <a href="/login">
            Login
        </a>
    </div>
</main>

<?php require  "_parts/footer.php"; ?>
