
<?php require "_parts/head.php"; ?>

<main>
    <form>
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
</main>

<?php require  "_parts/footer.php"; ?>
