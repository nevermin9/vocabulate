
<?php require "_parts/head.php"; ?>

<main>
    <form
        class="auth-form"
    >
        <label>
            Email

            <input 
                type="email"
                name="email"
                maxlength="100"
            />
        </label>

        <label>
            Password

            <input 
                type="password"
                name="password"
                maxlength="50"
            />
        </label>

        <button value="login">
            Login
        </button>
    </form>

    <div>
        <a href="/registration">
            Register
        </a>
    </div>
</main>

<?php require  "_parts/footer.php"; ?>
