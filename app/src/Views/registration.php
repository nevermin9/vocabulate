
<?php require "_parts/head.php"; ?>

<main>
    <form method="POST" action="/register">
        <!-- <label> -->
        <!--     Username -->
        <!---->
        <!--     <input  -->
        <!--         type="text" -->
        <!--         name="username" -->
        <!--         maxlength="50" -->
        <!--     /> -->
        <!-- </label> -->

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
</main>

<?php require  "_parts/footer.php"; ?>
